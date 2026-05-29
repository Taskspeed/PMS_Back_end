<?php

namespace App\Services;

use App\Models\UnitWorkPlan;
use App\Models\UnitWorkPlanRecord;
use Illuminate\Support\Facades\Auth;

use function Symfony\Component\Clock\now;

class TrackerService
{
    // updating the unitworkplan
    public function unitworkplanStatus(?array $validated)
    {
        $user = Auth::user(); 

        $status = UnitWorkPlanRecord::create([
            'unitworkplan_id' => $validated['unitworkplan_id'],
            'processed_by' => $user->id,          
            'date' => now()->format('m-d-Y'),
            'status'  => $validated['status'],
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return $status;
    }
}
