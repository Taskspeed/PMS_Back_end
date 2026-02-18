<?php

namespace App\Http\Controllers\office;

use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use App\Models\PerformanceRating;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceStandard;
use App\Http\Controllers\Controller;
use App\Http\Requests\performanceRatingStoreRequest;

use function PHPUnit\Framework\isEmpty;

class EmployeeRatingController extends Controller
{


    // fetch the target period of employee
    public function targetPeriodEmployee($controlNo)
    {

        $employeeTargetPeriod = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->where('control_no', $controlNo)->get();

        if ($employeeTargetPeriod->isEmpty()) {
            return response()->json([
                'message' => 'No target period found for this employee.',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'Target period retrieved successfully.',
            'targetPeriod' => $employeeTargetPeriod
        ], 200);
    }


    //  get the target peroid details the performance standart and standard outcome
    public function targetPeriodDetails($targetPeriodId)
    {
        $targetperiod = TargetPeriod::select('id')->where('id', $targetPeriodId)
            ->with([
                'performanceStandards' => function ($query) {
                    $query->select('id', 'target_period_id', 'category', 'mfo', 'output',
                    'output_name', 'performance_indicator', 'success_indicator', 'required_output')
                        ->with([
                            'standardOutcomes' => function ($query) {
                                $query->select('id', 'performance_standard_id', 'rating',
                                'quantity_target as quantity', 'effectiveness_criteria as effectiveness', 'timeliness_range as timeliness');
                            },
                    'configurations' => function ($query) {
                        $query->select('id', 'performance_standard_id', 'target_output as targetOutput',
                        'quantity_indicator as quantityIndicator', 'timeliness_indicator as timelinessIndicator', 'timeliness_range as range','timeliness_date as date','timeliness_description as description');
                    }
                        ]);
                }
            ])->get();

        return response()->json($targetperiod);
    }

    // employee store his rate
    public function performanceRatingStore(performanceRatingStoreRequest $request)
    {
        $validated = $request->validated();

        $saveRates = [];  // store in to array

        // save the rating using foreach loop
        DB::transaction(function () use ($validated, &$saveRates) {
            foreach ($validated['performance_rate'] as $rateData) {

                $rateData['performance_standard_id'] = $rateData['performance_standards'];
                unset($rateData['performance_standards']);

                $saveRates[] = PerformanceRating::create($rateData);
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Rate(s) successfully saved',
            'rates' => $saveRates
        ]);
    }

    // get the list of the employee the rate of date
    public function getListOfRatingEmployee($controlNo){

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
}
