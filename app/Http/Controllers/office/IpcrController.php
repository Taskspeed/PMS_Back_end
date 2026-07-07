<?php

namespace App\Http\Controllers\office;

use App\Http\Requests\AttendanceRequest;

use App\Http\Resources\IpcrResource;

use App\Http\Resources\MonthlyPerformanceResource;
use App\Http\Resources\MonthlyPerformanceSummaryResource;
use App\Http\Resources\Office\IpcrEmployeeResource;
use App\Models\Employee;
use App\Models\PerformanceStandard;
use App\Services\IpcrService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class IpcrController extends BaseController
{
    use ApiResponseTrait;

    protected IpcrService $ipcrService;

    public function __construct(IpcrService $ipcrService)
    {
       $this->ipcrService = $ipcrService;
    }

    public function getIpcrEmployee(string $controlNo, int $year, string $semester)
    {
        $employee  = $this->ipcrService->getIpcrData($controlNo, $year, $semester);

        if (! $employee) {
            return response()->json([
                'message' => 'Employee not found or access denied'
            ], 404);
        }

        return new IpcrResource($employee);
    }

    // get the perfomance standard of employee
    public function getPerformanceStandard(int $targetPeriodId)
    {
        $employee = PerformanceStandard::select('id', 'target_period_id', 'category', 'mfo', 'success_indicator', 'core', 'technical', 'leadership', 'required_output')
            ->where('target_period_id', $targetPeriodId)
            ->with(['standardOutcomes' => function ($query) {
                $query->select('id', 'performance_standard_id', 'rating', 'quantity_target as quantity', 'effectiveness_criteria as effectiveness', 'timeliness_range as timeliness');
            }])
            ->get();
        return response()->json($employee);
    }

    // // approving the ipcr of the employee
    // public function approveIpcrEmployee(string $controlNo, string $semester, int $year, Request $request)
    // {
    //     try {

    //         $targetPeriod = $this->ipcrService->approveIpcr($controlNo, $semester, $year, $request);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'IPCR status updated successfully.',
    //             'data'    => $targetPeriod,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //         ], $e->getCode() ?: 400);
    //     }
    // }

    // get the monthly rate of employee
    // month - week1, week2, week 3, it depend of the month how many weeks
    // then get the rate of the employee every day  then total
    // the format of the date is mm/dd/yy
    public function getMonthlyEmployee(int $targetPeriodId)
    {
        // access the MonthlyPerformanceService to get the monthly performance data
        $monthlyData = $this->ipcrService->getMonthly($targetPeriodId);

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
    public function getSummaryMonthlyEmployee(int $targetPeriodId)
    {
        $monthSummaryData = $this->ipcrService->getSummaryMonthly($targetPeriodId);

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
    public function attendance(AttendanceRequest $request)
    {
        try {
            $attendance = $this->ipcrService->storeAttendance($request);

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

    // list of ipcr need to approve  of the Department Head
    public function listIpcr(Request $request){

        $authUser = Auth::user();

        $semester = $request->input('semester');
        $year = $request->input('year');

        $data = $this->ipcrService->ipcr($semester, $year, $authUser);

       return $this->successMessage(IpcrEmployeeResource::collection($data),'Successfully fetched',);
    }
    
   
}
