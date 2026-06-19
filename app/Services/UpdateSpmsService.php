<?php

namespace App\Services;

use App\Models\OfficeOpcrRecord;
use App\Models\TargetPeriodRecord;
use App\Models\UnitWorkPlanRecord;
use Illuminate\Contracts\Auth\Authenticatable;

class UpdateSpmsService
{
    // update unit work plan
    public function unitWorkPlan(?array $validatedData, Authenticatable $authUser)
    {
        $records = [];

        foreach ($validatedData['unitworkplan_id'] as $id) {
            $records[] = UnitWorkPlanRecord::create([
                'unitworkplan_id'   => $id,
                'date'              => now()->toDateString(),
                'status'            => $validatedData['status'],
                'remarks'           => $validatedData['remarks'] ?? null,
                'processed_by_name' => $authUser->name,
                'processed_by'      => $authUser->id,
            ]);
        }
        return $records;
    }

    // update opcr
    public function opcr(?array $validatedData, Authenticatable $authUser)
    {
       $records = [];

        foreach ($validatedData['office_opcr_id'] as $id) {
            $records[] = OfficeOpcrRecord::create([
                'office_opcr_id'   => $id,
                'date'              => now()->toDateString(),
                'status'            => $validatedData['status'],
                'remarks'           => $validatedData['remarks'] ?? null,
                'processed_by_name' => $authUser->name,
                'processed_by'      => $authUser->id,
            ]);
        }

        return $records;
    }

    // update ipcr
    public function ipcr(?array $validatedData, Authenticatable $authUser)
    {
        $records = [];

        foreach ($validatedData['ipcr_id'] as $id) {
            $records[] = TargetPeriodRecord::create([
                'target_period_id'   => $id,
                'date'              => now()->toDateString(),
                'status'            => $validatedData['status'],
                'remarks'           => $validatedData['remarks'] ?? null,
                'processed_by_name' => $authUser->name,
                'processed_by'      => $authUser->id,
            ]);
        }

        return $records;
    }
}
