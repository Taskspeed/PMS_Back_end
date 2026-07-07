<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitWorkPlanOrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public static $wrap = null; // ✅ removes the "data" wrapper

    public function toArray(Request $request): array
    {
        $data                       = $this->resource;
        $officeEmployee             = $data->officeEmployee;
        $officeTargetPeriod         = $data->officeTargetPeriod;
        $organizationTargetPeriods  = $data->organizationTargetPeriods;
        $opcr = $data->opcr;
        $unitworkplan = $data->unitworkplan;

        return [
            // for the Department Head
            'office' => [
                'name' => $data->office_name,  // ✅ from resource, not $request

                // unit work plan
                'unitworkplan' => $unitworkplan ? [
                    'unitworkplan_id'       => $unitworkplan->id,
                    'semester' => $unitworkplan->semester,
                    'year'     => $unitworkplan->year,
                    'office_name' => $unitworkplan->office_name,
                    'unitworkplan_lastest_record' => $unitworkplan->unitworkplanLastestRecord ? [
                        'id'            => $unitworkplan->unitworkplanLastestRecord->id,
                        'date'          => $unitworkplan->unitworkplanLastestRecord->date,
                        'status'        => $unitworkplan->unitworkplanLastestRecord->status,
                        'remarks'        => $unitworkplan->unitworkplanLastestRecord->remarks,
                    ] : null,
                ] : null,

                // opcr
                'opcr' => $opcr ? [
                    'office_opcr_id'       => $opcr->id,
                    'semester' => $opcr->semester,
                    'year'     => $opcr->year,
                    'office_name' => $opcr->office_name,
                    'office_opcr_record_lastest_record' => $opcr->officeOpcrRecordLastestRecord ? [
                        'id'            => $opcr->officeOpcrRecordLastestRecord->id,
                        'date'          => $opcr->officeOpcrRecordLastestRecord->date,
                        'status'        => $opcr->officeOpcrRecordLastestRecord->status,
                    ] : null,
                ] : null,

                'employee' => [
                    'ControlNo' => $officeEmployee->ControlNo,
                    'name'      => $officeEmployee->name,
                    'rank'      => $officeEmployee->rank,
                    'position'  => $officeEmployee->position,
                    'sg'        => $officeEmployee->sg,
                    'level'     => $officeEmployee->level,
                ],
                'target_periods' => $officeTargetPeriod
                    ? collect([$officeTargetPeriod])->map(fn($tp) => [
                        'id'                   => $tp->id,
                        'control_no'           => $tp->control_no,
                        'semester'             => $tp->semester,
                        'year'                 => $tp->year,
                        'status'               => $tp->status,
                        'performance_standards' => $tp->performanceStandards,
                    ])
                    : [],
            ],

            // for the employee
            'organization' => [
                'name'      => $data->organization, // ✅ from resource, not $request
                'employees' => $organizationTargetPeriods
                    ->groupBy('control_no')
                    ->map(function ($periods) {
                        $employee = $periods->first()->employee;
                        return [
                            'employee' => [
                                'ControlNo' => $employee->ControlNo,
                                'name'      => $employee->name,
                                'rank'      => $employee->rank,
                                'position'  => $employee->position,
                                'sg'        => $employee->sg,
                                'level'     => $employee->level,
                            ],
                            'target_periods' => $periods->map(fn($tp) => [
                                'employee_target_period_id'      => (int)$tp->id,
                                'control_no'            => $tp->control_no,
                                'semester'              => $tp->semester,
                                'year'                  => $tp->year,
                                'status'                => $tp->status,
                                'performance_standards' => $tp->performanceStandards,
                            ])->values(),
                        ];
                    })->values(),
            ],

            // 'unitworkplan_status' => [
            //     $unitworkplan_status
            // ]
        ];
    }
}
