<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class IpcrService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

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
                $grouped = $this->groupRatingsByMonthIpcr($standard->performanceRating);

                $standard->monthly_ratings = $grouped;
                $standard->makeHidden('performanceRating');
            });
        });

        return $employee; // ✅ REQUIRED
    }


    private function groupRatingsByMonthIpcr($ratings)
    {
        $grouped = [];

        // 🔹 accumulators for WHOLE PERIOD averages
        $wholeTotals = [
            'quantity' => 0,
            'effectiveness' => 0,
            'timeliness' => 0,
        ];

        $wholeCounts = [
            'quantity' => 0,
            'effectiveness' => 0,
            'timeliness' => 0,
        ];

        foreach ($ratings as $rating) {
            try {
                $date = Carbon::createFromFormat('m/d/Y', $rating->date);
            } catch (\Exception $e) {
                Log::warning("Invalid date format: {$rating->date}");
                continue;
            }

            $monthKey = $date->format('Y-m');
            $weekKey  = 'week' . $this->getWeekOfMonthIpcr($date);

            if (!isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [
                    'month' => $date->format('F Y'),

                    // sums
                    'quantity' => $this->initializeWeeksIpcr(),
                    'effectiveness' => $this->initializeWeeksIpcr(),
                    'timeliness' => $this->initializeWeeksIpcr(),

                    // counters
                    '_counts' => [
                        'quantity' => $this->initializeWeeksIpcr(),
                        'effectiveness' => $this->initializeWeeksIpcr(),
                        'timeliness' => $this->initializeWeeksIpcr(),
                    ]
                ];
            }

            // accumulate sums
            $grouped[$monthKey]['quantity'][$weekKey] += (int) $rating->quantity_target_rate;
            $grouped[$monthKey]['effectiveness'][$weekKey] += (int) $rating->effectiveness_criteria_rate;
            $grouped[$monthKey]['timeliness'][$weekKey] += (int) $rating->timeliness_range_rate;

            // increment counters
            $grouped[$monthKey]['_counts']['quantity'][$weekKey]++;
            $grouped[$monthKey]['_counts']['effectiveness'][$weekKey]++;
            $grouped[$monthKey]['_counts']['timeliness'][$weekKey]++;
        }

        // 🔹 Convert weekly sums → averages + compute whole-period averages
        foreach ($grouped as &$month) {
            foreach (['quantity', 'effectiveness', 'timeliness'] as $type) {

                $weekTotal = 0;
                $weekCount = 0;

                foreach ($month[$type] as $week => $value) {
                    $count = $month['_counts'][$type][$week];

                    if ($count > 0) {
                        $avg = round($value / $count, 2);
                        $month[$type][$week] = $avg;

                        $weekTotal += $avg;
                        $weekCount++;
                    } else {
                        $month[$type][$week] = 0;
                    }
                }

                // ✅ Monthly average (average of weekly averages)
                $monthAverage = $weekCount > 0
                    ? round($weekTotal / $weekCount, 2)
                    : 0;

                $month[$type]['average'] = $monthAverage;

                // 🔹 Add to WHOLE PERIOD accumulators
                if ($monthAverage > 0) {
                    $wholeTotals[$type] += $monthAverage;
                    $wholeCounts[$type]++;
                }
            }

            unset($month['_counts']);
        }

        // 🔹 WHOLE PERIOD AVERAGES (what you asked for)
        $wholeAverage = [
            'quantity' => $wholeCounts['quantity'] > 0
                ? round($wholeTotals['quantity'] / $wholeCounts['quantity'], 2)
                : 0,

            'effectiveness' => $wholeCounts['effectiveness'] > 0
                ? round($wholeTotals['effectiveness'] / $wholeCounts['effectiveness'], 2)
                : 0,

            'timeliness' => $wholeCounts['timeliness'] > 0
                ? round($wholeTotals['timeliness'] / $wholeCounts['timeliness'], 2)
                : 0,
        ];

        // 🔹 Final overall IPCR average (optional but recommended)
        $wholeAverage['overall'] = round(
            (
                $wholeAverage['quantity'] +
                $wholeAverage['effectiveness'] +
                $wholeAverage['timeliness']
            ) / 3,
            2
        );

        return [
            // 'monthly' => array_values($grouped),
            'average' => $wholeAverage
        ];
    }



    /**
     * Initialize weeks structure with zeros
     */
    private function initializeWeeksIpcr()
    {
        return [
            'week1' => 0,
            'week2' => 0,
            'week3' => 0,
            'week4' => 0,
            'week5' => 0, // For months with 29-31 days
            // 'total' => 0,
        ];
    }

    /**
     * Get week number of the month (1-5)
     */
    private function getWeekOfMonthIpcr(Carbon $date)
    {
        // More accurate week calculation
        return (int) ceil($date->day / 7);

        // Alternative: Use Carbon's weekOfMonth if you want ISO week standards
        // return $date->weekOfMonth;
    }
}
