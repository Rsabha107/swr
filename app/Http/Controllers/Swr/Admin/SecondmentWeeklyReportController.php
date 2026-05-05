<?php

namespace App\Http\Controllers\Swr\Admin;

use App\Http\Controllers\Controller;
use App\Models\Swr\SecondmentWeeklyReport;
use App\Models\Swr\Event;
use App\Models\Swr\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;


class SecondmentWeeklyReportController extends Controller
{
    /**
     * Display all secondment weekly reports (Admin view)
     */
    public function index()
    {
        $events = Event::all();
        return view('swr.admin.report.list', compact('events'));
    }

    /**
     * Get venues for a specific event (AJAX endpoint)
     */
    public function byEvent($event_id)
    {
        Log::info('Admin byEvent: Fetching venues for event ID: ' . $event_id);

        $venues = Venue::whereIn('id', function ($q) use ($event_id) {
            $q->select('venue_id')
                ->from('venue_event')
                ->where('event_id', $event_id);
        })
        ->select('venues.id', 'venues.title', 'venues.city')
        ->orderBy('venues.title')
        ->get();

        return response()->json($venues);
    }

    /**
     * Display all reports with filtering, searching, and admin actions (AJAX)
     */
    public function list(Request $request)
    {
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        $limit = min($request->input('limit', 10), 100);
        $status = $request->input('status', '');
        $event_id = $request->input('event_id', '');
        $venue_id = $request->input('venue_id', '');

        $query = SecondmentWeeklyReport::with(['event', 'venue', 'user', 'documents'])
            ->orderBy($sort, $order);

        // Apply filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('main_activities', 'like', '%' . $search . '%')
                    ->orWhere('challenges_description', 'like', '%' . $search . '%')
                    ->orWhere('innovation_description', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($event_id) {
            $query->where('event_id', $event_id);
        }

        if ($venue_id) {
            $query->where('venue_id', $venue_id);
        }

        $total = $query->count();
        $reports = $query->paginate($limit);

        $rows = $reports->through(function ($report) {
            $statusBadge = '<span class="badge bg-' . $this->getStatusColor($report->status) . '">' . $report->getStatusLabel() . '</span>';
            
            $actions = '<div class="btn-group btn-group-sm" role="group">';
            $actions .= '<a href="' . route('swr.admin.report.detail', $report->id) . '" class="btn btn-info" title="View Details"><i class="fas fa-eye"></i></a>';
            
            if ($report->documents->count() > 0) {
                $actions .= '<a href="' . route('swr.admin.report.gallery', $report->id) . '" class="btn btn-secondary" title="Photos (' . $report->documents->count() . ')"><i class="fas fa-images"></i></a>';
            }

            if ($report->status === 'submitted') {
                $actions .= '<button class="btn btn-success approve-report" data-id="' . $report->id . '" title="Approve"><i class="fas fa-check"></i></button>';
                $actions .= '<button class="btn btn-danger reject-report" data-id="' . $report->id . '" title="Reject"><i class="fas fa-times"></i></button>';
            }

            $actions .= '<a href="' . route('swr.admin.report.pdf', $report->id) . '" target="_blank" class="btn btn-warning" title="Export as PDF"><i class="fas fa-file-pdf"></i></a>';
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
                'challenges_description' => Str::limit($report->challenges_description ?? 'N/A', 50),
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
     * Show details of a specific report
     */
    public function detail($id)
    {
        $report = SecondmentWeeklyReport::with(['event', 'venue', 'user', 'documents', 'innovationFunctionalAreas.functionalArea', 'challengeFunctionalAreas.functionalArea'])->findOrFail($id);
        $functionalAreas = $this->getFunctionalAreas();

        return view('swr.admin.report.detail', compact('report', 'functionalAreas'));
    }

    /**
     * Display gallery of photos attached to a report
     */
    public function gallery($id)
    {
        $report = SecondmentWeeklyReport::with('documents')->findOrFail($id);
        return view('swr.admin.report.gallery', compact('report'));
    }

    /**
     * Approve a submitted report
     */
    public function approve(Request $request, $id)
    {
        $report = SecondmentWeeklyReport::findOrFail($id);

        if ($report->status !== 'submitted') {
            return response()->json(['success' => false, 'message' => 'Report cannot be approved in its current status.'], 422);
        }

        $report->update([
            'status' => 'approved',
        ]);

        Log::info('Report approved', ['report_id' => $id, 'approved_by' => Auth::id()]);

        return response()->json(['success' => true, 'message' => 'Report approved successfully!']);
    }

    /**
     * Reject a submitted report
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $report = SecondmentWeeklyReport::findOrFail($id);

        if ($report->status !== 'submitted') {
            return response()->json(['success' => false, 'message' => 'Report cannot be rejected in its current status.'], 422);
        }

        $report->update([
            'status' => 'rejected',
        ]);

        Log::info('Report rejected', ['report_id' => $id, 'rejected_by' => Auth::id()]);

        return response()->json(['success' => true, 'message' => 'Report rejected successfully!']);
    }

    /**
     * Generate PDF export of a report
     */
    public function reportPdf($id = null)
    {
        $report = SecondmentWeeklyReport::with(['event', 'venue', 'user'])->findOrFail($id);

        $data = [
            'report' => $report,
            'css' => public_path('css/pdf.css'),
        ];

        $filename = 'SWR-' . $report->event?->name . '-' . $report->venue?->title . '-' . format_date($report->reporting_week, 'Ymd') . '.pdf';

        $pdf = Pdf::loadView('swr.admin.report.pdf', $data);
        // return $pdf->download($filename);
        return $pdf->stream($filename);
    }

    /**
     * Delete a report (Admin only)
     */
    public function destroy($id)
    {
        $report = SecondmentWeeklyReport::findOrFail($id);

        DB::transaction(function () use ($report) {
            // Delete all attached documents
            foreach ($report->documents as $doc) {
                Storage::disk($doc->disk)->delete($doc->file_path);
                $doc->delete();
            }
            
            // Delete the report
            $report->delete();
        });

        Log::info('Report deleted', ['report_id' => $id, 'deleted_by' => Auth::id()]);

        return response()->json([
            'success' => true,
            'message' => 'Report deleted successfully!',
        ]);
    }

    /**
     * Export reports to Excel
     */
    public function export(Request $request)
    {
        $event_id = $request->input('event_id');
        $venue_id = $request->input('venue_id');
        $status = $request->input('status');

        $query = SecondmentWeeklyReport::with(['event', 'venue', 'user']);

        if ($event_id) {
            $query->where('event_id', $event_id);
        }

        if ($venue_id) {
            $query->where('venue_id', $venue_id);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->get();

        // You would typically use a library like maatwebsite/excel here
        // For now, returning a CSV-like JSON structure
        $filename = 'SWR_Export_' . now()->format('Ymd_His') . '.csv';

        $csv = "Report ID,Name,Venue,Event,Reporting Week,Wellbeing,Status,Photos,Submitted At\n";
        foreach ($reports as $report) {
            $csv .= implode(',', [
                $report->id,
                '"' . $report->user?->name . '"',
                '"' . $report->venue?->title . '"',
                '"' . $report->event?->name . '"',
                $report->reporting_week,
                $report->wellbeing_status,
                $report->status,
                $report->documents->count(),
                $report->created_at->format('Y-m-d H:i'),
            ]) . "\n";
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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

    /**
     * Get functional areas list
     */
    private function getFunctionalAreas()
    {
        return [
            'ACS & ACR', 'BRO', 'CAT', 'CMP', 'FNB', 'GOP', 'HOS', 'HSE', 'LOG',
            'MED', 'MEO', 'MER', 'MRD', 'OVL', 'PWR', 'SEC', 'SGN', 'SPS',
            'STM', 'SUS', 'TKT', 'TSV', 'WKF', 'Other',
        ];
    }

        public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                appLog('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                appLog('Event ID: ' . session()->get('EVENT_ID'));
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('swr.admin.report')->with('message', 'Event Switched.');
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
}
