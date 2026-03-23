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


        protected $targetperiodService;

        public function __construct(TargetPeriodService $targetperiodService)
        {
            return $this->targetperiodService = $targetperiodService;
        }


    // // fetch the target period of employee
    // public function targetPeriodEmployee($controlNo)
    // {

    //     $employeeTargetPeriod = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status','office_id')->where('control_no', $controlNo)->get();

    //     if ($employeeTargetPeriod->isEmpty()) {
    //         return response()->json([
    //             'message' => 'No target period found for this employee.',
    //             'data' => []
    //         ], 200);
    //     }

    //     $unitWorkplan = UnitWorkPlan::where('office_id',  $employeeTargetPeriod->office_id)
    //         ->where('semester', $employeeTargetPeriod->semester)
    //         ->where('year', $employeeTargetPeriod->year)
    //         ->whereHas('unitworkplanLastestRecord', function ($q) {
    //             $q->where('status', 'reviewed');
    //         })
    //         ->exists();

    //     $opcr = OfficeOpcr::where('office_id', $employeeTargetPeriod->office_id)
    //         ->where('semester',  $employeeTargetPeriod->semester)
    //         ->where('year', $employeeTargetPeriod->year)
    //         ->whereHas('officeOpcrRecordLastestRecord', function ($q) {
    //             $q->where('status', 'reviewed');
    //         })
    //         ->exists();

    //     if (!$unitWorkplan && !$opcr) {
    //         return [
    //             'can_rate' => false,
    //             'message' => 'Unit Work Plan and OPCR are not reviewed yet.'
    //         ];
    //     }

    //     if (!$unitWorkplan) {
    //         return [
    //             'can_rate' => false,
    //             'message' => 'Unit Work Plan is not reviewed yet.'
    //         ];
    //     }

    //     if (!$opcr) {
    //         return [
    //             'can_rate' => false,
    //             'message' => 'OPCR is not reviewed yet.'
    //         ];
    //     }

    //     // ✅ BOTH REVIEWED → UPDATE TARGET PERIOD STATUS
    //     TargetPeriod::where('office_id', $employeeTargetPeriod->office_id)
    //         ->where('semester',  $employeeTargetPeriod->semester)
    //         ->where('year', $employeeTargetPeriod->year)
    //         ->update([
    //             'status' => 'target period started'
    //         ]);

    //     return [
    //         'can_rate' => true,
    //         'message' => 'Employees can now start rating.'
    //     ];


    //     return response()->json([
    //         'message' => 'Target period retrieved successfully.',
    //         'targetPeriod' => $employeeTargetPeriod
    //     ], 200);
    // }


    // target period of employee
    public function targetPeriodEmployee($controlNo)
    {

      $result = $this->targetperiodService->targetPeriod($controlNo);

      return $result;

    }

    //  private function canEmployeesRate($officeId, $semester, $year)
    // {
    //     $unitWorkplan = UnitWorkPlan::where('office_id', $officeId)
    //         ->where('semester', $semester)
    //         ->where('year', $year)
    //         ->whereHas('unitworkplanLastestRecord', function ($q) {
    //             $q->where('status', 'reviewed');
    //         })
    //         ->first();

    //     $opcr = OfficeOpcr::where('office_id', $officeId)
    //         ->where('semester', $semester)
    //         ->where('year', $year)
    //         ->whereHas('officeOpcrRecordLastestRecord', function ($q) {
    //             $q->where('status', 'reviewed');
    //         })
    //         ->first();


    //     if (!$unitWorkplan && !$opcr) {
    //         return [
    //             'can_rate' => false,
    //             'message' => 'Unit Work Plan and OPCR are not reviewed yet.'
    //         ];
    //     }

    //     if (!$unitWorkplan) {
    //         return [
    //             'can_rate' => false,
    //             'message' => 'Unit Work Plan is not reviewed yet.'
    //         ];
    //     }

    //     if (!$opcr) {
    //         return [
    //             'can_rate' => false,
    //             'message' => 'OPCR is not reviewed yet.'
    //         ];
    //     }

    //     return [
    //         'can_rate' => true,
    //         'message' => 'Employees can now start rating.'
    //     ];
    // }

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

    // // check if the emplotee can rate
    // public function canEmployeesRate($officeId, $semester, $year)
    // {
    //     $unitWorkplan = UnitWorkPlan::where('office_id', $officeId)
    //         ->where('semester', $semester)
    //         ->where('year', $year)
    //         ->whereHas('unitworkplanLastestRecord', function ($q) {
    //             $q->where('status', 'reviewed');
    //         })
    //         ->first();

    //     $opcr = OfficeOpcr::where('office_id', $officeId)
    //         ->where('semester', $semester)
    //         ->where('year', $year)
    //         ->whereHas('officeOpcrRecordLastestRecord', function ($q) {
    //             $q->where('status', 'reviewed');
    //         })
    //         ->first();

    //     return $unitWorkplan && $opcr;
    // }


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
    public function performanceRatingRecord($targetPeriodId){

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
