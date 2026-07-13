<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetPeriodRatingWeeksResource extends JsonResource
{
    public static $wrap = null;

    protected $month;
    protected $year;

    public function __construct($resource, $month = null, $year = null)
    {
        parent::__construct($resource);
        $this->month = $month;
        $this->year  = $year;
    }

   public function toArray(Request $request): array
{
    return [
        'target_period_id'      => $this->id,
        // 'performance_standards' => $this->performanceStandards->map(fn($standard) => [
        //     'performance_standard_id' => $standard->id,
        //     'configurations'          => $standard->configurations->map(fn($config) => [
        //         'id'                      => $config->id,
        //         'performance_standard_id' => $config->performance_standard_id,
        //         'targetOutput'            => $config->targetOutput,
        //         'quantityIndicator'       => $config->quantityIndicator,
        //         'timelinessIndicator'     => $config->timelinessIndicator,
        //         'range'                   => $config->range,
        //         'date'                    => $config->date,
        //         'description'             => $config->description,
        //     ]),
        // ]),
        'rating_weeks' => $this->mapRatingWeeks($this->rating_weeks ?? collect()),
    ];
}

private function mapPerformanceRating($performanceRating, int $standardId, $month, $year): array|object
{
    if ($performanceRating instanceof \Illuminate\Support\Collection && $performanceRating->keys()->first() && str_starts_with((string)$performanceRating->keys()->first(), 'week')) {
        $grouped = [];

        foreach ($performanceRating as $week => $data) {
            $ratingWeek = $data['rating_week'] ?? null;
            $ratings    = $data['ratings'];

            if ($ratingWeek) {
                $grouped[$week] = [
                    'performance_standard_id' => (int)$ratingWeek->performance_standard_id,
                       'latest_update' => $ratingWeek->updated_at->format('F d, Y'),
                    // 'rating_weeks_id' => $ratingWeek->id,
                    'status'          => $ratingWeek->status,
                ];
            } else {
                $grouped[$week] = [
                    'status' => $ratings->count() > 0 ? 'Pending' : 'No Rating',
                ];
            }
        }

        return $grouped;
    }

    // fallback: flat array (no month/year filter)
    return $performanceRating->map(fn($rating) => $this->mapRating($rating))->values()->toArray();
}

private function mapRatingWeeks($ratingWeeksCollection): array
{
    $grouped = [];

    foreach ($ratingWeeksCollection as $week => $data) {
        $ratingWeek = $data['rating_week'] ?? null;
        $ratings    = $data['ratings'] ?? collect();

        if ($ratingWeek) {
            $grouped[$week] = [
                'target_period_id' => (int) $ratingWeek->target_period_id,
                'latest_update'    => $ratingWeek->updated_at->format('F d, Y'),
                'status'           => $ratingWeek->status,
            ];
        } else {
            $grouped[$week] = [
                'status' => $ratings->count() > 0 ? 'Pending' : 'No Rating',
            ];
        }
    }

    return $grouped;
}
}
