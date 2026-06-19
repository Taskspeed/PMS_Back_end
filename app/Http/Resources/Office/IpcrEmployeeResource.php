<?php

namespace App\Http\Resources\Office;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IpcrEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'ControlNo'  => $this->ControlNo,
            'name'       => $this->name,
            'office'     => $this->office,
            'job_title'  => $this->job_title,
            'office_id'  => $this->office_id,
            'target_periods' => $this->targetPeriods->map(function ($tp) {
                return [
                    'id'         => $tp->id,
                    'control_no' => $tp->control_no,
                    'semester'   => $tp->semester,
                    'year'       => $tp->year,
                    'status'     => $tp->ipcrLastestRecord?->status,
                    'remarks'    => $tp->ipcrLastestRecord?->remarks,
                ];
            }),
        ];
    }
}
