<?php

namespace App\Http\Controllers\Wdr\Customer;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewReportEmailJob;
use Illuminate\Http\Request;
use App\Models\Wdr\DayType;
use App\Models\Wdr\Event;
use App\Models\Wdr\Venue;
use App\Models\Wdr\WorkforceDailyReport;
use App\Models\Wdr\WorkforceDailyReportDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;


class WorkforceDailyReportController extends Controller
{
    public function index()
    {
        $events = Event::all();
        return view('wdr.customer.report.list', compact(
            'events'
        ));
    }

    public function byEvent($event_id)
    {
        Log::info('byEvent: Fetching venues for event ID: ' . $event_id);
        $eventId = $event_id;

        $venues = auth()->user()->venues()
            ->whereIn('venues.id', function ($q) use ($eventId) {
                $q->select('venue_id')
                    ->from('venue_event')
                    ->where('event_id', $eventId);
            })
            ->select('venues.id', 'venues.title')
            ->orderBy('venues.title')
            ->get();

        return response()->json($venues);
    }

    public function create()
    {
        $events = Event::findOrFail(session()->get('EVENT_ID'));
        $venues = $events->venues;
        $day_types = DayType::all();

        return view('wdr.customer.report.create', compact(
            'events',
            'venues',
            'day_types'
        ));
    }

    public function gallery($id)
    {
        $events = Event::all();
        $report = WorkforceDailyReport::find($id);

        $this->authorize('view', $report);
        return view('wdr.customer.report.gallery', compact(
            'events',
            'report',
        ));
    }

    public function list(Request $request)
    {

        $search = request('search');
        $filter = request('filter');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $mds_schedule_event_filter = (request()->mds_schedule_event_filter) ? request()->mds_schedule_event_filter : "";
        $mds_schedule_venue_filter = (request()->mds_schedule_venue_filter) ? request()->mds_schedule_venue_filter : "";
        $mds_schedule_rsp_filter = (request()->mds_schedule_rsp_filter) ? request()->mds_schedule_rsp_filter : "";

        $ops = WorkforceDailyReport::orderBy($sort, $order);
        $ops = $ops->where('event_id', session()->get('EVENT_ID'));
        $ops = $ops->where('venue_id', session()->get('VENUE_ID'));


        if ($search) {
            $ops = $ops->where('incidents', 'like', '%' . $search . '%')
                ->orWhere('other_notes', 'like', '%' . $search . '%');
        }

        if ($mds_schedule_event_filter) {
            $ops = $ops->where('event_id', $mds_schedule_event_filter);
        }

        if ($mds_schedule_venue_filter) {
            $ops = $ops->where('venue_id', $mds_schedule_venue_filter);
        }

        if ($mds_schedule_rsp_filter) {
            $ops = $ops->where('rsp_id', $mds_schedule_rsp_filter);
        }

        $total = $ops->count();
        $limit = request("limit");
        $limit = max(1, min($limit, 100)); // min=1, max=100
        $ops = $ops->paginate($limit)->through(function ($op) {

            // $location = Location::find($guests->location_id);
            $full_name = $op->first_name . ' ' . $op->last_name;
            if ($op->is_admin == 'X') {
                $avatar_status = 'status-away';
            } else {
                $avatar_status = '';
            }

            if ($op->photo) {
                $image = ' <div class="avatar avatar-m ' . $avatar_status . '">
                                <a  href="#" role="button" title="' . $full_name . '">
                                    <img class="rounded-circle pull-up" src="/storage/upload/profile_images/' . $op->photo . '" alt="" />
                                </a>
                            </div>';
            } else {
                $image = '  <div class="avatar avatar-m ' . $avatar_status . '  me-1" id="project_team_members_init">
                                <a class="dropdown-toggle dropdown-caret-none d-inline-block" href="#" role="button" title="' . $full_name . '">
                                    <div class="avatar avatar-m  rounded-circle pull-up">
                                        <div class="avatar-name rounded-circle me-2"><span>' . generateInitials($full_name) . '</span></div>
                                    </div>
                                </a>
                            </div>';
            }

            $actions = '<div class="font-sans-serif btn-reveal-trigger position-static">';
            $edit_actions = '<a href="javascript:void(0)" class="btn btn-sm me-1" id="edit_guest_offcanv" data-id="' .
                $op->id .
                '" data-table="report_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>';
            $delete_actions =
                '<a href="javascript:void(0)" class="btn btn-sm me-1" data-table="report_table" data-id="' .
                $op->id .
                '" id="deleteReport" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
                '<i class="fas fa-trash text-danger"></i></a>';
            $upload_img_actions =
                '<a href="javascript:void(0)" class="btn btn-sm me-1" data-table="report_table" data-id="' .
                $op->id .
                '" id="uploadImagesReport" data-bs-toggle="tooltip" data-bs-placement="right" title="Upload Images">' .
                '<i class="fas fa-arrow-up text-success"></i></a>';
            $actions_pass =
                '<a href="' . route('wdr.report.pdf', $op->id) . '" target="_blank" class="btn btn-sm me-1" data-table="report_table" data-id="' .
                $op->id .
                '" id="generatePass" data-bs-toggle="tooltip" data-bs-placement="right" title="Generate Report">' .
                '<i class="fas fa-file-pdf text-success"></i></i></a>';

            $actions .=  $actions . $actions_pass. $delete_actions;
            $actions .= '</div>';

            // $order_status =  '<span class="badge badge-phoenix fs--2 ms-2 badge-phoenix-' . $op->status?->color . ' "><span class="badge-label" id="change_participant_status" style="cursor:pointer" data-id="' . $op->id . '"data-status_id="' . $op->status?->id . '" data-table="participant_table">' . $op->status?->title . '</span><span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;cursor:pointer"></span></span>';
            // $qid_image_route = $op->qidDocument
            //     ? '<a href="' . route('participant.docs.download', $op->qidDocument) . '" target="_blank" ><span><i class="fa-solid fa-eye me-2"></i>' . $op->qid . '</span></a>'
            //     : $op->qid;

            // $gardian_qid_image_route = $op->guardian->qidDocument
            //     ? '<a href="' . route('guardian.docs.download', $op->guardian->qidDocument) . '" target="_blank" ><span><i class="fa-solid fa-eye me-2"></i>' . $op->guardian->qid . '</span></a>'
            //     : $op->guardian->qid;
            if ($op->photos()->exists()) {
                $image_icon_color = 'text-success';
                $href_route = route('wdr.report.gallery', ['id' => $op->id]);
                // $link = '<a href="' . $href_route . '" target="_blank"><i class="fa-solid fa-image fa-2x ' . $image_icon_color . '"></i></a>';
                $link =  '<a href="' . $href_route . '" target="_blank" class="position-relative d-inline-block">
                    <i class="fa-solid fa-image fa-2x ' . $image_icon_color . '"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                        ' . $op->photos()->count() . '
                    </span>
                </a>';
            } else {
                $image_icon_color = 'text-muted';
                $href_route = 'javascript:void(0)';
                $link = '<i class="fa-solid fa-image fa-2x ' . $image_icon_color . '"></i>';
            }
            $image = $link;
            return  [
                'id' => $op->id,
                'ref_number' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . '<span class="badge rounded-pill ref-badge text-bg-light border px-3 py-2 fw-8 fw-semibold">' . $op->reference_number . '</span>' . '</div>',
                // 'ref_number' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->reference_number . '</div>',
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $image,
                'venue_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->venue?->title . '</div>',
                'report_date' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->report_date) . '</div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->event?->name . '</div>',
                'day_type_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . '<span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">' . $op->dayType?->title . '</span>' . '</div>',
                // 'day_type_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->dayType?->title . '</div>',
                'demand_of_day' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->demand_of_day . '</div>',
                'attended' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->attended . '</div>',
                'attendance_percentage' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->attendance_percentage . '</div>',
                'volunteers_meals_ordered' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->volunteers_meals_ordered . '</div>',
                'volunteers_meals_redeemed' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->volunteers_meals_redeemed . '</div>',
                'volunteer_meal_percentage' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->volunteer_meal_percentage . '</div>',
                'loc_staff_meals_ordered' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->loc_staff_meals_ordered . '</div>',
                'loc_staff_meals_redeemed' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->loc_staff_meals_redeemed . '</div>',
                'loc_staff_meal_percentage' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->loc_staff_meal_percentage . '</div>',
                'incidents' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->incidents . '</div>',
                'other_notes' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->other_notes . '</div>',
                'reported_by' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->reportedBy?->name . '</div>',
                'action' => $actions,
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $ops->items(),
            "total" => $total,
        ]);
    }

    public function GenerateFileName(WorkforceDailyReport $op, $seq = 0, $extension = 'pdf')
    {

        // ---------- Build automated filename ----------
        // Pick the right date field from your model (adjust if needed)
        $date = $op->report_date ?? $op->created_at;
        $dateStr = \Carbon\Carbon::parse($date)->format('Ymd');

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

    public function store(Request $request)
    {
        Log::info('Storing Workforce Daily Report', ['request' => $request->all()]);

        $user = Auth::user();

        $validated = $request->validate([
            'report_date' => ['required', 'date_format:d/m/Y'],
            'venue_id'      => 'required|string|max:50',
            'day_type_id'    => 'required|string|max:30',

            'demand_of_day' => 'required|integer|min:0',
            'attended'    => 'required|integer|min:0',

            'volunteers_meals_ordered'       => 'nullable|integer|min:0',
            'volunteers_meals_redeemed'   => 'nullable|integer|min:0',
            'loc_staff_meals_ordered'            => 'nullable|integer|min:0',
            'loc_staff_meals_redeemed'        => 'nullable|integer|min:0',
            'loc_external_meals_ordered'            => 'nullable|integer|min:0',
            'loc_external_meals_redeemed'        => 'nullable|integer|min:0',

            // 'reporter_name' => 'required|string|max:255',
            'incidents'    => 'nullable|string',
            'other_notes'   => 'nullable|string',

            'photos'       => 'nullable|array',
            'photos.*'     => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB each
            // 'photos.*'     => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB each
        ]);

        // Calculate percentages safely
        $demand = (int) $validated['demand_of_day'];
        $attended = (int) $validated['attended'];
        $attendancePct = $demand > 0 ? round(($attended / $demand) * 100, 2) : null;

        $volOrdered = (int) ($validated['volunteers_meals_ordered'] ?? 0);
        $volRedeemed = (int) ($validated['volunteers_meals_redeemed'] ?? 0);
        $volPct = $volOrdered > 0 ? round(($volRedeemed / $volOrdered) * 100, 2) : null;

        $staffOrdered = (int) ($validated['loc_staff_meals_ordered'] ?? 0);
        $staffRedeemed = (int) ($validated['loc_staff_meals_redeemed'] ?? 0);
        $staffPct = $staffOrdered > 0 ? round(($staffRedeemed / $staffOrdered) * 100, 2) : null;

        $locExternalOrdered = (int) ($validated['loc_external_meals_ordered'] ?? 0);
        $locExternalRedeemed = (int) ($validated['loc_external_meals_redeemed'] ?? 0);
        $locExternalPct = $locExternalOrdered > 0 ? round(($locExternalRedeemed / $locExternalOrdered) * 100, 2) : null;

        DB::transaction(function () use ($request, $validated, $attendancePct, $volPct, $staffPct, $locExternalPct, $user) {

            $seq = nextSequence('wdr');

            $report = WorkforceDailyReport::create([
                'reference_number' => 'WDR-' . date('Y') . '-' . get_current_event_id() . '-' . $validated['venue_id'] . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT),
                'report_date' => $validated['report_date']
                    ? Carbon::createFromFormat('d/m/Y', $validated['report_date'])->toDateString()
                    : null,
                'venue_id'       => $validated['venue_id'],
                'day_type_id'    => $validated['day_type_id'],

                'demand_of_day' => $validated['demand_of_day'],
                'attended'      => $validated['attended'],
                'attendance_percentage' => $attendancePct,

                'volunteers_meals_ordered' => $validated['volunteers_meals_ordered'] ?? 0,
                'volunteers_meals_redeemed' => $validated['volunteers_meals_redeemed'] ?? 0,
                'volunteer_meal_percentage' => $volPct,

                'loc_staff_meals_ordered' => $validated['loc_staff_meals_ordered'] ?? 0,
                'loc_staff_meals_redeemed' => $validated['loc_staff_meals_redeemed'] ?? 0,
                'loc_staff_meal_percentage' => $staffPct,

                'loc_external_meals_ordered' => $validated['loc_external_meals_ordered'] ?? 0,
                'loc_external_meals_redeemed' => $validated['loc_external_meals_redeemed'] ?? 0,
                'loc_external_meal_percentage' => $locExternalPct,

                // 'reporter_name' => $validated['reporter_name'],
                'incidents'     => $validated['incidents'] ?? null,
                'other_notes'   => $validated['other_notes'] ?? null,
                'event_id'      => get_current_event_id(),
                'created_by'    => auth()->id(),
                'updated_by'    => auth()->id(),
            ]);

            // Save multiple photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    if (!$photo || !$photo->isValid()) {
                        Log::warning("Invalid photo file skipped for report ID: {$report->id}");
                        continue; // Skip invalid files
                    }
                    
                    $dir = "reports/{$report->id}/photos";
                    
                    // Ensure directory exists
                    $disk = Storage::disk('private');
                    if (!$disk->exists($dir)) {
                        $disk->makeDirectory($dir, 0755, true);
                        Log::info("Created directory: {$dir} for report ID: {$report->id}");
                    }
                    // $filename = uniqid() . '.jpg'; // normalize to jpg

                    // // 🔥 Resize image
                    // // $image = Image::read($photo);
                    // $manager = new ImageManager(new Driver());

                    // // ✅ Read image
                    // $image = $manager->read($photo)
                    //     ->orient() // replaces orientate()
                    //     ->resize(1600, null, function ($constraint) {
                    //         $constraint->aspectRatio();
                    //         $constraint->upsize();
                    //     })
                    //     ->toJpeg(85); // encode


                    // // 🔥 Store in PRIVATE disk
                    // $path = Storage::disk('private')->put(
                    //     "{$dir}/{$filename}",
                    //     $image
                    // );

                    $sequence = ($report->photos()->count() ?? 0) + 1;
                    Log::info("Storing photo #{$sequence} for report ID: {$report->id}");
                    Log::info('photo count: ' . ($report->photos()->count() ?? 0));

                    $extension = $photo->getClientOriginalExtension() ?: 'jpg';
                    $filename = $this->GenerateFileName($report, $sequence, $extension);

                    if (empty($filename)) {
                        Log::error("Generated filename is empty for report ID: {$report->id}");
                        continue; // Skip this file
                    }

                    Log::info("Generated filename: {$filename} for report ID: {$report->id}");
                    Log::info("Storing file with original name: {$photo->getClientOriginalName()} and extension: {$extension} for report ID: {$report->id}");
                    Log::info("Storage directory: {$dir} for report ID: {$report->id}");

                    try {
                        // Use getPathname() instead of putFileAs/getRealPath() which fails on Windows temp files
                        $tmpPath = $photo->getPathname();
                        $stream = fopen($tmpPath, 'r');
                        if ($stream === false) {
                            Log::error("Cannot open photo file at path: {$tmpPath} for report ID: {$report->id}");
                            continue;
                        }
                        $disk->writeStream("{$dir}/{$filename}", $stream);
                        fclose($stream);
                        $path = "{$dir}/{$filename}";
                        
                        Log::info("File stored successfully at path: {$path} for report ID: {$report->id}");
                        
                        WorkforceDailyReportDocument::create([
                            'report_id'     => $report->id,
                            'disk'          => 'private',
                            'path'          => $path,
                            'original_name' => $photo->getClientOriginalName(),
                            'custom_name'   => $filename,
                            'extension'     => $photo->getClientOriginalExtension() ?: 'jpg',
                            'mime'          => $photo->getClientMimeType(),
                            'size'          => $photo->getSize(),
                        ]);
                        
                        Log::info("Document record created successfully for report ID: {$report->id}");
                        
                    } catch (\Exception $e) {
                        Log::error("Failed to store photo for report ID: {$report->id}, error: " . $e->getMessage());
                        Log::error("Stack trace: " . $e->getTraceAsString());
                        continue; // Skip this file and continue with others
                    }
                }
            }

            $save_pass_pdf = $this->save_pass_pdf($report, $seq);


            if ($save_pass_pdf) {
                $details = [
                    'email' => [$user->email],
                    'venue' => $report->venue->title,
                    'event' => $report->event->name,
                    'reference_number' => $report->reference_number,
                    'report_date' => \Carbon\Carbon::parse($report->report_date)->format('l jS \of F Y'),
                    'filename' => $this->GenerateFileName($report),
                    // 'filename' => $report->venue?->short_name . '_' . $report->reference_number . '.pdf',
                ];

                // Log::info('BookingController::store details: ' . json_encode($details));
                if (config('settings.send_notifications')) {
                    SendNewReportEmailJob::dispatch($details);
                }
            }
        });


        $toastr_message = [
            'type' => 'success',
            'message' => 'Report submitted successfully!',
        ];

        return redirect()->route('wdr.report')->with($toastr_message);
    }

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                appLog('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                appLog('Event ID: ' . session()->get('EVENT_ID'));
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('home')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            } else {
                // return back()->with('error', 'Workspace not found.');
                // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
                return back()->with('error', 'Event not found.');
            }
        } else {
            session()->forget('EVENT_ID');
            // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully. now showing all workspace data');
            return back()->withInput();
        }
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
                return redirect()->route('wdr.report')->with('message', 'Event Switched.');
                // return back()->with('message', 'Event Switched.');
            }
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        // return redirect()->route('tracki.project.show.card')->with('error', 'Workspace not found.');
        // appLog('event_id is null');
        return redirect()->route('wdr.report')->with('error', 'Event not found.');
        // }
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $report = WorkforceDailyReport::with('photos')->findOrFail($id);

            // Delete files first
            foreach ($report->photos as $doc) {
                if ($doc->disk && $doc->path) {
                    Storage::disk($doc->disk)->delete($doc->path);
                }
            }
            Storage::disk('private')->deleteDirectory("reports/{$report->id}");

            // Delete document records
            $report->photos()->delete();

            // Delete the report itself
            $report->delete();
        });

        return response()->json([
            'error'   => false,
            'message' => 'Workforce Daily Report and all images deleted successfully.',
        ]);
    }

    public function save_pass_pdf($wdr, $seq)
    {
        // set_time_limit(300);

        $qr_code = null;

        $data = [
            'wdr' => $wdr,
            'qr_code' => $qr_code,
        ];

        $filename = $this->GenerateFileName($wdr);

        $data['css'] = public_path('assets/css/invoice.css');
        $pdf = Pdf::loadView('wdr.admin.report.rpdf', $data);
        Storage::disk('private')->put('wdr/pdf-exports/' . $filename, $pdf->output());
        // Storage::disk('private')->put('wdr/pdf-exports/' . $wdr->reference_number . '.pdf', $pdf->output());

        return 1;
    }
}
