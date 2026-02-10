<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\PerformanceStandard;
use Illuminate\Support\Facades\Log;

class MonthlyPerformanceService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }
    // public static $wrap = null;


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
                            'performanceStandards.performanceRating'
                        ]);
                }
            ])
            ->first();

        if (! $employee) {
            return null;
        }

        $employee->targetPeriods->each(function ($period) {
            $period->performanceStandards->each(function ($standard) {
                $grouped = $this->groupRatingsByMonthlySummary($standard->performanceRating);

                $standard->monthly_ratings = $grouped;
                $standard->makeHidden('performanceRating');
            });
        });

        return $employee; // ✅ REQUIRED
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

        return $standards;
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
                    'month' => $date->format('F Y'),

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
                    'month' => $date->format('F Y'),

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

            return $standards;
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

        // 🔹 Prevent division by zero
        $quantityTotal = $totals['quantity_total'] ?: 1;

        // Calculate quantity_rating based on standard_outcomes
        $quantityRating = $this->getQuantityRating($totals['quantity_total'], $standardOutcomes);

        return [
            'totals' => $totals,
            // 'ratings' => [
            //     'quantity_rating' => $quantityRating,'effectiveness_rating' => round( $quantityTotal / $totals['effectiveness_total'], 2),
            //     'timeliness_rating' => round($quantityTotal / $totals['timeliness_total'], 2),
            // ],

           // Timeliness and Effectiveness Rating Calculation
           // Rating = Total Actual / Total Quantity
            'ratings' => [
                'quantity_rating' => (int) $quantityRating,
                'effectiveness_rating' => (int) round(
                    $totals['effectiveness_total']  /   $quantityTotal
                ),
                'timeliness_rating' => (int) round(
                    $totals['timeliness_total'] /   $quantityTotal
                ),
            ],

        ];
    }

    //getting the quantity rating based on the standard outcomes
    private function getQuantityRating($quantityTotal, $standardOutcomes)
    {
        // Sort outcomes by rating descending (5 -> 1)
        $outcomes = collect($standardOutcomes)->sortByDesc('rating');

        foreach ($outcomes as $outcome) {
            $quantity = data_get($outcome, 'quantity');

            // Skip null quantities
            if (is_null($quantity)) {
                continue;
            }

            // Handle "X and above" (e.g., "65 and above")
            if (preg_match('/^(\d+)\s+and\s+above$/i', $quantity, $matches)) {
                $threshold = (int) $matches[1];
                if ($quantityTotal >= $threshold) {
                    return (int) data_get($outcome, 'rating');
                }
            }
            // Handle "X and below" (e.g., "25 and below")
            elseif (preg_match('/^(\d+)\s+and\s+below$/i', $quantity, $matches)) {
                $threshold = (int) $matches[1];
                if ($quantityTotal <= $threshold) {
                    return (int) data_get($outcome, 'rating');
                }
            }
            // Handle range "X-Y" (e.g., "57-64")
            elseif (preg_match('/^(\d+)-(\d+)$/', $quantity, $matches)) {
                $min = (int) $matches[1];
                $max = (int) $matches[2];
                if ($quantityTotal >= $min && $quantityTotal <= $max) {
                    return (int) data_get($outcome, 'rating');
                }
            }
            // Handle exact number (e.g., "3", "2", "1", "0")
            elseif (is_numeric($quantity)) {
                if ($quantityTotal >= (int) $quantity) {
                    return (int) data_get($outcome, 'rating');
                }
            }
        }

        // Default to lowest rating if no match found
        return 1;
    }
}
