<?php

namespace App\Http\Controllers\Swr\Customer;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewReportEmailJob;
use App\Models\Swr\SecondmentWeeklyReport;
use App\Models\Swr\SecondmentWeeklyReportDocument;
use App\Models\Swr\Event;
use App\Models\Swr\Venue;
use App\Models\Swr\FunctionalArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SecondmentWeeklyReportExport;

class SecondmentWeeklyReportController extends Controller
{
    /**
     * Display list of all secondment weekly reports for the current user
     */
    public function index()
    {
        $events = Event::all();
        return view('swr.customer.report.list', compact('events'));
    }

    /**
     * Get venues for a specific event (AJAX endpoint)
     */
    public function byEvent($event_id)
    {
        Log::info('byEvent: Fetching venues for event ID: ' . $event_id);

        $venues = auth()->user()->venues()
            ->whereIn('venues.id', function ($q) use ($event_id) {
                $q->select('venue_id')
                    ->from('venue_event')
                    ->where('event_id', $event_id);
            })
            ->select('venues.id', 'venues.title')
            ->orderBy('venues.title')
            ->get();

        return response()->json($venues);
    }

        public function pickEvent(Request $request)
    {
        // $events = Event::all();
        // $this->switch($request->event_id);
        // return view('vapp.admin.booking.pick', compact('events'));
        if ($request->event_id) {
            // appLog('Event ID: ' . $request->event_id);
            if (Event::findOrFail($request->event_id) && !session()->has('EVENT_ID')) {
                // appLog('Inside if statement Event ID: ' . $request->event_id);

                session()->put('EVENT_ID', $request->event_id);
                session()->put('VENUE_ID', $request->venue_id);
                // appLog('session EVENT_ID: ' . session()->get('EVENT_ID'));
                // appLog('before redirect');
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('swr.report')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            }
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
        // appLog('event_id is null');
        return redirect()->route('swr.report')->with('error', 'Event not found.');
        // }
    }
    
    /**
     * Show the form for creating a new secondment weekly report
     */
    public function create()
    {
        $event = Event::findOrFail(session()->get('EVENT_ID'));
        $user = Auth::user();

        // Get venues assigned to this user and event
        $venues = $user->venues()
            ->whereIn('venues.id', function ($q) use ($event) {
                $q->select('venue_id')
                    ->from('venue_event')
                    ->where('event_id', $event->id);
            })->get();

        // Auto-select venue if user has only one venue assigned
        $defaultVenueId = $venues->count() === 1 ? $venues->first()->id : old('venue_id');

        // Fetch functional areas from database
        $functionalAreas = FunctionalArea::orderBy('title')->get();

        // Support types for HR/Wellbeing section
        $supportTypes = [
            'Workload' => 'Workload',
            'Accommodation' => 'Accommodation',
            'Logistics' => 'Logistics',
            'Health' => 'Health',
            'Other' => 'Other (please specify)',
        ];

        return view('swr.customer.report.create', compact(
            'event',
            'venues',
            'defaultVenueId',
            'user',
            'functionalAreas',
            'supportTypes'
        ));
    }

    public function GenerateFileName(SecondmentWeeklyReport $op, $seq = 0, $extension = 'pdf')
    {

        // ---------- Build automated filename ----------
        // Pick the right date field from your model (adjust if needed)
        $date = $op->reporting_week ?? $op->created_at;
        $dateStr = Carbon::parse($date)->format('Ymd');

        // Stadium / venue code (adjust field names)
        $stadiumCode = $op->venue?->short_name ?? 'Venue';

        // Report number (adjust field names)
        $serialNumber = getNumber($op->reference_number ?? '0');

        if ($seq > 0) {
            $serialNumber .= '-' . $seq;
        }

        // Ensure extension is not empty
        $extension = !empty($extension) ? $extension : 'pdf';

        // Example: 20260216_974_Match Report #1_Kuwait-Qatar.pdf
        $filename = "{$stadiumCode}-{$dateStr}-{$serialNumber}" . ".{$extension}";

        // sanitize filename for Windows/Linux
        $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);
        $filename = preg_replace('/\s+/', ' ', trim($filename));
        // ---------------------------------------------

        return $filename;
    }

    /**
     * Store a newly created secondment weekly report
     */
    public function store(Request $request)
    {
        Log::info('Storing Secondment Weekly Report', ['user_id' => Auth::id()]);
        Log::info('Request data', $request->all());
        $user = Auth::user();

        $validated = $request->validate([
            'reporting_week' => ['required', 'date_format:d/m/Y'],
            'venue_id' => 'required|exists:venues,id',

            // Basic Information
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            // 'city' => 'required|string|max:255',

            // Weekly Activities
            'main_activities' => 'required|string|max:5000',

            // Gained Experience
            'experience_gained' => 'required|string|max:5000',

            // Innovation
            'innovation_description' => 'required|string|max:5000',
            'innovation_functional_areas' => 'nullable|array',
            'innovation_functional_areas.*' => 'exists:functional_areas,id',
            'innovation_other_area' => 'nullable|string|max:500',

            // Challenges
            'challenges_description' => 'required|string|max:5000',
            'challenges_resolved' => 'required|in:yes,no',
            'challenges_functional_areas' => 'nullable|array',
            'challenges_functional_areas.*' => 'exists:functional_areas,id',
            'challenges_other_area' => 'nullable|string|max:500',

            // Photos
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',

            // Value for Qatar
            'value_for_qatar' => 'required|in:yes,no',
            'value_for_qatar_type' => 'nullable|in:Must Have,Good to Have,Requires further assessment',
            'value_for_qatar_description' => 'nullable|string|max:5000',

            // HR / Wellbeing
            'wellbeing_status' => 'required|in:Good,Moderate,Challenging',
            'needs_support' => 'required|in:yes,no',
            'support_types' => 'nullable|array',
            'support_types.*' => 'string',
            'support_other_description' => 'nullable|string|max:500',

            // Additional Comment
            'additional_comment' => 'nullable|string|max:2000',
        ]);

        $report = null;
        $seq = 0;

        DB::transaction(function () use ($request, $validated, $user, &$report, &$seq) {
            // Debug logging
            Log::info('SWR Store - Functional Areas', [
                'challenges_functional_areas' => $validated['challenges_functional_areas'] ?? [],
                'innovation_functional_areas' => $validated['innovation_functional_areas'] ?? [],
                'innovation_other_area' => $validated['innovation_other_area'] ?? 'NOT PROVIDED',
                'challenges_other_area' => $validated['challenges_other_area'] ?? 'NOT PROVIDED',
            ]);

            $seq = nextSequence('swr');

            // Create the report
            $report = SecondmentWeeklyReport::create([
                'reference_number' => 'SWR-' . date('Y') . '-' . get_current_event_id() . '-' . $validated['venue_id'] . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'event_id'      => get_current_event_id(),
                'venue_id' => $validated['venue_id'],
                'reporting_week' => $validated['reporting_week']
                    ? Carbon::createFromFormat('d/m/Y', $validated['reporting_week'])->toDateString()
                    : null,
                'name' => $validated['name'] ?? $user->name,
                'role' => $validated['role'],
                // 'city' => $validated['city'],
                'main_activities' => $validated['main_activities'],
                'experience_gained' => $validated['experience_gained'],
                'innovation_description' => $validated['innovation_description'],
                'innovation_other_area' => $validated['innovation_other_area'] ?? null,
                'challenges_description' => $validated['challenges_description'],
                'challenges_other_area' => $validated['challenges_other_area'] ?? null,
                'challenges_resolved' => $validated['challenges_resolved'] === 'yes',
                'value_for_qatar' => $validated['value_for_qatar'] === 'yes',
                'value_for_qatar_type' => $validated['value_for_qatar_type'] ?? null,
                'value_for_qatar_description' => $validated['value_for_qatar_description'] ?? null,
                'wellbeing_status' => $validated['wellbeing_status'],
                'needs_support' => $validated['needs_support'] === 'yes',
                'support_types' => !empty($validated['support_types']) ? $validated['support_types'] : null,
                'support_other_description' => $validated['support_other_description'] ?? null,
                'additional_comment' => $validated['additional_comment'] ?? null,
                'status' => 'draft',
            ]);

            // Save innovation functional areas to pivot table
            if (!empty($validated['innovation_functional_areas'])) {
                foreach ($validated['innovation_functional_areas'] as $area_id) {
                    \App\Models\Swr\SecondmentWeeklyReportInnovationFunctionalArea::create([
                        'secondment_weekly_report_id' => $report->id,
                        'functional_area_id' => $area_id,
                    ]);
                }
            }

            // Save challenge functional areas to pivot table
            if (!empty($validated['challenges_functional_areas'])) {
                Log::info('Saving challenge functional areas', ['count' => count($validated['challenges_functional_areas'])]);
                foreach ($validated['challenges_functional_areas'] as $area_id) {
                    Log::info('Creating challenge functional area record', [
                        'report_id' => $report->id,
                        'functional_area_id' => $area_id,
                        'type' => gettype($area_id),
                    ]);
                    \App\Models\Swr\SecondmentWeeklyReportChallengeFunctionalArea::create([
                        'secondment_weekly_report_id' => $report->id,
                        'functional_area_id' => $area_id,
                    ]);
                }
            } else {
                Log::info('No challenge functional areas to save');
            }

            // Store uploaded photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $dir = "swr/{$report->id}/photos";
                    $filename = uniqid() . '_' . time() . '.' . $photo->getClientOriginalExtension();

                    // Use getPathname() to avoid getRealPath() returning false on Windows temp files
                    $stream = fopen($photo->getPathname(), 'r');
                    if ($stream === false) {
                        Log::error("Cannot open photo file for SWR report ID: {$report->id}");
                        continue;
                    }
                    Storage::disk('local')->writeStream("{$dir}/{$filename}", $stream);
                    fclose($stream);
                    $path = "{$dir}/{$filename}";

                    SecondmentWeeklyReportDocument::create([
                        'secondment_weekly_report_id' => $report->id,
                        'original_name' => $photo->getClientOriginalName(),
                        'file_name' => $filename,
                        'file_path' => $path,
                        'disk' => 'local',
                        'mime_type' => $photo->getClientMimeType(),
                        'file_size' => $photo->getSize(),
                        'document_type' => 'photo',
                        'related_section' => 'general',
                        'uploaded_by_user' => $user->name,
                        'description' => 'Photo ' . ($index + 1),
                    ]);
                }
            }

            Log::info('Secondment Weekly Report created', ['report_id' => $report->id, 'user_id' => $user->id]);

            $save_pass_pdf = $this->save_pass_pdf($report, $seq);

            if ($save_pass_pdf) {
                $details = [
                    'email' => [$user->email],
                    'venue' => $report->venue->title,
                    'event' => $report->event->name,
                    'reference_number' => $report->reference_number,
                    'report_date' => \Carbon\Carbon::parse($report->reporting_week)->format('l jS \of F Y'),
                    'filename' => $this->GenerateFileName($report),
                    // 'filename' => $report->venue?->short_name . '_' . $report->reference_number . '.pdf',
                ];

                // Log::info('BookingController::store details: ' . json_encode($details));
                if (config('settings.send_notifications')) {
                    SendNewReportEmailJob::dispatch($details);
                }
            }
        });

        return redirect()->route('swr.report')
            ->with(['type' => 'success', 'message' => 'Report created successfully!']);
    }

    public function save_pass_pdf($swr, $seq)
    {
        // set_time_limit(300);

        $qr_code = null;
        
        // Reload with documents for PDF generation
        $swr->load('documents');

        $data = [
            'report' => $swr,
            'qr_code' => $qr_code,
        ];

        $filename = $this->GenerateFileName($swr);

        $data['css'] = public_path('assets/css/invoice.css');
        $pdf = Pdf::loadView('swr.admin.report.pdf', $data);
        Storage::disk('private')->put('swr/pdf-exports/' . $filename, $pdf->output());
        // Storage::disk('private')->put('swr/pdf-exports/' . $swr->reference_number . '.pdf', $pdf->output());

        return 1;
    }

    /**
     * Display the list of reports with filtering and searching (AJAX)
     */
    public function list(Request $request)
    {
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $limit = min($request->input('limit', 10), 100);

        $query = SecondmentWeeklyReport::forUser(Auth::id())
            ->with(['event', 'venue', 'user', 'documents'])
            ->orderBy($sort, $order);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('main_activities', 'like', '%' . $search . '%')
                    ->orWhere('challenges_description', 'like', '%' . $search . '%')
                    ->orWhere('innovation_description', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $total = $query->count();
        $reports = $query->paginate($limit);

        $rows = $reports->through(function ($report) {
            $statusBadge = '<span class="badge bg-' . $this->getStatusColor($report->status) . '">' . $report->getStatusLabel() . '</span>';

            $actions = '<div class="btn-group" role="group">';
            $actions .= '<a href="' . route('swr.report.pdf', $report->id) . '" class="btn btn-sm btn-danger" title="Generate PDF" target="_blank"><i class="fas fa-file-pdf"></i></a>';

            if ($report->canEdit()) {
                $actions .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger delete-report" data-id="' . $report->id . '" data-table="report_table" title="Delete"><i class="fas fa-trash"></i></a>';
            }

            if ($report->documents->count() > 0) {
                $actions .= '<a href="' . route('swr.report.gallery', $report->id) . '" class="btn btn-sm btn-secondary" title="Photos (' . $report->documents->count() . ')"><i class="fas fa-images"></i></a>';
            }

            $actions .= '</div>';

            if ($report->documents->isNotEmpty()) {
                $link = '<a href="' . route('swr.report.gallery', $report->id) . '" target="_blank" class="position-relative d-inline-block">
                    <i class="fa-solid fa-image fa-2x text-success"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                        ' . $report->documents->count() . '
                    </span>
                </a>';
            } else {
                $link = '<i class="fa-solid fa-image fa-2x text-muted"></i>';
            }

            return [
                'id' => $report->id,
                'ref_number' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . '<span class="badge rounded-pill ref-badge text-bg-light border px-3 py-2 fw-8 fw-semibold">' . $report->reference_number . '</span>' . '</div>',
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $link . '</div>',
                'name' => $report->name ?? $report->user?->name ?? 'N/A',
                'role' => $report->role ?? 'N/A',
                'city' => $report->city ?? 'N/A',
                'venue_id' => $report->venue?->title ?? 'N/A',
                'reporting_week' => format_date($report->reporting_week ?? $report->created_at->toDateString()),
                'event_id' => $report->event?->id ?? 'N/A',
                'main_activities' => Str::limit($report->main_activities ?? 'N/A', 50),
                'experience_gained' => Str::limit($report->experience_gained ?? 'N/A', 50),
                'innovation_description' => Str::limit($report->innovation_description ?? 'N/A', 50),
                'innovation_other_area' => $report->innovation_other_area ?? 'N/A',
                'challenges_description' => Str::limit($report->challenges_description ?? 'N/A', 50),
                'challenges_other_area' => $report->challenges_other_area ?? 'N/A',
                'challenges_resolved' => $report->challenges_resolved ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>',
                'wellbeing_status' => $report->getWellbeingEmoji() . ' ' . ($report->wellbeing_status ?? 'N/A'),
                'needs_support' => $report->needs_support ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-secondary">No</span>',
                'value_for_qatar' => $report->value_for_qatar ? '<span class="badge bg-info">Yes</span>' : '<span class="badge bg-secondary">No</span>',
                'status' => $statusBadge,
                'created_at' => format_date($report->created_at, 'Y-m-d H:i'),
                'updated_at' => format_date($report->updated_at, 'Y-m-d H:i'),
                'action' => $actions,
            ];
        });

        return response()->json([
            'rows' => $rows->items(),
            'total' => $total,
        ]);
    }

    /**
     * Export filtered reports to Excel
     */
    public function export(Request $request)
    {
        Log::info('Exporting Secondment Weekly Reports for user: ' . auth()->user()->name);
        Log::info($request->all());

        $filters = $request->only([
            'export_event_filter',
            'export_venue_filter',
            'export_date_range_filter',
        ]);

        Log::info('Filters applied: ' . json_encode($filters));

        return Excel::download(
            new SecondmentWeeklyReportExport($filters), 
            'secondment_weekly_reports_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Display details of a specific report
     */
    public function detail($id)
    {
        $report = SecondmentWeeklyReport::with('innovationFunctionalAreas.functionalArea', 'challengeFunctionalAreas.functionalArea')->findOrFail($id);
        $this->authorize('view', $report);

        return view('swr.customer.report.detail', compact('report'));
    }

    /**
     * Display gallery of photos attached to a report
     */
    public function gallery($id)
    {
        $report = SecondmentWeeklyReport::with('documents')->findOrFail($id);
        $this->authorize('view', $report);

        return view('swr.customer.report.gallery', compact('report'));
    }

    /**
     * Delete a secondment weekly report
     */
    public function destroy($id)
    {
        $report = SecondmentWeeklyReport::findOrFail($id);
        $this->authorize('delete', $report);

        DB::transaction(function () use ($report) {
            // Delete all attached documents
            foreach ($report->documents as $doc) {
                Storage::disk($doc->disk)->delete($doc->file_path);
                $doc->delete();
            }

            // Delete the report
            $report->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully!',
        ]);
    }

    /**
     * Show the form for editing a report
     */
    public function edit($id)
    {
        $report = SecondmentWeeklyReport::with('innovationFunctionalAreas.functionalArea', 'challengeFunctionalAreas.functionalArea')->findOrFail($id);
        $this->authorize('update', $report);

        $events = Event::all();
        $user = Auth::user();

        // Get venues for the current event
        $venues = $user->venues()
            ->whereIn('venues.id', function ($q) use ($report) {
                $q->select('venue_id')
                    ->from('venue_event')
                    ->where('event_id', $report->event_id);
            })->get();

        // Fetch functional areas from database
        $functionalAreas = FunctionalArea::orderBy('name')->get();

        return view('swr.customer.report.edit', compact('report', 'events', 'venues', 'user', 'functionalAreas'));
    }

    /**
     * Update a secondment weekly report
     */
    // public function update(Request $request, $id)
    // {
    //     $report = SecondmentWeeklyReport::findOrFail($id);
    //     $this->authorize('update', $report);

    //     Log::info('Updating Secondment Weekly Report', ['report_id' => $id, 'user_id' => Auth::id()]);

    //     $validated = $request->validate([
    //         'reporting_week' => 'required|date',
    //         'venue_id' => 'required|exists:venues,id',
    //         'event_id' => 'required|exists:events,id',

    //         // Basic Information
    //         'name' => 'nullable|string|max:255',
    //         'role' => 'nullable|string|max:255',
    //         'city' => 'nullable|string|max:255',

    //         // Weekly Activities
    //         'main_activities' => 'required|string|max:5000',

    //         // Gained Experience
    //         'experience_gained' => 'required|string|max:5000',

    //         // Innovation
    //         'innovation_description' => 'required|string|max:5000',
    //         'innovation_functional_areas' => 'nullable|array',
    //         'innovation_functional_areas.*' => 'exists:functional_areas,id',

    //         // Challenges
    //         'challenges_description' => 'required|string|max:5000',
    //         'challenges_resolved' => 'required|in:0,1',
    //         'challenges_functional_areas' => 'nullable|array',
    //         'challenges_functional_areas.*' => 'exists:functional_areas,id',

    //         // Value for Qatar
    //         'value_for_qatar' => 'required|in:0,1',
    //         'value_for_qatar_type' => 'nullable|string|max:255',
    //         'value_for_qatar_description' => 'nullable|string|max:5000',

    //         // HR / Wellbeing
    //         'wellbeing_status' => 'required|in:Good,Moderate,Challenging',
    //         'needs_support' => 'required|in:0,1',
    //         'support_types' => 'nullable|array',
    //         'support_types.*' => 'string',

    //         // Additional Comment
    //         'additional_comment' => 'nullable|string|max:2000',

    //         // Status
    //         'status' => 'required|in:draft,submitted',
    //     ]);

    //     DB::transaction(function () use ($report, $validated, $request) {
    //         // Update the report
    //         $report->update([
    //             'reporting_week' => $validated['reporting_week'],
    //             'event_id' => $validated['event_id'],
    //             'venue_id' => $validated['venue_id'],
    //             'name' => $validated['name'] ?? Auth::user()->name,
    //             'role' => $validated['role'],
    //             'city' => $validated['city'],
    //             'main_activities' => $validated['main_activities'],
    //             'experience_gained' => $validated['experience_gained'],
    //             'innovation_description' => $validated['innovation_description'],
    //             'challenges_description' => $validated['challenges_description'],
    //             'innovation_other_area' => $report->innovation_other_area ?? 'N/A',

    //             'challenges_resolved' => $validated['challenges_resolved'] == 1,
    //             'value_for_qatar' => $validated['value_for_qatar'] == 1,
    //             'value_for_qatar_type' => $validated['value_for_qatar_type'] ?? null,
    //             'value_for_qatar_description' => $validated['value_for_qatar_description'] ?? null,
    //             'wellbeing_status' => $validated['wellbeing_status'],
    //             'needs_support' => $validated['needs_support'] == 1,
    //             'support_types' => $validated['support_types'] ?? null,
    //             'additional_comment' => $validated['additional_comment'] ?? null,
    //             'status' => $validated['status'],
    //         ]);

    //         // Delete and recreate innovation functional areas
    //         \App\Models\Swr\SecondmentWeeklyReportInnovationFunctionalArea::where('secondment_weekly_report_id', $report->id)->delete();
    //         if (!empty($validated['innovation_functional_areas'])) {
    //             foreach ($validated['innovation_functional_areas'] as $area_id) {
    //                 \App\Models\Swr\SecondmentWeeklyReportInnovationFunctionalArea::create([
    //                     'secondment_weekly_report_id' => $report->id,
    //                     'functional_area_id' => $area_id,
    //                 ]);
    //             }
    //         }

    //         // Delete and recreate challenge functional areas
    //         \App\Models\Swr\SecondmentWeeklyReportChallengeFunctionalArea::where('secondment_weekly_report_id', $report->id)->delete();
    //         if (!empty($validated['challenges_functional_areas'])) {
    //             foreach ($validated['challenges_functional_areas'] as $area_id) {
    //                 \App\Models\Swr\SecondmentWeeklyReportChallengeFunctionalArea::create([
    //                     'secondment_weekly_report_id' => $report->id,
    //                     'functional_area_id' => $area_id,
    //                 ]);
    //             }
    //         }

    //         Log::info('Secondment Weekly Report updated', ['report_id' => $report->id, 'user_id' => Auth::id()]);
    //     });

    //     return redirect()->route('swr.report.detail', $report->id)
    //         ->with(['type' => 'success', 'message' => 'Report updated successfully!']);
    // }

    /**
     * Switch between events
     */
    public function switch($id)
    {
        $event = Event::findOrFail($id);
        session()->put('EVENT_ID', $id);

        return redirect()->route('swr.report')
            ->with(['type' => 'success', 'message' => 'Event switched successfully!']);
    }

    /**
     * Generate PDF for a report
     */
    public function reportPdf($id)
    {
        $report = SecondmentWeeklyReport::with(['event', 'venue', 'user', 'innovationFunctionalAreas.functionalArea', 'challengeFunctionalAreas.functionalArea'])->findOrFail($id);
        $this->authorize('view', $report);

        $data = [
            'report' => $report,
            'css' => public_path('css/pdf.css'),
        ];

        $filename = 'SWR-' . $report->event?->name . '-' . $report->venue?->title . '-' . format_date($report->reporting_week, 'Ymd') . '.pdf';

        $pdf = Pdf::loadView('swr.admin.report.pdf', $data);
        return $pdf->stream($filename);
    }

    /**
     * Get status badge color
     */
    private function getStatusColor($status)
    {
        $colors = [
            'draft' => 'secondary',
            'submitted' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
        ];
        return $colors[$status] ?? 'secondary';
    }
}
