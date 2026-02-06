<?php
namespace App\Http\Controllers\office;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceStandard;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class IpcrController extends BaseController
{
    // protected $user;
    // protected $officeId;

    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         $this->user     = Auth::user();
    //         $this->officeId = $this->user->office_id;

    //         return $next($request);
    //     });
    // }
    // getting the ipcr of the employee based on controlno and year
    public function getIpcr($controlNo, $year, $semester)
    {
        $employee = Employee::where('ControlNo', $controlNo)
            ->with([
                'targetPeriods' => function ($q) use ($year, $semester) {
                    $q->where('year', $year)
                        ->where('semester', $semester)
                        ->with([
                            'performanceStandards' => function ($query) {
                                $query->select(
                                    'id',
                                    'target_period_id',
                                    'category',
                                    'mfo',
                                    'output',
                                    'success_indicator',
                                    'core',
                                    'technical',
                                    'leadership'
                                )->with([
                                    'performanceRating' => function ($q) {
                                        $q->select(
                                            'id',
                                            'performance_standard_id',
                                            'date',
                                            'quantity_target_rate',
                                            'effectiveness_criteria_rate',
                                            'timeliness_range_rate'
                                        );
                                    }
                                ]);
                            }
                        ]);
                }
            ])
            ->first();

        // ✅ Null check FIRST
        if (! $employee) {
            return response()->json([
                'message' => 'Employee not found or access denied',
            ], 404);
        }

        // ✅ Transform PERFORMANCE STANDARDS (not employee)
        $employee->targetPeriods->each(function ($period) {
            $period->performanceStandards->each(function ($standard) {
                $grouped = $this->groupRatingsByMonthIpcr($standard->performanceRating);

                $standard->monthly_ratings = $grouped;
                $standard->makeHidden('performanceRating');
            });
        });

        return response()->json($employee);
    }


    // // getting the ipcr of the employee based on controlno and year
    // public function getIpcr($controlNo, $year, $semester)
    // {
    //     $employee = Employee::where('ControlNo', $controlNo)
    //         // ->where('office_id', $this->officeId) // ✅ OFFICE RESTRICTION
    //         ->with([
    //             'targetPeriods' => function ($q) use ($year, $semester) {
    //                 $q->where('year', $year)
    //                     ->where('semester', $semester)
    //                     ->with([
    //                         'performanceStandards' => function ($query) {
    //                             $query->select('id', 'target_period_id', 'category', 'mfo', 'output' ,'success_indicator')
    //                      ->with(['performanceRating' => function ($query) {
    //                         $query->select('id', 'performance_standard_id','date','quantity_target_rate','effectiveness_criteria_rate','timeliness_range_rate');
    //                     }]);
    //                 },
    //                     ]);
    //             },
    //         ])
    //         ->first();



    //     if (! $employee) {
    //         return response()->json([
    //             'message' => 'Employee not found or access denied',
    //         ], 404);
    //     }

    //     return response()->json($employee);
    // }

    // get the perfomance standard of employee
    public function getPerformanceStandard($targetPeriodId)
    {
        $employee = PerformanceStandard::select('id', 'target_period_id', 'category', 'mfo', 'success_indicator','core','technical','leadership', 'required_output')
            ->where('target_period_id', $targetPeriodId)
            ->with(['standardOutcomes' => function ($query) {
                $query->select('id', 'performance_standard_id', 'rating', 'quantity_target as quantity', 'effectiveness_criteria as effectiveness', 'timeliness_range as timeliness');
            }])

            ->get();

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
            // ->where('office_id', $this->officeId)
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
            $weekKey  = 'week' . $this->getWeekOfMonth($date);

            if (!isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [
                    'month' => $date->format('F Y'),

                    // sums
                    'quantity' => $this->initializeWeeks(),
                    'effectiveness' => $this->initializeWeeks(),
                    'timeliness' => $this->initializeWeeks(),

                    // counters
                    '_counts' => [
                        'quantity' => $this->initializeWeeks(),
                        'effectiveness' => $this->initializeWeeks(),
                        'timeliness' => $this->initializeWeeks(),
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
            'monthly' => array_values($grouped),
            'whole_average' => $wholeAverage
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
    public function getSummaryMonthlyRate($targetPeriodId)
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

        // $monthCount = count($monthlyRatings);

        $validMonths = 0;

        foreach ($monthlyRatings as $month) {
            $q = data_get($month, 'quantity.total');
            $e = data_get($month, 'effectiveness.total');
            $t = data_get($month, 'timeliness.total');

            if ($q !== null || $e !== null || $t !== null) {
                $totals['quantity'] += $q ?? 0;
                $totals['effectiveness'] += $e ?? 0;
                $totals['timeliness'] += $t ?? 0;
                $validMonths++;
            }
        }

        // return [
        //     'quantity' => [
        //         'total' => $totals['quantity'],
        //         'average' => $monthCount > 0
        //             ? round($totals['quantity'] / $monthCount, 2)
        //             : 0
        //     ],
        //     'effectiveness' => [
        //         'total' => $totals['effectiveness'],
        //         'average' => $monthCount > 0
        //             ? round($totals['effectiveness'] / $monthCount, 2)
        //             : 0
        //     ],
        //     'timeliness' => [
        //         'total' => $totals['timeliness'],
        //         'average' => $monthCount > 0
        //             ? round($totals['timeliness'] / $monthCount, 2)
        //             : 0
        //     ],
        // ];

        return [
            'quantity' => [
                'total' => $totals['quantity'],
                'average' => $validMonths > 0 ? round($totals['quantity'] / $validMonths, 2) : 0
            ],
            'effectiveness' => [
                'total' => $totals['effectiveness'],
                'average' => $validMonths > 0 ? round($totals['effectiveness'] / $validMonths, 2) : 0
            ],
            'timeliness' => [
                'total' => $totals['timeliness'],
                'average' => $validMonths > 0 ? round($totals['timeliness'] / $validMonths, 2) : 0
            ],
        ];
    }


    /**
     * Group performance ratings by month and week
     */
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




    // this function is to get the  signatory of the office
    // get the controlno to determine who is the person because every employee are  different signatory
    // for the employee employee
    // public function getStructure(Request $request)
    // {
    //     $request->validate([
    //         'office_name' => 'required|string',
    //         'organization' => 'required|string',
    //         'semester' => 'required',
    //         'year' => 'required',
    //         'ControlNo' => 'required|string',
    //     ]);

    //     /**
    //      * =====================================
    //      * 0️⃣ VALIDATE ORGANIZATION BELONGS TO OFFICE
    //      * =====================================
    //      */
    //     $orgExistsInOffice = DB::table('employees')
    //         ->where('office', $request->office_name)
    //         ->where(function ($q) use ($request) {
    //             $q->where('office2', $request->organization)
    //                 ->orWhere('group', $request->organization)
    //                 ->orWhere('division', $request->organization)
    //                 ->orWhere('section', $request->organization)
    //                 ->orWhere('unit', $request->organization);
    //         })
    //         ->exists();

    //     if (!$orgExistsInOffice) {
    //         return response()->json([
    //             // 'message' => 'Invalid organization. The organization does not belong to the selected office.'
    //             'message' => 'There are no employees assigned to the selected organization in this office.'

    //         ], 422);
    //     }

    //     /**
    //      * ===============================
    //      * 1️⃣ OFFICE HEAD
    //      * ===============================
    //      */
    //     $officeEmployee = DB::table('employees')
    //         ->where('office', $request->office_name)
    //         ->whereNull('division')
    //         ->whereNull('section')
    //         ->whereNull('unit')
    //         ->select('ControlNo', 'name', 'rank', 'position',)
    //         ->first();

    //     if (!$officeEmployee) {
    //         return response()->json([
    //             'message' => 'Office head not found.'
    //         ], 404);
    //     }

    //     /**
    //      * ===============================
    //      * 2️⃣ ORGANIZATION EMPLOYEES
    //      * ===============================
    //      */
    //     $employees = DB::table('employees')
    //         ->where('office', $request->office_name)
    //         ->where(function ($q) use ($request) {
    //             $q->where('office2', $request->organization)
    //                 ->orWhere('group', $request->organization)
    //                 ->orWhere('division', $request->organization)
    //                 ->orWhere('section', $request->organization)
    //                 ->orWhere('unit', $request->organization);
    //         })
    //         ->select('ControlNo', 'name', 'rank', 'position')
    //         ->get();

    //     $controlNos = $employees->pluck('ControlNo');

    //     $organizationTargetPeriods = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->with([
    //         'employee:ControlNo,name,rank,position',
    //         'performanceStandards.standardOutcomes' => function ($query) {
    //             $query->select(
    //                 'id',
    //                 'performance_standard_id',
    //                 'rating',
    //                 'quantity_target',
    //                 'effectiveness_criteria',
    //                 'timeliness_range'
    //             );
    //         },
    //     ])
    //         ->whereIn('control_no', $controlNos)
    //         ->where('semester', $request->semester)
    //         ->where('year', $request->year)
    //         ->get();


    //     return response()->json([
    //         'office' => [
    //             'name' => $request->office_name,
    //             'employee' => [
    //                 'ControlNo' => $officeEmployee->ControlNo,
    //                 'name'      => $officeEmployee->name,
    //                 'rank'      => $officeEmployee->rank,
    //                 'position'  => $officeEmployee->position,

    //             ],

    //         ],

    //         'organization' => [
    //             'name' => $request->organization,
    //             'employees' => $organizationTargetPeriods
    //                 ->groupBy('control_no')
    //                 ->map(function ($periods) {
    //                     $employee = $periods->first()->employee;

    //                     return [
    //                         'employee' => [
    //                             'ControlNo' => $employee->ControlNo,
    //                             'name' => $employee->name,
    //                             'rank' => $employee->rank,
    //                             'position' => $employee->position,

    //                         ],

    //                     ];
    //                 })->values()
    //         ]
    //     ]);
    // }

}
