<?php

namespace App\Listeners;

use App\Events\TargetPeriodLockEvent;
use App\Models\TargetPeriodLock as TargetPeriodLockCreated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use function Symfony\Component\Clock\now;

class TargetPeriodLock
{
    /**
     * Create the event listener.
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Handle the event.
     */
    public function handle(TargetPeriodLockEvent $event): void
    {
        $target_period_lock = $event->target_period_lock;
        $user = $event->user;

        // if (!$user) {
        //     Log::error('User is null in TargetPeriodLockEvent');
        //     return;
        // }

        // ✅ Just save directly
        $target_period_lock =  TargetPeriodLockCreated::create([
            'semester' => $target_period_lock->semester, //
            'year'     => $target_period_lock->year,     //
            'status'     => 'Open',    // ✅ correct
            'date' => Carbon::now()->format('Y-m-d'), // ✅ standard DB date format
            'lock_by'    => $user?->id,


            // 'status'      => 'Pending',
        ]);


        Log::info('target period lock saved successfully!');
    }
}
