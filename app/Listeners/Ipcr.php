<?php

namespace App\Listeners;

use App\Events\IpcrEvent;
use App\Models\TargetPeriodRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


class Ipcr
{
    public function handle(IpcrEvent $event): void
    {
        $targetPeriod = $event->ipcr; // This IS the TargetPeriod model
        $user = $event->user; // This IS the TargetPeriod model

        TargetPeriodRecord::create([
            'target_period_id' => $targetPeriod->id,  // ✅ access id directly
            'date'             => Carbon::now()->format('Y-m-d'), // ✅ standard DB date format
            'status'           => 'Draft',
            'remarks'          => 'Create',
            'processed_by'     => $user?->id,
            'processed_by_name'=> $user?->name,
        ]);
    }
}