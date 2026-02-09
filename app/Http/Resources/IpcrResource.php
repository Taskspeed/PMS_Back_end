<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IpcrResource extends JsonResource
{
    public static $wrap = null;
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'control_no' => $this->ControlNo,
            'name'       => $this->name,
            'division' => $this->division,
            'section' => $this->section,
            'unit' => $this->unit,
            'position_id' => $this->position_id,
            'office_id' => $this->office_id,
            'office2' => $this->office2,
            'group' => $this->group,
            'tblStructureID' => $this->tblStructureID,
            'sg' => $this->sg,
            'level' => $this->level,
            'positionID' => $this->positionID,
            'itemNo' => $this->itemNo,
            'pageNo' => $this->pageNo,
            'position' => $this->position,
            'office' => $this->office,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,


            'target_periods' => $this->targetPeriods->map(function ($period) {

                return [
                    'id' => $period->id,
                    'control_no' => $period->control_no,
                    'year'      => $period->year,
                    'semester'  => $period->semester,
                    'office' => $period->office,
                    'office2' => $period->office2,
                    'division' => $period->division,
                    'section' => $period->section,
                    'unit' => $period->unit,
                    'status' => $period->status,
                    'created_at' => $period->created_at,
                    'updated_at' => $period->updated_at,

                    'performance_standards' => $period->performanceStandards->map(function ($standard) {
                        return [
                            'id' => $standard->id,
                            'target_period_id' => $standard->target_period_id,
                            'category' => $standard->category,
                            'mfo'      => $standard->mfo,
                            'output'   => $standard->output,
                            'success_indicator'   => $standard->success_indicator,
                            'core'   => $standard->core,
                            'technical'   => $standard->technical,
                            'leadership'   => $standard->leadership,
                            'monthly_ratings' => $standard->monthly_ratings,
                        ];
                    })
                ];
            })
        ];
    }
}
