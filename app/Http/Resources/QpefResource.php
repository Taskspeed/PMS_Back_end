<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\QpefService;

class QpefResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        // Calculate sub_totals and weighted scores
        $qpefService = new QpefService();
        $computation = $qpefService->computationQpef($this->control_no, $this->quarterly, $this->year); //$this->resource,
        // $control_no, $quarterly, $year
        return [
            'id' => $this->id,
            'control_no' => $this->control_no,
            'quarterly' => $this->quarterly,
            'year' => $this->year,

            // Job Performance with sub_total and weighted score
            'job_performance' => [
                'items' => $this->whenLoaded('jobPerformances'),
                'sub_total' => $computation['job_performance']['sub_total'],
                'weight' => $computation['job_performance']['weight'],
                'weighted_score' => $computation['job_performance']['weighted_score'],
            ],

            // Competencies Attitude with sub_total and weighted score
            'competencies_attitude' => [
                'items' => $this->whenLoaded('competenciesAttitudes'),
                'sub_total' => $computation['competencies_attitude']['sub_total'],
                'weight' => $computation['competencies_attitude']['weight'],
                'weighted_score' => $computation['competencies_attitude']['weighted_score'],
            ],

            // Physical Mental with sub_total and weighted score
            'physical_mental' => [
                'items' => $this->whenLoaded('physicalMentals'),
                'sub_total' => $computation['physical_mental']['sub_total'],
                'weight' => $computation['physical_mental']['weight'],
                'weighted_score' => $computation['physical_mental']['weighted_score'],
            ],

            // Recommendation Development
            'recommendation_development' => $this->whenLoaded('recommendationDevelopment'),

            // Total weighted score
            // 'total_weighted_score' => $computation['total_weighted_score'],

               'final_rating' => [
                'job_performance_weighted_score' => $computation['job_performance_weighted_score'],
                'competencies_attitude_weighted_score' => $computation['competencies_attitude_weighted_score'],
                'physical_mental_weighted_score' => $computation['physical_mental_weighted_score'],
                'final_rating' => $computation['final_rating'],

            ],
        ];
    }
}
