<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyPerformanceResource extends JsonResource
{
  


    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'target_period_id' => $this->target_period_id,
            'category' => $this->category,
            'mfo' => $this->mfo,
            'output' => $this->output,
            'monthly_ratings' => $this->monthly_ratings,

        ];
    }
}
