<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeOpcrPendingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public static $wrap = null;


    public function toArray(Request $request): array
    {
        $latestRecord = $this->officeOpcrRecordLastestRecord;
        $officeHead   = $this->officeHead;
        // $targetPeriod = $officeHead?->officeHeadTargetPeriod;

        return [
            'id'          => $this->id,
            'office_name' => $this->office_name,
            'semester'    => $this->semester,
            'year'        => $this->year,
            'date'        => $latestRecord?->date,
            'status'      => $latestRecord?->status,
            'office_head_name' => $officeHead->name,
            'control_no' => $officeHead->ControlNo,
            // 'target_period_id'    => $targetPeriod->id,
            //     'control_no' => $targetPeriod->control_no,
            // 'target_period_semester' => $targetPeriod->semester,
            // 'target_period_year' => $targetPeriod->year,
            // 'target_period'    => $targetPeriod ? [
            //     'target_period_id'    => $targetPeriod->id,
            //     'control_no' => $targetPeriod->control_no,
            //     'semester' => $targetPeriod->semester,
            //     'year' => $targetPeriod->year,
            // ] : null,

        ];
    }
}
