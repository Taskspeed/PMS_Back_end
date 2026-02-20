<?php

namespace App\Http\Controllers\office;

use App\Http\Requests\AttendanceRequest;

use App\Models\Employee;

use Illuminate\Http\Request;
use App\Services\IpcrService;
use App\Models\PerformanceStandard;
use App\Http\Resources\IpcrResource;
use App\Http\Resources\MonthlyPerformanceResource;
use App\Http\Resources\MonthlyPerformanceSummaryResource;

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
    public function approveIpcrEmployee($controlNo, $semester, $year, Request $request, IpcrService $ipcrService)
    {
        try {

            $targetPeriod = $ipcrService->approveIpcr($controlNo, $semester, $year, $request);

            return response()->json([
                'success' => true,
                'message' => 'IPCR status updated successfully.',
                'data'    => $targetPeriod,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    // get the monthly rate of employee
    // month - week1, week2, week 3, it depend of the month how many weeks
    // then get the rate of the employee every day  then total
    // the format of the date is mm/dd/yy
    public function getMonthlyEmployee($targetPeriodId, IpcrService $ipcrService)
    {
        // access the MonthlyPerformanceService to get the monthly performance data
        $monthlyData = $ipcrService->getMonthly($targetPeriodId);

        if (empty($monthlyData)) {
            return response()->json([
                'message' => 'Monthly performance not found'
            ], 404);
        }

        // use the MonthlyPerformanceResource to format the response
        return response()->json([
            'standards' => MonthlyPerformanceResource::collection($monthlyData['standards'])->resolve(),
            'attendance' => $monthlyData['attendance']
        ]);
    }

    // get the summary-monthly-rate
    public function getSummaryMonthlyEmployee($targetPeriodId, IpcrService $ipcrService)
    {
        $monthSummaryData = $ipcrService->getSummaryMonthly($targetPeriodId);

        if (empty($monthSummaryData)) {
            return response()->json([
                'message' => 'Summary monthly performance not found'
            ], 404);
        }

        // use the MonthlyPerformanceSummaryResource to format the response
        return response()->json([
            'standards' =>  MonthlyPerformanceSummaryResource::collection($monthSummaryData['standards'])->resolve(),
            'attendance' => $monthSummaryData['attendance']
        ]);
    }

    // store attendance
    public function attendance(AttendanceRequest $request, IpcrService $ipcrService)
    {
        try {
            $attendance = $ipcrService->storeAttendance($request);

            return response()->json([
                'success' => true,
                'message' => 'Attendance data stored successfully.',
                'data'    => $attendance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    // updating - status ipcr of employee args approve,review,cancel and others
    public function statusIpcr(Request $request, $targetPeriodId, IpcrService $updatingIpcr){

    $validateData = $request->validate([
            'status' =>  'required|string'
    ]);

    //updating the targetperiod of employee
    $ipcr = $updatingIpcr->updateStatusIpcr($validateData,$targetPeriodId);

        return response()->json([
            'success' => true,
            'message' => 'IPCR status updated successfully.',
            'data' => $ipcr
        ], 200);
    }
}
