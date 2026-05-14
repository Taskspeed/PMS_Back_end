<?php

namespace App\Listeners;

use App\Events\UnitWorkPlanEvent;
use App\Models\UnitWorkPlan as UnitWorkPlanCreate;
use App\Models\UnitWorkPlanRecord;
use Carbon\Carbon;
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
        $user = $event->user;

        $employee = $unitworkplan->employee;

        // ✅ Just save directly
        $newunitworkplan =  UnitWorkPlanCreate::create([
            'office_id' => $employee->office_id,
            'office_name' => $employee->office,
            'year'        => $unitworkplan->year,
            'semester'    => $unitworkplan->semester,
            // 'status'      => 'Pending',
        ]);

      $unitworkplan_record  = UnitWorkPlanRecord::create([
            'unitworkplan_id' => $newunitworkplan->id,
            'date' => Carbon::now()->format('Y-m-d'), // ✅ standard DB date format
            'status'    =>  'Draft',
            'remarks'    => 'Created',
            'processed_by'    => $user->id,
           'processed_by_name'    => $user->name,
        ]);



        Log::info('UnitWorkPlanRecord saved successfully!');
    }
}
