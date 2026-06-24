<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetPeriodRatingResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'target_period_id'                   => $this->id,
            'performance_standards' => $this->performanceStandards->map(fn($standard) => [
                'performance_standard_id'                   => $standard->id,
                // 'target_period_id'     => $standard->target_period_id,
                'category'             => $standard->category,
                'mfo'                  => $standard->mfo,
                'output'               => $standard->output,
                'output_name'          => $standard->output_name,
                'performance_indicator' => $standard->performance_indicator,
                'success_indicator'    => $standard->success_indicator,
                'required_output'      => $standard->required_output,
                'performance_rating'   => $this->mapPerformanceRating($standard->performanceRating),
                'configurations'       => $standard->configurations->map(fn($config) => [
                    'id'                   => $config->id,
                    'performance_standard_id' => $config->performance_standard_id,
                    'targetOutput'         => $config->targetOutput,
                    'quantityIndicator'    => $config->quantityIndicator,
                    'timelinessIndicator'  => $config->timelinessIndicator,
                    'range'                => $config->range,
                    'date'                 => $config->date,
                    'description'          => $config->description,
                ]),
            ]),
        ];
    }

    private function mapPerformanceRating($performanceRating): array|object
    {
        // when grouped by week (Collection of week => ratings)
        if ($performanceRating instanceof \Illuminate\Support\Collection && $performanceRating->keys()->first() && str_starts_with((string)$performanceRating->keys()->first(), 'week')) {
            $grouped = [];
            foreach ($performanceRating as $week => $ratings) {
                $grouped[$week] = $ratings->map(fn($rating) => $this->mapRating($rating))->values();
            }
            return $grouped;
        }

        // fallback: flat array (no month/year filter)
        return $performanceRating->map(fn($rating) => $this->mapRating($rating))->values()->toArray();
    }

    private function mapRating($rating): array
    {
        return [
            'performanance_rating_id'                      => $rating->id,
            // 'performance_standard_id' => $rating->performance_standard_id,
            'control_no'              => $rating->control_no,
            'date'                    => $rating->date,
            'quantity_actual'         => $rating->quantity_actual,
            'effectiveness_actual'    => $rating->effectiveness_actual,
            'timeliness_actual'       => $rating->timeliness_actual,
            'status'                  => $rating->status,
            'dropdown_rating'         => $rating->dropdownRating->map(fn($d) => [
                'dropdown_rating_id'                    => $d->id,
                // 'performance_rating_id' => $d->performance_rating_id,
                'quantity'              => $d->quantity,
                'effectiveness'         => $d->effectiveness,
                'timeliness'            => $d->timeliness,
                'created_at'            => $d->created_at,
                'updated_at'            => $d->updated_at,
            ]),
        ];
    }
}