<?php

namespace App\Services;

use App\Models\Qpef;
use Illuminate\Support\Facades\DB;

class QpefService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // Weight percentages
    const JOB_PERFORMANCE_WEIGHT = 0.40; // 40%
    const COMPETENCIES_WEIGHT = 0.50;    // 50%
    const PHYSICAL_MENTAL_WEIGHT = 0.10; // 10%

    public function createQpef($validated)
    {
        DB::beginTransaction();

        try {
            // Create main QPEF record
            $qpef = Qpef::create([
                'control_no' => $validated['control_no'],
                'quarterly' => $validated['quarterly'],
                'year' => $validated['year'],
            ]);

            // Create Job Performance records
            foreach ($validated['job_performance'] as $performance) {
                $qpef->jobPerformances()->create([
                    'indicators' => $performance['indicators'],
                    'rating' => $performance['rating'],
                    'remarks' => $performance['remarks'],

                ]);
            }

            // Create Competencies Attitude records
            foreach ($validated['competencies_attitude'] as $competency) {
                $qpef->competenciesAttitudes()->create([
                    'indicators' => $competency['indicators'],
                    'rating' => $competency['rating'],
                    'remarks' => $competency['remarks'],
                ]);
            }

            // Create Physical Mental records
            foreach ($validated['physical_mental'] as $physical) {
                $qpef->physicalMentals()->create([
                    'indicators' => $physical['indicators'],
                    'rating' => $physical['rating'],
                    'remarks' => $physical['remarks'],
                ]);
            }

            // Create Recommendation Development record
            if (isset($validated['recommendation_development'])) {
                $qpef->recommendationDevelopment()->create([
                    'for_retention' => $validated['recommendation_development']['for_retention'] ?? false,
                    'for_commendation' => $validated['recommendation_development']['for_commendation'] ?? false,
                    'for_improvement' => $validated['recommendation_development']['for_improvement'] ?? false,
                    'for_non_renewal' => $validated['recommendation_development']['for_non_renewal'] ?? false,
                    'recommendation' => $validated['recommendation_development']['recommendation'] ?? null,
                ]);
            }




            DB::commit();

            return $qpef;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    // updating Qpef
    public function updateQpef($qpefId, $validated)
    {
        DB::beginTransaction();

        try {

            $qpef = Qpef::with([
                'jobPerformances',
                'competenciesAttitudes',
                'physicalMentals',
                'recommendationDevelopment'
            ])->findOrFail($qpefId);

            // Update main QPEF
            // $qpef->update([
            //     'control_no' => $validated['control_no'],
            //     'quarterly'  => $validated['quarterly'],
            //     'year'       => $validated['year'],
            // ]);

            /*
        |--------------------------------------------------------------------------
        | JOB PERFORMANCE UPDATE OR CREATE
        |--------------------------------------------------------------------------
        */
            /*
|--------------------------------------------------------------------------
| JOB PERFORMANCE UPDATE OR CREATE
|--------------------------------------------------------------------------
*/
            /*
|--------------------------------------------------------------------------
| JOB PERFORMANCE UPDATE OR CREATE
|--------------------------------------------------------------------------
*/

            foreach ($validated['job_performance'] as $performance) {
                if (!empty($performance['id'])) {
                    // UPDATE existing record - cast to integer
                    $qpef->jobPerformances()
                        ->where('id', (int)$performance['id'])
                        ->update([
                            'indicators' => $performance['indicators'],
                            'rating'     => $performance['rating'],
                            'remarks'    => $performance['remarks'],
                        ]);
                } else {
                    // CREATE new record
                    $qpef->jobPerformances()->create([
                        'indicators' => $performance['indicators'],
                        'rating'     => $performance['rating'],
                        'remarks'    => $performance['remarks'],
                    ]);
                }
            }

            /*
|--------------------------------------------------------------------------
| COMPETENCIES UPDATE OR CREATE
|--------------------------------------------------------------------------
*/

            foreach ($validated['competencies_attitude'] as $competency) {
                if (!empty($competency['id'])) {
                    $qpef->competenciesAttitudes()
                        ->where('id', (int)$competency['id'])
                        ->update([
                            'indicators' => $competency['indicators'],
                            'rating'     => $competency['rating'],
                            'remarks'    => $competency['remarks'],
                        ]);
                } else {
                    $qpef->competenciesAttitudes()->create([
                        'indicators' => $competency['indicators'],
                        'rating'     => $competency['rating'],
                        'remarks'    => $competency['remarks'],
                    ]);
                }
            }

            /*
|--------------------------------------------------------------------------
| PHYSICAL MENTAL UPDATE OR CREATE
|--------------------------------------------------------------------------
*/

            foreach ($validated['physical_mental'] as $physical) {
                if (!empty($physical['id'])) {
                    $qpef->physicalMentals()
                        ->where('id', (int)$physical['id'])
                        ->update([
                            'indicators' => $physical['indicators'],
                            'rating'     => $physical['rating'],
                            'remarks'    => $physical['remarks'],
                        ]);
                } else {
                    $qpef->physicalMentals()->create([
                        'indicators' => $physical['indicators'],
                        'rating'     => $physical['rating'],
                        'remarks'    => $physical['remarks'],
                    ]);
                }
            }
            /*
        |--------------------------------------------------------------------------
        | RECOMMENDATION UPDATE OR CREATE
        |--------------------------------------------------------------------------
        */

            if (isset($validated['recommendation_development'])) {

                $qpef->recommendationDevelopment()->updateOrCreate(
                    ['qpef_id' => $qpef->id],
                    [
                        'for_retention'     => $validated['recommendation_development']['for_retention'] ?? false,
                        'for_commendation'  => $validated['recommendation_development']['for_commendation'] ?? false,
                        'for_improvement'   => $validated['recommendation_development']['for_improvement'] ?? false,
                        'for_non_renewal'   => $validated['recommendation_development']['for_non_renewal'] ?? false,
                        'recommendation'    => $validated['recommendation_development']['recommendation'] ?? null,
                    ]
                );
            }

            DB::commit();

            return $qpef->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    // Get qpef of employee $control_no, $quarterly, $year
    public function getEmployeeQpef($control_no, $quarterly, $year)
    {
        $employeeQpef = Qpef::with('jobPerformances', 'competenciesAttitudes', 'physicalMentals', 'recommendationDevelopment')
            ->where('control_no', $control_no)
            ->where('quarterly', $quarterly)
            ->where('year', $year)
            ->first(); // ✅ no more firstOrFail

        return $employeeQpef;
    }

    /**
     * Get the sub_total and weighted_score of job performance, competencies attitude,
     * and physical mental of the employee QPEF
     *
     * @param int $control_no
     * @param string $quarterly
     * @param int $year
     * @return array
     */
// public function computationQpef($control_no, $quarterly, $year)
// {
//     $qpef = $this->getEmployeeQpef($control_no, $quarterly, $year);

//     // Calculate Job Performance average
//     $jobPerformanceAvg = $qpef->jobPerformances->avg('rating') ?? 0;
//     $jobPerformanceWeighted = $jobPerformanceAvg * self::JOB_PERFORMANCE_WEIGHT;

//     // Calculate Competencies Attitude average
//     $competenciesAvg = $qpef->competenciesAttitudes->avg('rating') ?? 0;
//     $competenciesWeighted = $competenciesAvg * self::COMPETENCIES_WEIGHT;

//     // Calculate Physical Mental average
//     $physicalMentalAvg = $qpef->physicalMentals->avg('rating') ?? 0;
//     $physicalMentalWeighted = $physicalMentalAvg * self::PHYSICAL_MENTAL_WEIGHT;

//     // Calculate total weighted score
//     $totalWeightedScore = $jobPerformanceWeighted + $competenciesWeighted + $physicalMentalWeighted;

//     return [
//         'job_performance' => [
//             'sub_total' => round($jobPerformanceAvg, 2),
//             'weight' => self::JOB_PERFORMANCE_WEIGHT * 100 . '%',
//             'weighted_score' => round($jobPerformanceWeighted, 2),
//         ],
//         'competencies_attitude' => [
//             'sub_total' => round($competenciesAvg, 2),
//             'weight' => self::COMPETENCIES_WEIGHT * 100 . '%',
//             'weighted_score' => round($competenciesWeighted, 2),
//         ],
//         'physical_mental' => [
//             'sub_total' => round($physicalMentalAvg, 2),
//             'weight' => self::PHYSICAL_MENTAL_WEIGHT * 100 . '%',
//             'weighted_score' => round($physicalMentalWeighted, 2),
//         ],

//         'final_rating' => [
//             'job_performance_weighted_score' => round($jobPerformanceWeighted, 2),
//             'competencies_attitude_weighted_score' => round($competenciesWeighted, 2),
//             'physical_mental_weighted_score' => round($physicalMentalWeighted, 2),
//             'final_rating' => round($totalWeightedScore, 2),

//         ],

//     ];
// }

// /**
//  * Alternative: If you already have the QPEF object loaded
//  *
//  * @param Qpef $qpef
//  * @return array
//  */
public function computationQpef($control_no, $quarterly, $year)
{
        $qpef = $this->getEmployeeQpef($control_no, $quarterly, $year);
        // Calculate Job Performance average
        $jobPerformanceAvg = $qpef->jobPerformances->avg('rating') ?? 0;
        $jobPerformanceWeighted = $jobPerformanceAvg * self::JOB_PERFORMANCE_WEIGHT;

        // Calculate Competencies Attitude average
        $competenciesAvg = $qpef->competenciesAttitudes->avg('rating') ?? 0;
        $competenciesWeighted = $competenciesAvg * self::COMPETENCIES_WEIGHT;

        // Calculate Physical Mental average
        $physicalMentalAvg = $qpef->physicalMentals->avg('rating') ?? 0;
        $physicalMentalWeighted = $physicalMentalAvg * self::PHYSICAL_MENTAL_WEIGHT;

        // Calculate total weighted score
        $totalWeightedScore = $jobPerformanceWeighted + $competenciesWeighted + $physicalMentalWeighted;

        return [
            'job_performance' => [
                'sub_total' => round($jobPerformanceAvg, 2),
                'weight' => self::JOB_PERFORMANCE_WEIGHT * 100 . '%',
                'weighted_score' => round($jobPerformanceWeighted, 2),
            ],
            'competencies_attitude' => [
                'sub_total' => round($competenciesAvg, 2),
                'weight' => self::COMPETENCIES_WEIGHT * 100 . '%',
                'weighted_score' => round($competenciesWeighted, 2),
            ],
            'physical_mental' => [
                'sub_total' => round($physicalMentalAvg, 2),
                'weight' => self::PHYSICAL_MENTAL_WEIGHT * 100 . '%',
                'weighted_score' => round($physicalMentalWeighted, 2),
            ],
            // 'total_weighted_score' => round($totalWeightedScore, 2),

            'job_performance_weighted_score' => round($jobPerformanceWeighted, 2),
            'competencies_attitude_weighted_score' => round($competenciesWeighted, 2),
            'physical_mental_weighted_score' => round($physicalMentalWeighted, 2),
            'final_rating' => round($totalWeightedScore, 2),

        ];
    }
}
