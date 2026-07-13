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
            'date' => $latestRecord?->date 
            ? \Carbon\Carbon::parse($latestRecord->date)->format('F d, Y') 
            : null,
            'status'      => $latestRecord?->status,
            'office_head_name' => $officeHead?->name ?? 'Department Head not found',
            'control_no'       => $officeHead?->ControlNo ?? null,

        ];
    }
}
