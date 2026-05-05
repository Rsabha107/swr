<?php

namespace App\Http\Controllers\Wdr\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wdr\Event;
use App\Models\Wdr\WorkforceDailyReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use ZipArchive;

class WorkforceDailyReportController extends Controller
{
    //
    public function index()
    {
        $wd_reports = WorkforceDailyReport::all();
        $events = Event::all();

        return view('wdr.admin.report.list', compact(
            'wd_reports',
            'events',
        ));
    }

    public function gallery($id)
    {
        // $events = Event::all();
        $report = WorkforceDailyReport::findOrFail($id);
        return view('wdr.admin.report.gallery', compact(
            // 'events',
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

        if ($search) {
            $ops = $ops->where('reference_number', 'like', '%' . $search . '%')
                ->orWhereHas('venue', function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('event', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('reportedBy', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                });
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
            // Generate Pass
            $actions_pass =
                '<a href="'. route('wdr.report.pdf', $op->id) .'" target="_blank" class="btn btn-sm me-1" data-table="report_table" data-id="' .
                $op->id .
                '" id="generatePass" data-bs-toggle="tooltip" data-bs-placement="right" title="Generate Report">' .
                '<i class="fas fa-file-pdf text-success"></i></i></a>';
            $edit_actions = '<a href="javascript:void(0)" class="btn btn-sm" id="edit_guest_offcanv" data-id="' .
                $op->id .
                '" data-table="guest_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Update">' .
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
                '<i class="bx bx-arrow-to-top text-success"></i></a>';

            $actions .=  $actions . $actions_pass . $delete_actions;
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
                $href_route = route('wdr.admin.report.gallery', ['id' => $op->id]);
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
                'image' => '<div class="align-middle white-space-wrap fs-9 px-3">' . $image,
                'ref_number' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . '<span class="badge rounded-pill text-bg-light ref-badge border px-3 py-2 fw-semibold">' . $op->reference_number . '</span>' . '</div>',
                'venue_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->venue?->title . '</div>',
                'report_date' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . format_date($op->report_date) . '</div>',
                'event_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' .  $op->event?->name . '</div>',
                'day_type_id' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . '<span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">' . $op->dayType?->title . '</span>' . '</div>',
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

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                appLog('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                appLog('Event ID: ' . session()->get('EVENT_ID'));
                // return redirect()->route('tracki.project.show.card')->with('message', 'Workspace switched successfully.');
                return redirect()->route('wdr.admin.report')->with('message', 'Event Switched.');
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

    // pdf report
    public function reportPdf(Request $request, $id)
    {

        $op = WorkforceDailyReport::findOrFail($id);

        $qr_code = null;

        $data = [
            'wdr' => $op,
            'qr_code' => $qr_code,
            // 'rsp_arrival_date' => $rspArrivalDate,
        ];

        if ($request->has('preview')) {
            $data['css'] = asset('assets/css/invoice.css');
            return view('wdr.admin.report.rpdf', $data);
        } else {
            $data['css'] = public_path('assets/css/invoice.css');
        }

        // ---------- Build automated filename ----------
        // Pick the right date field from your model (adjust if needed)
        $date = $op->report_date ?? $op->created_at;
        $dateStr = \Carbon\Carbon::parse($date)->format('Ymd');

        // Stadium / venue code (adjust field names)
        $stadiumCode = $op->venue?->short_name ?? 'Stadium';

        // Report number (adjust field names)
        $serialNumber = getNumber($op->reference_number ?? '0');

        // Example: 20260216_974_Match Report #1_Kuwait-Qatar.pdf
        $filename = "{$stadiumCode}_{$dateStr}_{$serialNumber}" . ".pdf";

        // sanitize filename for Windows/Linux
        $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $filename);
        $filename = preg_replace('/\s+/', ' ', trim($filename));
        // ---------------------------------------------

        $pdf = Pdf::loadView('wdr.admin.report.rpdf', $data);
        // return $pdf->download('itsolutionstuff.pdf');
        return $pdf->stream($filename);
    }  //taskDetailsPDF

}
