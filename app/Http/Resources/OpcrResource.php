<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpcrResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'control_no' => $this->ControlNo,
            'name' => $this->name,

            'target_periods' => $this->whenLoaded('targetPeriods', function () {
                return $this->targetPeriods->map(function ($target) {

                    return [
                        'id' => $target->id,
                        'semester' => $target->semester,
                        'year' => $target->year,
                        'status' => $target->status,

                        'performance_standards' => $target->performanceStandards
                            ? $target->performanceStandards->map(function ($ps) {

                                return [
                                    'id' => $ps->id,
                                    'target_period_id' => $ps->target_period_id,
                                    'category' => $ps->category,
                                    'mfo' => $ps->mfo,
                                    'output' => $ps->output,
                                    'success_indicator' => $ps->success_indicator,
                                    'core' => $ps->core,
                                    'technical' => $ps->technical,
                                    'leadership' => $ps->leadership,

                                    // assuming hasOne
                                    'opcr' => $ps->opcr ? [
                                        'id' => $ps->opcr->id,
                                        'budget' => $ps->opcr->budget,
                                        'accountable' => $ps->opcr->accountable,
                                        'accomplishment' => $ps->opcr->accomplishment,
                                    ] : null,
                                ];
                            })
                            : [],
                    ];
                });
            }),
        ];
    }
}
