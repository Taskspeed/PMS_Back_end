<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\PerformanceRating;
use App\Models\PerformanceStandard;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;

class EmployeeRatingController extends Controller
{


    public function targetPeriodEmployee($control_no) // fetch the target period of employee
    {
        $employeeTargetPeriod = TargetPeriod::where(
            'control_no',
            $control_no
        )->get();

        return response()->json($employeeTargetPeriod);
    }
    
    //  get the target peroid details the performance standart and standard outcome
    public function targetPeriodDetails($targetPeriodId)
    {
        $targetperiod = TargetPeriod::select('id')->where('id', $targetPeriodId)

         ->with(['performanceStandards.standardOutcomes'])->get();

        return response()->json($targetperiod);
    }


    public function performanceRatingStore(Request $request)  // employee store his rate
    {
        $validated = $request->validate([

            // rate
            'performance_rate' => 'required|array|min:1',
            // 'performance_rate.*.target_period_id' => 'required|exists:target_periods,id',
            'performance_rate.*.performance_standards' => 'required|exists:performance_standards,id',
            'performance_rate.*.date' => 'required|date_format:m/d/Y',
            'performance_rate.*.control_no' => 'required|string',
            'performance_rate.*.quantity_target_rate' => 'required|string',
            'performance_rate.*.effectiveness_criteria_rate' => 'required|string',
            'performance_rate.*.timeliness_range_rate' => 'required|string',

        ]);

         $saveRates = [];  // store in to array

        // save the rating using foreach loop
        foreach ($validated['performance_rate'] as $rateData ) {
            $saveRates[] = PerformanceRating::create($rateData);
        }

        return response()->json([
            'status' => true,
            'message' => 'Rate(s) successfully saved',
            'rates' => $saveRates
        ]);

    }
}
