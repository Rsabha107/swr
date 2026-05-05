<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Swr\SecondmentWeeklyReport;
use Illuminate\Auth\Access\Response;

class SecondmentWeeklyReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SecondmentWeeklyReport $report): bool
    {
        // SuperAdmin can view any report
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // Customer can only view their own reports
        return $report->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Customer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SecondmentWeeklyReport $report): bool
    {
        // SuperAdmin can update any report
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // Customer can only update their own draft reports
        return $report->user_id === $user->id && $report->canEdit();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SecondmentWeeklyReport $report): bool
    {
        // SuperAdmin can delete any report
        if ($user->hasRole('SuperAdmin')) {
            return true;
        }

        // Customer can only delete their own draft reports
        return $report->user_id === $user->id && $report->canEdit();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SecondmentWeeklyReport $report): bool
    {
        return $user->hasRole('SuperAdmin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SecondmentWeeklyReport $report): bool
    {
        return $user->hasRole('SuperAdmin');
    }
}
