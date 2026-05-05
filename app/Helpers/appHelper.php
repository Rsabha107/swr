<?php

// namespace App\Http\Helpers;

use App\Models\Event;
use App\Models\Gms\ParticipantStatus;
use App\Models\Vapp\MatchCategory;
use App\Models\Vapp\ParkingCapacity;
use App\Models\Vapp\VappRequest;
use App\Models\Vapp\VappRequestStatus;
use App\Models\Vapp\Venue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Permission\Models\Role;

/**
 * Get a private image as a base64 data URI
 *
 * @param string $disk
 * @param string $path
 * @return string|null
 */


function getNumber($code)
{
    return (int) Str::afterLast($code, '-');
}



function private_image_base64($disk, $path)
{
    if (!Storage::disk($disk)->exists($path)) {
        return null;
    }

    $mime = Storage::disk($disk)->mimeType($path);
    $data = base64_encode(Storage::disk($disk)->get($path));

    return "data:$mime;base64,$data";
}

if (! function_exists('get_current_event_id')) {
    /**
     * Get the current event ID from session or default
     *
     * @return int|null
     */
    function get_current_event_id()
    {
        // Assuming event ID is stored in session
        return session('EVENT_ID', null);
    }
}

if (! function_exists('nextSequence')) {
    /**
     * Get the next value in a named sequence
     *
     * @param string $key
     * @return int
     * @throws \Exception
     */

    function nextSequence(string $key): int
    {
        return DB::transaction(function () use ($key) {
            $row = DB::table('sequences')
                ->where('key', $key)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                throw new Exception("Sequence '{$key}' not found");
            }

            $next = $row->value + 1;

            DB::table('sequences')
                ->where('key', $key)
                ->update(['value' => $next]);

            return $next;
        });
    }
}

if (! function_exists('getNameById')) {
    /**
     * Get a column value (default: name) from any table by ID
     *
     * @param string $table
     * @param int|string $id
     * @param string $column
     * @return string|null
     */
    function getNameById(string $table, $id, string $column = 'name')
    {
        return DB::table($table)->where('id', $id)->value($column);
    }
}

if (! function_exists('getIdByName')) {
    /**
     * Get a column value (default: name) from any table by ID
     *
     * @param string $table
     * @param int|string $id
     * @param string $column
     * @return string|null
     */
    function getIdByName(string $table, $name, string $column = 'title')
    {
        return DB::table($table)->where($column, $name)->value('id');
    }
}

if (!function_exists('getVenueIdByLabel')) {
    function getVenueIdByLabel(string $label): ?int
    {
        $op_id = Venue::where('short_name', $label)->pluck('id')->first();

        return $op_id ?? null;
    }
}

if (!function_exists('getStatusIdByLabel')) {
    function getStatusIdByLabel(string $label): ?int
    {
        // appLog('getStatusIdByLabel called with label: ' . $label);
        $status_id = ParticipantStatus::where('title', $label)->pluck('id')->first();

        // appLog('getStatusIdByLabel found status_id: ' . ($status_id ?? 'null'));
        return $status_id ?? null;
    }
}

if (!function_exists('getRoleIdByLabel')) {
    function getRoleIdByLabel(string $label): ?int
    {
        $op_id = Role::where('name', $label)->pluck('id')->first();

        return $op_id ?? null;
    }
}


if (!function_exists('get_label')) {

    function get_label($label, $default, $locale = '')
    {
        if (Lang::has('labels.' . $label, $locale)) {
            return trans('labels.' . $label, [], $locale);
        } else {
            return $default;
        }
    }
}

if (!function_exists('getQrCode')) {

    function getQrCode($id, $size)
    {
        // $qr_code = QrCode::size($size)->generate($id);
        $qr_code = base64_encode(QrCode::format('svg')->size($size)->errorCorrection('H')->generate($id));

        return ($qr_code);
    }
}

if (!function_exists('time_range_segment')) {

    function time_range_segment($time_range, $segment)
    {
        if ($segment == 'from') {
            $return_segment = Str::substr($time_range, 0,  Str::position($time_range, "-") - 1);
            return $return_segment;
        } elseif ($segment == 'to') {
            $return_segment = Str::substr($time_range, Str::position($time_range, "-") + 1);
            return $return_segment;
        } else {
            return null;
        }
    }
}

/**
 * Generate initials from a name
 *
 * @param string $name
 * @return string
 */
if (!function_exists('generate')) {
    function generateInitials(string $name): string
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[0], 0, 1, 'UTF-8') .
                    mb_substr(end($words), 0, 1, 'UTF-8'),
                'UTF-8'
            );
        }
        return makeInitialsFromSingleWord($name);
    }
}

/**
 * Make initials from a word with no spaces
 *
 * @param string $name
 * @return string
 */
if (!function_exists('makeInitialsFromSingleWord')) {
    function makeInitialsFromSingleWord(string $name): string
    {
        preg_match_all('#([A-Z]+)#', $name, $capitals);
        if (count($capitals[1]) >= 2) {
            return mb_substr(implode('', $capitals[1]), 0, 2, 'UTF-8');
        }
        return mb_strtoupper(mb_substr($name, 0, 2, 'UTF-8'), 'UTF-8');
    }
}


if (!function_exists('format_date')) {
    function format_date($date, $time = null, $format = null, $apply_timezone = true)
    {
        if ($date) {
            // appLog('date: '.$date);
            // appLog('time: '.$time);
            // appLog('format: '.$format);
            $format = $format ?? get_php_date_format();
            $time = $time ?? '';

            $date = $time != '' ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::parse($date);

            // appLog('date: '.$date);

            // if ($time !== '') {
            //     if ($apply_timezone) {
            //         $date->setTimezone(config('app.timezone'));
            //     }
            //     $format .= ' ' . $time;
            // }

            // appLog($date->format($format));

            return $date->format($format);
        } else {
            return '-';
        }
    }
}

if (!function_exists('get_php_date_format')) {
    function get_php_date_format()
    {
        // $general_settings = get_settings('general_settings');
        $date_format = 'DD-MM-YYYY|d-m-Y';
        // $date_format = $general_settings['date_format'] ?? 'DD-MM-YYYY|d-m-Y';
        $date_format = explode('|', $date_format);
        return $date_format[1];
    }
}

if (!function_exists('get_approval_id')) {

    function get_approval_id($variable)
    {
        $ret = VappRequestStatus::where('title', $variable)->first();
        if ($ret) {
            return $ret->id;
        } else {
            return null;
        }
    }
}

if (!function_exists('get_capacity_totals')) {
    function get_capacity_totals($event_id, $venue_id,  $vapp_size_id, $parking_id, $variation_id, $match_category_id, $match_id,  $status_name)
    {
        $status_id = get_approval_id($status_name);
        appLog('Helper::appHelper get_capacity_totals $status_id: ' . $status_id);
        // $query = VappRequest::where('event_id', $event_id)
        //     ->where('venue_id', $venue_id)
        //     ->where('parking_id', $parking_id)
        //     ->where('variation_id', $variation_id)
        //     ->where('vapp_size_id', $vapp_size_id)
        //     ->where('match_id', $match_id)
        //     ->where('request_status_id', $status_id)->get();

        $excludedVenueIds = [
            getVenueIdByLabel('STA'),
            getVenueIdByLabel('INF'),
        ];

        $results = VappRequest::where('event_id', $event_id)
            ->where('venue_id', $venue_id)
            ->whereNotIn('venue_id', $excludedVenueIds)
            ->where('parking_id', $parking_id)
            ->where('variation_id', $variation_id)
            ->where('vapp_size_id', $vapp_size_id)
            ->where('request_status_id', $status_id)
            ->when($match_category_id === getMatchCategoryIdByLabel('MATCH'), function ($q) use ($match_id) {
                $q->where('match_id', $match_id);
            })
            ->when($match_category_id === getMatchCategoryIdByLabel('ALL'), function ($q) {
                $q->where('match_category_id', getMatchCategoryIdByLabel('ALL'));
            })->get();

        appLog('event_id: ' . $event_id);
        appLog('venue_id: ' . $venue_id);
        appLog('vapp_size_id: ' . $vapp_size_id);
        appLog('parking_id: ' . $parking_id);
        appLog('variation_id: ' . $variation_id);
        appLog('status_name: ' . $status_name);

        appLog($results);
        $ret = $results->sum('approved_vapps');
        if ($ret) {
            return $ret;
        } else {
            return 0;
        }
    }
}

// get the totals of capacity for category ALL
// event, venue, parking, match category of ALL, vapp size.
if (!function_exists('get_capacity_totals_all')) {
    function get_capacity_totals_all($event_id, $venue_id,  $vapp_size_id, $parking_id, $match_category_id, $status_name)
    {
        $status_id = get_approval_id($status_name);
        appLog('Helper::appHelper get_capacity_totals_all $status_id: ' . $status_id);
        // $query = VappRequest::where('event_id', $event_id)
        //     ->where('venue_id', $venue_id)
        //     ->where('parking_id', $parking_id)
        //     ->where('variation_id', $variation_id)
        //     ->where('vapp_size_id', $vapp_size_id)
        //     ->where('match_id', $match_id)
        //     ->where('request_status_id', $status_id)->get();

        $results = VappRequest::where('event_id', $event_id)
            ->where('venue_id', $venue_id)
            ->where('parking_id', $parking_id)
            ->where('vapp_size_id', $vapp_size_id)
            ->where('request_status_id', $status_id)
            ->where('match_category_id', getMatchCategoryIdByLabel('ALL'))
            ->get();

        appLog('event_id: ' . $event_id);
        appLog('venue_id: ' . $venue_id);
        appLog('vapp_size_id: ' . $vapp_size_id);
        appLog('parking_id: ' . $parking_id);
        appLog('match_category_id: ' . $match_category_id);
        appLog('status_name: ' . $status_name);

        appLog($results);
        $ret = $results->sum('approved_vapps');
        if ($ret) {
            return $ret;
        } else {
            return 0;
        }
    }
}

// get the totals of capacity for Venue STA
// event, venue, parking, match category of ALL, vapp size.
if (!function_exists('get_capacity_totals_sta_inf')) {
    function get_capacity_totals_sta_inf($event_id, $vapp_size_id, $parking_id, $status_name)
    {
        $status_id = get_approval_id($status_name);
        appLog('Helper::appHelper get_capacity_totals_sta_inf $status_id: ' . $status_id);
        // $query = VappRequest::where('event_id', $event_id)
        //     ->where('venue_id', $venue_id)
        //     ->where('parking_id', $parking_id)
        //     ->where('variation_id', $variation_id)
        //     ->where('vapp_size_id', $vapp_size_id)
        //     ->where('match_id', $match_id)
        //     ->where('request_status_id', $status_id)->get();
        $venueIds = [
            getVenueIdByLabel('STA'),
            getVenueIdByLabel('INF'),
        ];

        $results = VappRequest::where('event_id', $event_id)
            ->whereIn('venue_id', $venueIds)
            ->where('parking_id', $parking_id)
            ->where('vapp_size_id', $vapp_size_id)
            ->where('request_status_id', $status_id)
            ->get();

        appLog('event_id: ' . $event_id);
        appLog('vapp_size_id: ' . $vapp_size_id);
        appLog('parking_id: ' . $parking_id);
        appLog('status_name: ' . $status_name);

        appLog($results);
        $ret = $results->sum('approved_vapps');
        if ($ret) {
            return $ret;
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_inv_totals')) {
    function get_inv_totals($event_id, $venue_id,  $vapp_size_id, $parking_id, $variation_id,  $match_category_id, $match_id, $status_name)
    {
        $status_id = get_approval_id($status_name);
        appLog('Helper::appHelper get_inv_totals $status_id: ' . $status_id);
        $results = VappRequest::where('event_id', $event_id)
            ->where('venue_id', $venue_id)
            ->where('parking_id', $parking_id)
            ->where('variation_id', $variation_id)
            ->where('vapp_size_id', $vapp_size_id)
            ->where('request_status_id', $status_id)
            ->when($match_category_id === getMatchCategoryIdByLabel('MATCH'), function ($q) use ($match_id) {
                $q->where('match_id', $match_id);
            })
            ->when($match_category_id === getMatchCategoryIdByLabel('ALL'), function ($q) {
                $q->where('match_category_id', getMatchCategoryIdByLabel('ALL'));
            })->get();

        appLog('event_id: ' . $event_id);
        appLog('venue_id: ' . $venue_id);
        appLog('vapp_size_id: ' . $vapp_size_id);
        appLog('parking_id: ' . $parking_id);
        appLog('variation_id: ' . $variation_id);
        appLog('status_name: ' . $status_name);
        appLog('match_category_id: ' . $match_category_id);
        appLog('match_id: ' . $match_id);

        appLog($results);
        $ret = $results->sum('approved_vapps');
        if ($ret) {
            return $ret;
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_inv_totals_match')) {
    function get_inv_totals_match($event_id, $venue_id,  $vapp_size_id, $parking_id, $variation_id,  $match_category_id, $match_id, $status_name)
    {
        $status_id = get_approval_id($status_name);
        appLog('Helper::appHelper get_totals $status_id: ' . $status_id);
        $results = VappRequest::where('event_id', $event_id)
            ->where('venue_id', $venue_id)
            ->where('parking_id', $parking_id)
            ->where('variation_id', $variation_id)
            ->where('vapp_size_id', $vapp_size_id)
            ->where('match_category_id', $match_category_id)
            ->where('match_id', $match_id)
            ->where('request_status_id', $status_id)->get();

        appLog('event_id: ' . $event_id);
        appLog('venue_id: ' . $venue_id);
        appLog('vapp_size_id: ' . $vapp_size_id);
        appLog('parking_id: ' . $parking_id);
        appLog('variation_id: ' . $variation_id);
        appLog('status_name: ' . $status_name);
        appLog('match_category_id: ' . $match_category_id);
        appLog('match_id: ' . $match_id);

        appLog($results);
        $ret = $results->sum('approved_vapps');
        if ($ret) {
            return $ret;
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_inv_totals_all')) {
    function get_inv_totals_all($event_id, $venue_id,  $vapp_size_id, $parking_id, $variation_id, $match_category_id, $status_name)
    {
        $status_id = get_approval_id($status_name);
        appLog('Helper::appHelper get_inv_totals_all $status_id: ' . $status_id);
        $results = VappRequest::where('event_id', $event_id)
            ->where('venue_id', $venue_id)
            ->where('parking_id', $parking_id)
            ->where('variation_id', $variation_id)
            ->where('vapp_size_id', $vapp_size_id)
            ->where('match_category_id', $match_category_id)
            ->where('request_status_id', $status_id)->get();

        appLog('event_id: ' . $event_id);
        appLog('venue_id: ' . $venue_id);
        appLog('vapp_size_id: ' . $vapp_size_id);
        appLog('parking_id: ' . $parking_id);
        appLog('variation_id: ' . $variation_id);
        appLog('status_name: ' . $status_name);
        appLog('match_category_id: ' . $match_category_id);

        appLog($results);
        $ret = $results->sum('approved_vapps');
        if ($ret) {
            return $ret;
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_parking_capacity')) {
    function get_parking_capacity($event_id, $venue_id,  $vapp_size_id, $parking_id, $variation_id, $status_name)
    {
        $status_id = get_approval_id($status_name);
        appLog('Helper::appHelper get_parking_capacity $status_id: ' . $status_id);

        $results = ParkingCapacity::with('parking_master.vapp_sizes')
            ->whereHas('parking_master.vapp_sizes', function ($q) use ($vapp_size_id) {
                $q->where('vapp_sizes.id', $vapp_size_id); // or any filter
            })->where('event_id', $event_id)
            ->where('venue_id', $venue_id)
            ->where('parking_id', $parking_id);

        $ret = $results->sum('capacity');

        // $results = ParkingCapacity::where('event_id', $event_id)
        //     ->where('venue_id', $venue_id)
        //     ->where('parking_id', $parking_id)
        //     ->where('variation_id', $variation_id)
        //     ->where('vapp_size_id', $vapp_size_id)
        //     ->where('request_status_id', $status_id)->get();

        appLog('event_id: ' . $event_id);
        appLog('venue_id: ' . $venue_id);
        appLog('vapp_size_id: ' . $vapp_size_id);
        appLog('parking_id: ' . $parking_id);
        appLog('variation_id: ' . $variation_id);
        appLog('status_name: ' . $status_name);

        // appLog(json_encode($query));
        // $ret = $query->sum('approved_vapps');
        if ($ret) {
            return $ret;
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_project_progress')) {

    function get_project_progress($id)
    {
        $project = Event::findOrFail($id);

        $progress_value = 0;
        $task_count = $project->tasks->count();
        $task_progress_sum = $project->tasks->sum('progress');

        if ($task_count) {
            $progress_value = round(($task_progress_sum / $task_count), 2);
        }

        appLog('Helper::appHelper $task_count: ' . $task_count);
        appLog('Helper::appHelper $task_progress_sum: ' . $task_progress_sum);
        appLog('Helper::appHelper $progress_value: ' . $progress_value);

        return $progress_value;
    }
}

if (!function_exists('generateSecurePassword')) {
    function generateSecurePassword($length = 12)
    {
        $lowercase    = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers      = '0123456789';
        $specialChars = '!@#$%^&*()-_=+<>?';

        // Ensure at least one of each
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];

        // Fill the rest
        $all = $lowercase . $uppercase . $numbers . $specialChars;
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Shuffle to randomize order
        return str_shuffle($password);
    }
}

if (!function_exists('getPublicIp')) {
    function getPublicIp()
    {
        try {
            return Http::timeout(5)
                ->get('https://api64.ipify.org?format=json')
                ->json()['ip'];
        } catch (\Exception $e) {
            return 'Unable to fetch public IP';
        }
    }
}
