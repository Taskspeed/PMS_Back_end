<?php

namespace App\Http\Controllers\office;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use App\Services\IpcrService;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceStandard;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\IpcrResource;
use App\Http\Resources\MonthlyPerformanceResource;
use App\Http\Resources\MonthlyPerformanceSummaryResource;
use App\Services\MonthlyPerformanceService;
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



    public function getIpcrEmployee($controlNo, $year, $semester, IpcrService $ipcrService)
    {
        $employee  = $ipcrService->getIpcrData($controlNo, $year, $semester);

        if (! $employee) {
            return response()->json([
                'message' => 'Employee not found or access denied'
            ], 404);
        }

        return new IpcrResource($employee); // ✅ SINGLE resource
    }


    // get the perfomance standard of employee
    public function getPerformanceStandard($targetPeriodId)
    {
        $employee = PerformanceStandard::select('id', 'target_period_id', 'category', 'mfo', 'success_indicator', 'core', 'technical', 'leadership', 'required_output')
            ->where('target_period_id', $targetPeriodId)
            ->with(['standardOutcomes' => function ($query) {
                $query->select('id', 'performance_standard_id', 'rating', 'quantity_target as quantity', 'effectiveness_criteria as effectiveness', 'timeliness_range as timeliness');
            }])

            ->get();

        return response()->json($employee);
    }



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
    public function getMonthlyEmployee($targetPeriodId, MonthlyPerformanceService $monthlyPerformanceService)
    {
        // access the MonthlyPerformanceService to get the monthly performance data
        $monthlyData = $monthlyPerformanceService->getMonthly($targetPeriodId);

        if ($monthlyData->isEmpty()) {
            return response()->json([
                'message' => 'Monthly performance not found'
            ], 404);
        }

        // use the MonthlyPerformanceResource to format the response
        return response()->json(
            MonthlyPerformanceResource::collection($monthlyData)->resolve()
        );
    }



    // get the summary-monthly-rate
    public function getSummaryMonthlyEmployee($targetPeriodId , MonthlyPerformanceService $monthlyPerformanceService)
    {

        $monthSummaryData = $monthlyPerformanceService->getSummaryMonthly($targetPeriodId);

        if ($monthSummaryData->isEmpty()) {
            return response()->json([
                'message' => 'Summary monthly performance not found'
            ], 404);
        }

        return response()->json(
            MonthlyPerformanceSummaryResource::collection($monthSummaryData)->resolve()
        );

    }







}
