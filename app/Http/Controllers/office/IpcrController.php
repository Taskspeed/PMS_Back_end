<?php
namespace App\Http\Controllers\office;

use Carbon\Carbon;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\PerformanceStandard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class IpcrController extends BaseController
{
    protected $user;
    protected $officeId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user     = Auth::user();
            $this->officeId = $this->user->office_id;

            return $next($request);
        });
    }

    // getting the ipcr of the employee based on controlno and year
    public function getIpcr($controlNo, $year, $semester)
    {
        $employee = Employee::where('ControlNo', $controlNo)
            ->where('office_id', $this->officeId) // ✅ OFFICE RESTRICTION
            ->with([
                'targetPeriods' => function ($q) use ($year, $semester) {
                    $q->where('year', $year)
                        ->where('semester', $semester)
                        ->with([
                            'performanceStandards' => function ($query) {
                                $query->select('id', 'target_period_id', 'category', 'mfo', 'output', 'success_indicator')
                                    ->with('standardOutcomes');
                            },
                        ]);
                },
            ])
            ->first();

        if (! $employee) {
            return response()->json([
                'message' => 'Employee not found or access denied',
            ], 404);
        }

        return response()->json($employee);
    }

    // get the perfomance standard of employee
    public function getPerformanceStandard($targetPeriodId)
    {
        $employee = PerformanceStandard::select('id', 'target_period_id', 'category', 'mfo', 'success_indicator')
            ->where('target_period_id', $targetPeriodId)
            ->with(['standardOutcomes' => function ($query) {
                $query->select('id', 'performance_standard_id', 'rating', 'quantity_target as quantity', 'effectiveness_criteria as effectiveness', 'timeliness_range as timeliness');
            }])->get();

        return response()->json($employee);
    }

    // get the monthly rate of employee
    // month - week1, week2, week 3, it depend of the month how many weeks
    // then get the rate of the employee every day  then total
    // the format of the date is mm/dd/yy
    // public function getMonthlyRate($targetPeriodId)
    // {
    //     $perfomanceRating = PerformanceStandard::select('id', 'target_period_id', 'category', 'mfo')
    //         ->where('target_period_id', $targetPeriodId)
    //         ->with([
    //              'performanceRating' => function ($query) {
    //                 $query->select('id', 'performance_standard_id', 'date', 'quantity_target_rate as quantity_rate', 'effectiveness_criteria_rate as effectiveness_rate', 'timeliness_range_rate as timeliness_rate');
    //             },
    //         ])->get();

    //     return response()->json($perfomanceRating);
    // }





    // approving the ipcr of the employee
    public function approveIpcrEmployee($controlNo, $semester, $year, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approve,reject,review',
        ]);

        // Get employee with office restriction
        $employee = Employee::where('ControlNo', $controlNo)
            ->where('office_id', $this->officeId)
            ->first();

        if (! $employee) {
            return response()->json([
                'message' => 'Employee not found or access denied',
            ], 404);
        }

        // Get the target period
        $targetPeriod = $employee->targetPeriods()
            ->where('year', $year)
            ->where('semester', $semester)
            ->first();

        if (! $targetPeriod) {
            return response()->json([
                'message' => 'Target period not found',
            ], 404);
        }

        // Update only the target period
        $targetPeriod->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'IPCR status updated successfully.',
            'data'    => $targetPeriod,
        ]);
    }

    // get the monthly rate of employee
    // month - week1, week2, week 3, it depend of the month how many weeks
    // then get the rate of the employee every day  then total
    // the format of the date is mm/dd/yy
    public function getMonthlyRate($targetPeriodId)
    {
        $standards = PerformanceStandard::select([
            'id',
            'target_period_id',
            'category',
            'mfo'
        ])
            ->where('target_period_id', $targetPeriodId)
            ->with([
                'performanceRating' => function ($query) {
                    $query->select([
                        'id',
                        'performance_standard_id', // ✅ Include foreign key
                        'date',
                        'quantity_target_rate',
                        'effectiveness_criteria_rate',
                        'timeliness_range_rate'
                    ]);
                }
            ])
            ->get();

        $standards->transform(function ($standard) {
            $grouped = $this->groupRatingsByMonth($standard->performanceRating);

            // Store in a new property to preserve original relation
            $standard->monthly_ratings = $grouped;
            $standard->makeHidden('performanceRating');

            return $standard;
        });

        return response()->json($standards);
    }

    /**
     * Group performance ratings by month and week
     */
    private function groupRatingsByMonth($ratings)
    {
        $grouped = [];

        foreach ($ratings as $rating) {
            try {
                $date = Carbon::createFromFormat('m/d/Y', $rating->date);
            } catch (\Exception $e) {
                // Skip invalid dates or log error
                Log::warning("Invalid date format: {$rating->date}");
                continue;
            }

            $monthKey = $date->format('Y-m');
            $weekNumber = $this->getWeekOfMonth($date);
            $weekKey = "week{$weekNumber}";

            // Initialize month if not exists
            if (!isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [
                    'month' => $date->format('F Y'),
                    // 'year_month' => $monthKey,
                    'quantity' => $this->initializeWeeks(),
                    'effectiveness' => $this->initializeWeeks(),
                    'timeliness' => $this->initializeWeeks(),
                ];
            }

            // Add ratings to appropriate week
            $grouped[$monthKey]['quantity'][$weekKey] += (int) $rating->quantity_target_rate;
            $grouped[$monthKey]['effectiveness'][$weekKey] += (int) $rating->effectiveness_criteria_rate;
            $grouped[$monthKey]['timeliness'][$weekKey] += (int) $rating->timeliness_range_rate;
        }

        // Calculate totals and averages
        foreach ($grouped as &$month) {
            foreach (['quantity', 'effectiveness', 'timeliness'] as $type) {
                $weekValues = array_filter($month[$type], fn($k) => $k !== 'total', ARRAY_FILTER_USE_KEY);
                $month[$type]['total'] = array_sum($weekValues);

                // Optional: Calculate average
                // $nonZeroWeeks = count(array_filter($weekValues));
                // $month[$type]['average'] = $nonZeroWeeks > 0
                //     ? round($month[$type]['total'] / $nonZeroWeeks, 2)
                //     : 0;
            }
        }

        return array_values($grouped);
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
            'total'=> 0,
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
    public function getSummaryMonthlyRate($targetPeriodId)
    {
        $standards = PerformanceStandard::select([
            'id',
            'target_period_id',
            'category',
            'mfo'
        ])
            ->where('target_period_id', $targetPeriodId)
            ->with([
                'performanceRating' => function ($query) {
                    $query->select([
                        'id',
                        'performance_standard_id',
                        'date',
                        'quantity_target_rate',
                        'effectiveness_criteria_rate',
                        'timeliness_range_rate'
                    ]);
                }
            ])
            ->get();

        $standards->transform(function ($standard) {
            $monthly = $this->groupRatingsByMonth($standard->performanceRating);

            // ✅ NEW: compute PS summary
            $summary = $this->computePSSummary($monthly);

            $standard->monthly_ratings = $monthly;
            $standard->summary = $summary;

            $standard->makeHidden('performanceRating');

            return $standard;
        });

        return response()->json($standards);
    }

    private function computePSSummary(array $monthlyRatings)
    {
        $totals = [
            'quantity' => 0,
            'effectiveness' => 0,
            'timeliness' => 0,
        ];

        $monthCount = count($monthlyRatings);

        foreach ($monthlyRatings as $month) {
            $totals['quantity'] += $month['quantity']['total'];
            $totals['effectiveness'] += $month['effectiveness']['total'];
            $totals['timeliness'] += $month['timeliness']['total'];
        }

        return [
            'quantity' => [
                'total' => $totals['quantity'],
                'average' => $monthCount > 0
                    ? round($totals['quantity'] / $monthCount, 2)
                    : 0
            ],
            'effectiveness' => [
                'total' => $totals['effectiveness'],
                'average' => $monthCount > 0
                    ? round($totals['effectiveness'] / $monthCount, 2)
                    : 0
            ],
            'timeliness' => [
                'total' => $totals['timeliness'],
                'average' => $monthCount > 0
                    ? round($totals['timeliness'] / $monthCount, 2)
                    : 0
            ],
        ];
    }
}
