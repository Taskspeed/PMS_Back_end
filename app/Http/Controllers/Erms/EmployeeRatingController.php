<?php

namespace App\Http\Controllers\Erms;

use App\Http\Controllers\Controller;
use App\Http\Requests\performanceRatingStoreRequest;
use App\Models\PerformanceRating;
use App\Models\TargetPeriod;
use App\Services\PerformanceRatingService;
use App\Services\TargetPeriodService;



class EmployeeRatingController extends Controller
{

    // service
    protected $targetperiodService;

    public function __construct(TargetPeriodService $targetperiodService)
    {
        return $this->targetperiodService = $targetperiodService;
    }


    // target period of employee
    public function targetPeriodEmployee($controlNo)
    {

        $result = $this->targetperiodService->targetPeriod($controlNo);

        return $result;
    }


    //  get the target peroid details the performance standard and standard outcome
    public function targetPeriodDetails($targetPeriodId)
    {
        $targetperiod = TargetPeriod::select('id')->where('id', $targetPeriodId)
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
                        'required_output'
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
            ])->get();

        // check if the his office

        return response()->json($targetperiod);
    }

    //  get the target peroid details the performance standard and standard outcome
    public function targetPeriod($targetPeriodId, $month = null, $year = null,$week= null)
    {

        $data = $this->targetperiodService->getTargetPeriodWithStandardsAndRatings($targetPeriodId,$month,$year,$week);

        return $data;
    }



    // employee store his rate
    public function performanceRating(performanceRatingStoreRequest $request, PerformanceRatingService $performanceRatingService)
    {
        $validated = $request->validated();

        $rating = $performanceRatingService->performanceRatingStore($validated);

        return response()->json([
            'status' => true,
            'message' => 'Rate(s) successfully saved',
            'rates' => $rating
        ]);
    }

    // get the list of the employee the rate of date
    public function getListOfRatingEmployee($controlNo)
    {

        $list = PerformanceRating::select(
            'id',
            'performance_standard_id',
            // 'control_no',
            'date'
        )
            ->where('control_no', $controlNo)
            ->orderBy('date', 'asc')
            ->get();

        if ($list->isEmpty()) {
            return response()->json([
                'message' => 'Employee does not have ratings yet'
            ], 404);
        }

        return response()->json($list, 200);
    }

    //performance rating record
    public function performanceRatingRecord($targetPeriodId)
    {

        $employee_rating_record = TargetPeriod::select('id')->where('id', $targetPeriodId)
            ->with([
                'performanceStandards' => function ($query) {
                    $query->select(
                        'id',
                        'target_period_id',
                        'category',
                        'mfo',
                        // 'output',
                        // 'output_name',
                        // 'performance_indicator',
                        // 'success_indicator',
                        // 'required_output'
                    )
                        ->with([
                            'performanceRating' => function ($query) {
                                $query->select(
                                    'id',
                                    'performance_standard_id',

                                    'date',
                                    'quantity_actual',
                                    'effectiveness_actual',
                                    'timeliness_actual'
                                );
                            },

                        ]);
                }
            ])->get();


        return response()->json($employee_rating_record);
    }
}
