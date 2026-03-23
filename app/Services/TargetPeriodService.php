<?php

namespace App\Services;

use App\Models\OfficeOpcr;
use App\Models\TargetPeriod;
use App\Models\UnitWorkPlan;
use Carbon\Carbon;

class TargetPeriodService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // fetch the targetperiod of employee
    public function targetPeriod($controlNo)
    {


        $employeeTargetPeriods = TargetPeriod::select(
            'id',
            'control_no',
            'semester',
            'year',
            'status',
            'office_id'
        )->where('control_no', $controlNo)->get();


        if ($employeeTargetPeriods->isEmpty()) {
            return response()->json([
                'message' => 'No target period found for this employee.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message'      => 'Target period retrieved successfully.',
            'targetPeriod' => $employeeTargetPeriods
        ], 200);
    }


    public function getTargetPeriodWithStandardsAndRatings($targetPeriodId, $month = null, $year = null, $week = null)
    {
        $targetPeriod = TargetPeriod::select('id')
            ->where('id', $targetPeriodId)
            ->with([
                'performanceStandards' => function ($query) {
                    $query->select(
                        'id',
                        'target_period_id',
                        'category',
                        'mfo',
                        'output',
                        'output_name',
                        'performance_indicator',
                        'success_indicator',
                        'required_output',

                    )
                        ->with([
                            'standardOutcomes' => function ($query) {
                                $query->select(
                                    'id',
                                    'performance_standard_id',
                                    'rating',
                                    'quantity_target as quantity',
                                    'effectiveness_criteria as effectiveness',
                                    'timeliness_range as timeliness'
                                );
                            },
                            // fetch ALL ratings — filter in PHP below
                            'performanceRating' => function ($query) {
                                $query->select(
                                    'id',
                                    'performance_standard_id',
                                    'control_no',
                                    'date',
                                    'quantity_actual',
                                    'effectiveness_actual',
                                    'timeliness_actual'
                                )->with(['dropdownRating']);
                            },
                            'configurations' => function ($query) {
                                $query->select(
                                    'id',
                                    'performance_standard_id',
                                    'target_output as targetOutput',
                                    'quantity_indicator as quantityIndicator',
                                    'timeliness_indicator as timelinessIndicator',
                                    'timeliness_range as range',
                                    'timeliness_date as date',
                                    'timeliness_description as description'
                                );
                            }
                        ]);
                }
            ])
            ->first();

        if (!$targetPeriod) {
            return response()->json(['message' => 'Target period not found.'], 404);
        }

        // filter performance ratings in PHP if month, year, week are provided
        if ($month && $year && $week) {
            $range       = $this->getWeekRangeForMonth($month, $year, $week);
            $monthNumber = Carbon::createFromFormat('F', $month)->month;
            $dayStart    = $range[0];
            $dayEnd      = $range[1];

            $targetPeriod->performanceStandards->each(function ($standard) use ($monthNumber, $year, $dayStart, $dayEnd) {
                $standard->setRelation(
                    'performanceRating',
                    $standard->performanceRating->filter(function ($rating) use ($monthNumber, $year, $dayStart, $dayEnd) {
                        try {
                            // date format is mm/dd/yyyy
                            $date = Carbon::createFromFormat('m/d/Y', $rating->date);

                            return $date->month == $monthNumber
                                && $date->year  == $year
                                && $date->day   >= $dayStart
                                && $date->day   <= $dayEnd;
                        } catch (\Exception $e) {
                            return false;
                        }
                    })->values() // re-index the array
                );
            });
        }

        return response()->json($targetPeriod);
    }
// ```

// **Example — July 2026 calendar:**

// July 2026 starts on a Wednesday, so the actual weeks are:

// | Week | Days | Date Range |
// |------|------|------------|
// | week1 | Wed–Sat | July 1–4 |
// | week2 | Sun–Sat | July 5–11 |
// | week3 | Sun–Sat | July 12–18 |
// | week4 | Sun–Sat | July 19–25 |
// | week5 | Sun–Thu | July 26–31 |

// **Postman:**
// ```
// GET http://localhost:8000/api/target-period/71/ratings/July/2026/week1
// GET http://localhost:8000/api/target-period/71/ratings/July/2026/week2
// GET http://localhost:8000/api/target-period/71/ratings/July/2026/week3


    private function getWeekRangeForMonth($month, $year, $week)
    {
        // Get first day of the month
        $firstDay = Carbon::createFromFormat('F Y', "$month $year")->startOfMonth();

        // Get total days in the month
        $daysInMonth = $firstDay->daysInMonth;

        $weeks  = [];
        $day    = 1;
        $weekNo = 1;

        while ($day <= $daysInMonth) {
            $currentDate = Carbon::createFromFormat('Y-m-d', "$year-{$firstDay->month}-$day");

            // Week ends on Saturday (6) or last day of month
            $weekStart = $day;

            // Move to end of current week (Saturday)
            while ($day <= $daysInMonth && $currentDate->dayOfWeek !== Carbon::SATURDAY) {
                $day++;
                if ($day <= $daysInMonth) {
                    $currentDate = Carbon::createFromFormat('Y-m-d', "$year-{$firstDay->month}-$day");
                }
            }

            $weeks["week$weekNo"] = [$weekStart, $day];
            $day++;
            $weekNo++;
        }

        return $weeks[$week] ?? null;
    }

}
