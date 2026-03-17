<?php

namespace App\Services;

use App\Models\OfficeOpcr;
use App\Models\TargetPeriod;
use App\Models\UnitWorkPlan;

class TargetPeriodService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    public function targetPeriod($controlNo)
    {


        $employeeTargetPeriods = TargetPeriod::select(
            'id',
            'control_no',
            'semester',
            'year',
            'status',
            'office_id'
        )->where('control_no', $controlNo)->get();


        if ($employeeTargetPeriods->isEmpty()) {
            return response()->json([
                'message' => 'No target period found for this employee.',
                'data' => []
            ], 200);
        }

        foreach ($employeeTargetPeriods as $target) {

            // --- UnitWorkPlan check ---
            $unitWorkplanQuery = UnitWorkPlan::where('office_id', $target->office_id)
                ->where('semester', $target->semester)
                ->where('year', $target->year);

            $unitWorkplanExists = (clone $unitWorkplanQuery)->exists();



            $unitWorkplan = (clone $unitWorkplanQuery)
                ->whereHas('unitworkplanLastestRecord', function ($q) {
                    $q->where('status', 'Reviewed');
                })
                ->exists();


            // --- OPCR check ---
            $opcrQuery = OfficeOpcr::where('office_id', $target->office_id)
                ->where('semester', $target->semester)
                ->where('year', $target->year);

            $opcrExists = (clone $opcrQuery)->exists();

            $opcr = (clone $opcrQuery)
                ->whereHas('officeOpcrRecordLastestRecord', function ($q) {
                    $q->where('status', 'Reviewed');
                })
                ->exists();


            if ($unitWorkplan && $opcr) {
                $updated = TargetPeriod::where('id', $target->id)->update([
                    'status' => 'target period started'
                ]);

                $target->status = 'target period started';
            } else {
            }
        }

        $updatedTargetPeriods = TargetPeriod::select(
            'id',
            'control_no',
            'semester',
            'year',
            'status',
            'office_id'
        )->where('control_no', $controlNo)->get();


        return response()->json([
            'message'      => 'Target period retrieved successfully.',
            'targetPeriod' => $updatedTargetPeriods
        ], 200);
    }
}
