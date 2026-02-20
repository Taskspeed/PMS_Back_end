<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Month;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\PerformanceRating;
use App\Models\PerformanceStandard;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AttendanceRequest;
use App\Models\TargetPeriod;

class IpcrService
{
    /**
     * Create a new class instance.
     */


    // public function __construct()
    // {
    //     //
    // }


    //---------------------------------------------------------------------------- Ipcr Data-----------------------------------------------------------------------------//

    //Ipcr Data of Employee
    public function getIpcrData($controlNo, $year, $semester)
    {
        $employee = Employee::where('ControlNo', $controlNo)
            ->with([
                'targetPeriods' => function ($q) use ($year, $semester) {
                    $q->where('year', $year)
                        ->where('semester', $semester)
                        ->with([
                            'performanceStandards.performanceRating:id,performance_standard_id,date,quantity_actual as quantity,effectiveness_actual as effectiveness,timeliness_actual as timeliness',
                            'performanceStandards.standardOutcomes:performance_standard_id,rating,quantity_target as quantity,effectiveness_criteria as effectiveness,timeliness_range as timeliness',


                        ]); //
                }
            ])
            ->first();

        if (! $employee) {
            return null;
        }

        $employee->targetPeriods->each(function ($period) {
            $period->performanceStandards->each(function ($standard) {


                $monthly = $this->groupRatingsByMonthlySummary(
                    $standard->performanceRating
                );

                $summary = $this->getComputationTotalAndRating(
                    $monthly['monthly'],
                    $standard->standardOutcomes
                );

                $average = $this->getAverageRating($summary['ratings']);



                // attach computed values (important)
                $standard->monthly_ratings = $monthly;
                $standard->totals = $summary['totals'];
                // $standard->ratings = $summary['ratings'];

                // merge average rating into ratings array
                $standard->ratings = array_merge(
                    $summary['ratings'],
                    ['average_rating' => $average]
                );

                //accomplishment
                $accomplishment = $this->accomplishment($standard, $summary);
                $standard->accomplishment = $accomplishment;



                // merge get the effectiveness and timeliness rating into accomplishment
                $standard->accomplishment = array_merge(
                    $accomplishment,
                    [
                        'effectiveness_rating' => $summary['ratings']['effectiveness_rating'],
                        'timeliness_rating' => $summary['ratings']['timeliness_rating'],
                    ]
                );

                $standard->makeHidden('performanceRating');
            });
        });

        return $employee;
    }

    //---------------------------------------------------------------------------- monthly-rate-----------------------------------------------------------------------------//


    public function getMonthly($targetPeriodId)
    {
        $standards = PerformanceStandard::select([
            'id',
            'target_period_id',
            'category',
            'mfo',
            'output'
        ])
            ->where('target_period_id', $targetPeriodId)
            ->with([
                'performanceRating' => function ($query) {
                    $query->select([
                        'id',
                        'performance_standard_id', // ✅ Include foreign key
                        'date',
                        'quantity_actual as quantity',
                        'effectiveness_actual as effectiveness',
                        'timeliness_actual as timeliness'
                    ]);
                }
            ])
            ->get();

        $standards->transform(function ($standard) {
            $grouped = $this->groupRatingsByMonthly($standard->performanceRating);

            // Store in a new property to preserve original relation
            $standard->monthly_ratings = $grouped;
            $standard->makeHidden('performanceRating');

            return $standard;
        });

        // 🔹 FETCH ATTENDANCE SEPARATELY
        $attendance = Month::with([
            'absents:month_id,week1,week2,week3,week4,week5,total_absent',
            'lates:month_id,week1,week2,week3,week4,week5,total_late'
        ])
            ->where('target_period_id', $targetPeriodId)
            ->get();


        // 🔹 RETURN BOTH AS SEPARATE DATA
        return [

            'standards' => $standards,
            'attendance' => $attendance ?? null
        ];
    }


    private function groupRatingsByMonthly($ratings)
    {
        $grouped = [];


        foreach ($ratings as $rating) {
            try {
                $date = Carbon::createFromFormat('m/d/Y', $rating->date);
            } catch (\Exception $e) {
                Log::warning("Invalid date format: {$rating->date}");
                continue;
            }

            $monthKey = $date->format('Y-m');
            $weekKey  = 'week' . $this->getWeekOfMonth($date);

            if (!isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [
                    'month' => $date->format('F'),

                    // WEEKLY TOTALS
                    'quantity' => $this->initializeWeeks(),
                    'effectiveness' => $this->initializeWeeks(),
                    'timeliness' => $this->initializeWeeks(),
                ];
            }

            // 🔹 ADD TOTALS (NO AVERAGE)
            $grouped[$monthKey]['quantity'][$weekKey] += (int) $rating->quantity;
            $grouped[$monthKey]['effectiveness'][$weekKey] += (int) $rating->effectiveness;
            $grouped[$monthKey]['timeliness'][$weekKey] += (int) $rating->timeliness;
        }

        // 🔹 OPTIONAL: Monthly TOTAL (sum of weeks)
        foreach ($grouped as &$month) {
            foreach (['quantity', 'effectiveness', 'timeliness'] as $type) {
                // $month[$type]['week_total'] = array_sum($month[$type]);
                $weeks = $month[$type];
                unset($weeks['week_total']);
                $month[$type]['week_total'] = array_sum($weeks);
            }
        }

        // 🔹 OVERALL TOTALS (NO AVERAGE)


        return [
            'monthly' => array_values($grouped),

        ];
    }



    /**
     * Initialize weeks structure with zeros
     */
    private function initializeWeeks()
    {
        return [
            'week1' => 0,
            'week2' => 0,
            'week3' => 0,
            'week4' => 0,
            'week5' => 0, // For months with 29-31 days
            // 'total'=> 0,
        ];
    }

    /**
     * Get week number of the month (1-5)
     */
    private function getWeekOfMonth(Carbon $date)
    {
        // More accurate week calculation
        return (int) ceil($date->day / 7);

        // Alternative: Use Carbon's weekOfMonth if you want ISO week standards
        // return $date->weekOfMonth;
    }

    //---------------------------------------------------------------------------- summary-monthly-rate-----------------------------------------------------------------------------//


    // get the summary-monthly-rate
    public function getSummaryMonthly($targetPeriodId)
    {
        $standards = PerformanceStandard::select([
            'id',
            'target_period_id',
            'category',
            'mfo',
            'output'
        ])
            ->where('target_period_id', $targetPeriodId)
            ->with([
                'standardOutcomes:performance_standard_id,rating,quantity_target as quantity',
                'performanceRating:id,performance_standard_id,date,quantity_actual as quantity,effectiveness_actual as effectiveness,timeliness_actual as timeliness'
            ])
            ->get();

        foreach ($standards as $standard) {




            $monthly = $this->groupRatingsByMonthlySummary(
                $standard->performanceRating
            );

            $summary = $this->getComputationTotalAndRating(
                $monthly['monthly'],
                $standard->standardOutcomes
            );

            // attach computed values (important)
            $standard->monthly_ratings = $monthly;
            $standard->totals = $summary['totals'];
            $standard->ratings = $summary['ratings'];
        }

        // 🔹 FETCH ATTENDANCE SEPARATELY
        $attendance = Month::with([
            'absents:month_id,week1,week2,week3,week4,week5,total_absent',
            'lates:month_id,week1,week2,week3,week4,week5,total_late'
        ])
            ->where('target_period_id', $targetPeriodId)
            ->get();

        // 🔹 RETURN BOTH AS SEPARATE DATA
        return [

            'standards' => $standards,
            'attendance' => $attendance ?? null
        ];
    }


    // get the summary-monthly-rate



    private function groupRatingsByMonthlySummary($ratings)
    {
        $grouped = [];

        foreach ($ratings as $rating) {
            try {
                $date = Carbon::createFromFormat('m/d/Y', $rating->date);
            } catch (\Exception $e) {
                Log::warning("Invalid date format: {$rating->date}");
                continue;
            }

            $monthKey = $date->format('Y-m');
            $weekKey  = 'week' . $this->getWeekOfMonth($date);

            if (!isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [
                    'month' => $date->format('F'), // Y for Year

                    // WEEKLY TOTALS
                    'quantity' => $this->initializeWeeks(),
                    'effectiveness' => $this->initializeWeeks(),
                    'timeliness' => $this->initializeWeeks(),
                ];
            }

            // 🔹 ADD TOTALS (NO AVERAGE)
            $grouped[$monthKey]['quantity'][$weekKey] += (int) $rating->quantity;
            $grouped[$monthKey]['effectiveness'][$weekKey] += (int) $rating->effectiveness;
            $grouped[$monthKey]['timeliness'][$weekKey] += (int) $rating->timeliness;
        }

        // 🔹 OPTIONAL: Monthly TOTAL (sum of weeks)
        foreach ($grouped as &$month) {
            foreach (['quantity', 'effectiveness', 'timeliness'] as $type) {
                $month[$type]['month_total'] = array_sum($month[$type]);

                // REMOVE week1–week5 from response
                unset(
                    $month[$type]['week1'],
                    $month[$type]['week2'],
                    $month[$type]['week3'],
                    $month[$type]['week4'],
                    $month[$type]['week5']
                );
            }
        }

        //


        return [
            'monthly' => array_values($grouped),

        ];
    }

    // get the monthly rate of employee Timeliness and Effectiveness
    private function getComputationTotalAndRating(array $monthlyRatings, $standardOutcomes)
    {
        $totals = [
            'quantity_total' => 0,
            'effectiveness_total' => 0,
            'timeliness_total' => 0,
        ];

        // get the total of quantity, effectiveness, and timeliness across all months
        foreach ($monthlyRatings as $month) {
            $totals['quantity_total'] += data_get($month, 'quantity.month_total', 0);
            $totals['effectiveness_total'] += data_get($month, 'effectiveness.month_total', 0);
            $totals['timeliness_total'] += data_get($month, 'timeliness.month_total', 0);
        }



        // Calculate quantity_rating based on standard_outcomes
        $quantityRating = $this->getQuantityRating($totals['quantity_total'], $standardOutcomes);

        $quantityTotal = $totals['quantity_total'];

        $effectivenessRating = $quantityTotal > 0
            ? round($totals['effectiveness_total'] / $quantityTotal)
            : 0;

        $timelinessRating = $quantityTotal > 0
            ? round($totals['timeliness_total'] / $quantityTotal)
            : 0;



        return [
            'totals' => $totals,
            // 'ratings' => [
            //     'quantity_rating' => $quantityRating,'effectiveness_rating' => round( $quantityTotal / $totals['effectiveness_total'], 2),
            //     'timeliness_rating' => round($quantityTotal / $totals['timeliness_total'], 2),
            // ],

            // Timeliness and Effectiveness Rating Calculation
            // Rating = Total Actual / Total Quantity
            'ratings' => [
                'quantity_rating'      => (int) $quantityRating,
                'effectiveness_rating' => (int) $effectivenessRating,
                'timeliness_rating'    => (int) $timelinessRating,
            ],

        ];
    }

    //getting the quantity rating based on the standard outcomes
    private function getQuantityRating($quantityTotal, $standardOutcomes)
    {
        if ($quantityTotal <= 0) {
            return 0;
        }

        $outcomes = collect($standardOutcomes)->sortByDesc('rating');

        foreach ($outcomes as $outcome) {
            $quantity = data_get($outcome, 'quantity');

            if (is_null($quantity)) {
                continue;
            }

            if (preg_match('/^(\d+)\s+and\s+above$/i', $quantity, $matches)) {
                $threshold = (int) $matches[1];
                if ($quantityTotal >= $threshold) {
                    return (int) data_get($outcome, 'rating');
                }
            } elseif (preg_match('/^(\d+)\s+and\s+below$/i', $quantity, $matches)) {
                $threshold = (int) $matches[1];
                if ($quantityTotal <= $threshold) {
                    return (int) data_get($outcome, 'rating');
                }
            } elseif (preg_match('/^(\d+)-(\d+)$/', $quantity, $matches)) {
                $min = (int) $matches[1];
                $max = (int) $matches[2];
                if ($quantityTotal >= $min && $quantityTotal <= $max) {
                    return (int) data_get($outcome, 'rating');
                }
            } elseif (is_numeric($quantity)) {
                if ($quantityTotal >= (int) $quantity) {
                    return (int) data_get($outcome, 'rating');
                }
            }
        }

        return 0;
    }



    // get the average  of employee base on the ratings
    // quantity rating + effectiveness rating + timeliness rating / 3
    private function getAverageRating($ratings)
    {

        $quantityRating = data_get($ratings, 'quantity_rating', 0);
        $effectivenessRating = data_get($ratings, 'effectiveness_rating', 0);
        $timelinessRating = data_get($ratings, 'timeliness_rating', 0);

        // Calculate average
        $average = ($quantityRating + $effectivenessRating + $timelinessRating) / 3;

        return round($average, 2); // Round to 2 decimal places


    }

    // getting the value of standard outcomes based on the rating
    private function getStandardOutcomeText($standardOutcomes, $rating)
    {
        $outcome = $standardOutcomes->firstWhere('rating', (string) $rating);

        return [
            'effectiveness' => $outcome->effectiveness ?? '',
            'timeliness'    => $outcome->timeliness ?? '',
        ];
    }

    private function accomplishment($standard, $summary)
    {
        $ratings = PerformanceRating::where('performance_standard_id', $standard->id)->get();

        $quantityTotal = $ratings->sum(function ($rating) {
            return is_numeric($rating->quantity_actual) ? $rating->quantity_actual : 0;
        });

        // ratings
        $effectivenessRating = $summary['ratings']['effectiveness_rating'];
        $timelinessRating    = $summary['ratings']['timeliness_rating'];

        // get text from standard outcomes
        $outcomeText = $this->getStandardOutcomeText(
            $standard->standardOutcomes,
            $effectivenessRating
        );

        // performance indicators → text
        $performanceIndicators = collect($standard->performance_indicator ?? [])
            ->implode(' and ');

        // build sentence
        $actualAccomplishment = trim(sprintf(
            '%d %s %s %s %s',
            $quantityTotal, // quantity total
            $standard->output_name, // outpput_name
            $performanceIndicators, // performance indicators
            $outcomeText['effectiveness'], // standard outcome for effectiveness
            $outcomeText['timeliness'] // standard outcome for timeliness
        ));

        return [
            'quantityTotal'         => $quantityTotal,
            'effectiveness_rating'  => $effectivenessRating,
            'timeliness_rating'     => $timelinessRating,
            'actual_accomplishment' => $actualAccomplishment,
        ];
    }

    //-----------------------approve the ipcr of the employee----------------------------------------------//

    public function approveIpcr($controlNo, $semester, $year, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approve,reject,review,re-submit',
        ]);

        // Get employee with office restriction
        $employee = Employee::where('ControlNo', $controlNo)
            // ->where('office_id', $this->officeId)
            ->firstOrFail(); // Throws exception if not found

        // Get the target period
        $targetPeriod = $employee->targetPeriods()
            ->where('year', $year)
            ->where('semester', $semester)
            ->firstOrFail(); // Throws exception if not found

        // Update only the target period
        $targetPeriod->update([
            'status' => $validated['status'],
        ]);

        return $targetPeriod->fresh(); // Return updated model
    }


    //---------------------------------------------------------------------------- late and absent -----------------------------------------------------------------------------//

    // storing attendance
    public function storeAttendance(AttendanceRequest $request)
    {
        $validated = $request->validated();
        $createdMonths = [];

        foreach ($validated['months'] as $monthData) {
            // Create the month record
            $month = Month::create([
                'target_period_id' => $validated['target_period_id'],
                'month' => $monthData['month'],
            ]);

            // Create absent record
            $month->absents()->create([
                'month_id' => $month->id,
                'week1' => $monthData['absent']['week1'] ?? 0,
                'week2' => $monthData['absent']['week2'] ?? 0,
                'week3' => $monthData['absent']['week3'] ?? 0,
                'week4' => $monthData['absent']['week4'] ?? 0,
                'week5' => $monthData['absent']['week5'] ?? 0,
                'total_absent' => $monthData['absent']['total_absent'] ?? 0,
            ]);

            // Create late record
            $month->lates()->create([
                'month_id' => $month->id,
                'week1' => $monthData['late']['week1'] ?? 0,
                'week2' => $monthData['late']['week2'] ?? 0,
                'week3' => $monthData['late']['week3'] ?? 0,
                'week4' => $monthData['late']['week4'] ?? 0,
                'week5' => $monthData['late']['week5'] ?? 0,
                'total_late' => $monthData['late']['total_late'] ?? 0,
            ]);

            // Load relationships
            $month->load(['absents', 'lates']);
            $createdMonths[] = $month;
        }

        return $createdMonths;
    }

    // status of ipcr of employee
    public function updateStatusIpcr($validateData, $targetPeriodId)
    {

        $ipcr = TargetPeriod::findOrFail($targetPeriodId);

        $ipcr->update($validateData); // updating

        return  $ipcr;
    }
}
