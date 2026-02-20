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

        return [
            'office' => [
                'name' => $data->office_name,  // ✅ from resource, not $request

                'unitworkplan_status' => $data->unitworkplan_status, // ✅ HERE
                
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
                                'id'                    => $tp->id,
                                'control_no'            => $tp->control_no,
                                'semester'              => $tp->semester,
                                'year'                  => $tp->year,
                                'status'                => $tp->status,
                                'performance_standards' => $tp->performanceStandards,
                            ])->values(),
                        ];
                    })->values(),
            ],
        ];
    }
    }
