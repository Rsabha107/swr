<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Wdr\WorkforceDailyReport;
use App\Models\Swr\SecondmentWeeklyReport;
use App\Policies\WorkforceDailyReportPolicy;
use App\Policies\SecondmentWeeklyReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        WorkforceDailyReport::class => WorkforceDailyReportPolicy::class,
        SecondmentWeeklyReport::class => SecondmentWeeklyReportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
