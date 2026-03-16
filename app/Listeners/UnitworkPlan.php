<?php

namespace App\Listeners;

use App\Events\UnitWorkPlanRecord;
use Illuminate\Support\Facades\Log;


class UnitworkPlan
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
    public function handle(UnitWorkPlanRecord $event): void
    {
        $unitworkplan = $event->unitworkplan;

        $employee = $unitworkplan->employee;

        // ✅ Just save directly
        \App\Models\UnitWorkPlanRecord::create([
            'office_name' => $employee->office,
            'year'        => $unitworkplan->year,
            'semester'    => $unitworkplan->semester,
            'status'      => 'Pending',
        ]);
        Log::info('UnitWorkPlanRecord saved successfully!');
    }
}
