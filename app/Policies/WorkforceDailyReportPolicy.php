<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\Vapp\VappRequest;
use App\Models\Wdr\WorkforceDailyReport;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class WorkforceDailyReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */


    public function view(User $user, WorkforceDailyReport $workforceDailyReport): bool
    {
        // dd('inside policy WorkforceDailyReportPolicy::view user_id=' . $user->id . ' report_venue_id=' . $workforceDailyReport->venue_id);
        Log::info('inside policy WorkforceDailyReportPolicy::view user_id=' . $user->id . ' report_venue_id=' . $workforceDailyReport->venue_id);
        if ($user->hasRole('SuperAdmin')) return true;

        return $user->events()->where('events.id', $workforceDailyReport->event_id)->exists()
            && $user->venues()->where('venues.id', $workforceDailyReport->venue_id)->exists();
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkforceDailyReport $workforce_daily_report): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy WorkforceDailyReportPolicy::update user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy WorkforceDailyReportPolicy::update use_id=' . $user->id . ' report_created_by=' . $workforce_daily_report->created_by);
        return $user->id == $workforce_daily_report->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkforceDailyReport $workforce_daily_report): bool
    {
        //
        if (auth()->user()->hasAnyRole(['SuperAdmin'])) {
            appLog('inside policy WorkforceDailyReportPolicy::delete user has role SuperAdmin/Admin');
            return true;
        }
        appLog('inside policy WorkforceDailyReportPolicy::delete use_id=' . $user->id . ' report_created_by=' . $workforce_daily_report->created_by);
        return $user->id == $workforce_daily_report->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Employee $employee): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        //
    }
}
