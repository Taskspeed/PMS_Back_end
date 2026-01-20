<?php

namespace App\Http\Controllers\office;

use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use App\Models\PerformanceRating;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceStandard;
use App\Http\Controllers\Controller;
use App\Http\Requests\performanceRatingStoreRequest;

class EmployeeRatingController extends Controller
{


    // public function targetPeriodEmployee($controlNo) // fetch the target period of employee
    // {
    //     $employeeTargetPeriod = TargetPeriod::where(
    //         'control_no',
    //         $controlNo
    //     )->get();

    //     return response()->json($employeeTargetPeriod);
    // }

    // fetch the target period of employee
    public function targetPeriodEmployee($controlNo)
    {

        $employeeTargetPeriod = TargetPeriod::select('control_no','semester','year','status')->where('control_no', $controlNo)->get();

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
         ->with(['performanceStandards' => function  ($query){
            $query->select('id','target_period_id','category','mfo','output', 'output_name', 'performance_indicator', 'success_indicator', 'required_output')
            ->with(['standardOutcomes']);
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
}
