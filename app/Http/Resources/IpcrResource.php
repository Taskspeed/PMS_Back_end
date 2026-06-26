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
            // employee details
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

            // target periods with nested performance standards
            'target_periods' => $this->targetPeriods->map(function ($period) {
                $latestRecord = $period->ipcrLastestRecord;

            // Received hr target
             $targetRecord = collect($period->ipcrRecord)
                    ->where('status', 'received')
                    ->sortByDesc('id')
                    ->first();

            // Received hr accomplishment
            $accomplishmentRecord = collect($period->ipcrRecord)
                ->where('status', 'Received_accomplishment')
                ->sortByDesc('id')
                ->first();

                // calibrated
             $calibratedRecord = collect($period->ipcrRecord)
            ->where('status', 'Calibrated_target')
            ->sortByDesc('id')
            ->first();


                return [
                    'id' => $period->id,
                    'control_no' => $period->control_no,
                    'year'      => $period->year,
                    'semester'  => $period->semester,
                    'status'     => $latestRecord?->status,   // ✅ fixed swap
                    'remarks'    => $latestRecord?->remarks,  // ✅ fixed swap


                    // performance standards with nested standard outcomes and monthly ratings
                    'performance_standards' => $period->performanceStandards->map(function ($standard) {
                        return [
                            'id' => $standard->id,
                            'target_period_id' => $standard->target_period_id,
                            'category' => $standard->category,
                            'mfo' => $standard->mfo,
                            'output' => $standard->output,
                            'output_name' => $standard->output_name,
                            'success_indicator' => $standard->success_indicator,
                            'performance_indicator' => $standard->performance_indicator,
                            'core' => $standard->core,
                            'technical' => $standard->technical,
                            'leadership' => $standard->leadership,
                            'standard_outcomes' => $standard->standardOutcomes,

                            'monthly_ratings' => $standard->monthly_ratings ?? null,
                            'totals' => $standard->totals ?? null,
                            'ratings' => $standard->ratings ?? null,


                            'accomplishment' => $standard->accomplishment ?? [
                                'quantityTotal' => 0,
                                'effectiveness_rating' => 0,
                                'timeliness_rating' => 0,
                            ],

                        ];
                    }),
   'final_rating' => $this->final_rating ?? null,
                    //  target 
                    'ipcr_target_record' => $targetRecord ? [
                        'id'           => $targetRecord->id,
                        'status'       => $targetRecord->status,
                        'remarks'      => $targetRecord->remarks,
                        'processed_by' => $targetRecord->processed_by,
                        'processed_by_info' => $targetRecord->processedBy ? [
                            'name'        => $targetRecord->processedBy->name,
                            'prefix'      => $targetRecord->processedBy->prefix,
                            'suffix'      => $targetRecord->processedBy->suffix,
                            'designation' => $targetRecord->processedBy->designation,
                        ] : null,
                        'date' => $targetRecord->date ?? null,
                    ] : null,

                    'ipcr_accomplishment_record' => $accomplishmentRecord ? [
                        'id'                => $accomplishmentRecord->id,
                        'status'            => $accomplishmentRecord->status,
                        'remarks'           => $accomplishmentRecord->remarks,
                        'processed_by'      => $accomplishmentRecord->processed_by,
                        'processed_by_info' => $targetRecord->processedBy ? [
                            'name'        => $targetRecord->processedBy->name,
                            'prefix'      => $targetRecord->processedBy->prefix,
                            'suffix'      => $targetRecord->processedBy->suffix,
                            'designation' => $targetRecord->processedBy->designation,
                        ] : null,
                        'date' => $targetRecord->date ?? null,
                    ] : null,

                    'ipcr_calibrated_record' => $calibratedRecord ? [
                        'id'                => $calibratedRecord->id,
                        'status'            => $calibratedRecord->status,
                        'remarks'           => $calibratedRecord->remarks,
                        'processed_by_info' => $targetRecord->processedBy ? [
                            'name'        => $targetRecord->processedBy->name,
                            'prefix'      => $targetRecord->processedBy->prefix,
                            'suffix'      => $targetRecord->processedBy->suffix,
                            'designation' => $targetRecord->processedBy->designation,
                        ] : null,
                        'date' => $targetRecord->date ?? null,
                    ] : null,
                        // ← add this
              
                ];
            })
        ];
    }
}
