<?php

namespace App\Providers;

use App\Events\UnitWorkPlanRecord;
use App\Listeners\UnitworkPlan;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    // protected $listen = [
    //     UnitWorkPlanRecord::class => [ // event
    //         UnitworkPlan::class, // listener -- storing unit work plan
    //     ],
    // ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // 👇 Add this
        // Event::listen(
        //     UnitWorkPlanRecord::class,
        //     UnitworkPlan::class,
        // );

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
