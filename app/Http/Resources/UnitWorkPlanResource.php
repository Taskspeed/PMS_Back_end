<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitworkplanResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'rank'           => $this->rank,
            'division'       => $this->division,
            'section'        => $this->section,
            'unit'           => $this->unit,
            'position_id'    => $this->position_id,
            'office_id'      => $this->office_id,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'ControlNo'      => $this->ControlNo,
            'office2'        => $this->office2,
            'group'          => $this->group,
            'tblStructureID' => $this->tblStructureID,
            'sg'             => $this->sg,
            'level'          => $this->level,
            'positionID'     => $this->positionID,
            'itemNo'         => $this->itemNo,
            'pageNo'         => $this->pageNo,
            'position'       => $this->position,
            'office'         => $this->office,
            'status'         => $this->status,
            'job_title'      => $this->job_title,
            'target_periods' => $this->targetPeriods->map(fn($period) => [
                'id'         => $period->id,
                'control_no' => $period->control_no,
                'year'       => $period->year,
                'semester'   => $period->semester,
                'status'     => $period->status,
                'performance_standards' => $period->performanceStandards->map(function ($ps) {
                    $config = $ps->configurations->first();

                    return [
                        'performanceStandardId' => $ps->id,
                        'target_period_id'     => $ps->target_period_id,
                        'category'             => $ps->category,
                        'mfo'                  => $ps->mfo,
                        'output'               => $ps->output,
                        'core'                 => $ps->core ?? [],
                        'technical'            => $ps->technical ?? [],
                        'leadership'           => $ps->leadership ?? [],
                        'output_name'          => $ps->output_name,
                        'performance_indicator'=> $ps->performance_indicator ?? [],
                        'success_indicator'    => $ps->success_indicator,
                        'required_output'      => $ps->required_output,
                        'created_at'           => $ps->created_at,
                        'updated_at'           => $ps->updated_at,
                        'supervisory_control_no' => $ps->supervisory_control_no,
                        'ratings' => $ps->standardOutcomes->map(fn($o) => [
                            'ratingId'                    => $o->id,
                            'performance_standard_id' => $o->performance_standard_id,
                            'rating'                => $o->rating,
                            'quantity'              => $o->quantity_target,
                            'effectiveness'         => $o->effectiveness_criteria,
                            'timeliness'            => $o->timeliness_range,
                        ]),
                        'config' => $config ? [
                            'configurationId'        => $config->id,
                            'targetOutput'        => $config->target_output,
                            'quantityIndicator'   => $config->quantity_indicator,
                            'timelinessIndicator' => $config->timeliness_indicator,
                            'timelinessType'      => [
                                'range'       => (bool) $config->timeliness_range,
                                'date'        => (bool) $config->timeliness_date,
                                'description' => (bool) $config->timeliness_description,
                            ],
                        ] : null,
                    ];
                }),
            ]),
        ];
    }
}