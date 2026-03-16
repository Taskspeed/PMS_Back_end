<?php

namespace App\Listeners;

use App\Events\UnitWorkPlanEvent;
use App\Models\UnitWorkPlan as UnitWorkPlanCreate;
use App\Models\UnitWorkPlanRecord;
use Illuminate\Support\Facades\Log;


class UnitWorkPlan
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
    public function handle(UnitWorkPlanEvent $event): void
    {
        $unitworkplan = $event->unitworkplan;

        $employee = $unitworkplan->employee;

        // ✅ Just save directly
        $unitworkplan =  UnitWorkPlanCreate::create([
            'office_name' => $employee->office,
            'year'        => $unitworkplan->year,
            'semester'    => $unitworkplan->semester,
            // 'status'      => 'Pending',
        ]);

      $unitworkplan_record  = UnitWorkPlanRecord::create([
            'unitworkplan_id' => $unitworkplan->id,
            'date' => now()->format('m-d-Y'),
            'status'    =>  'Draft',
            'remarks'    => 'Created',
            'reviewed_by'    => NULL,
        ]);



        Log::info('UnitWorkPlanRecord saved successfully!');
    }
}
