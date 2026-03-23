<?php

namespace App\Http\Controllers\office;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    //getting the  total employee in the office
    public function dashboardStatus(Request $request,$semester,$year)
    {
        $user = Auth::user();

        $officeId = $user->office_id;

        // Assuming you have an Employee model with an 'office_code' field
        $employees = \App\Models\Employee::where('office_id', $officeId)->count();

        $controlNos = \App\Models\Employee::where('office_id', $officeId)->pluck('ControlNo');

        $ipcr_status = \App\Models\TargetPeriod::whereIn('control_no', $controlNos)
        ->where('semester',$semester)
        ->where('year',$year)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');


        return response()->json([
            'total_employees' => $employees,
            'ipcr_status' =>  $ipcr_status
     ]);
    }

    //get the total of status of the employee ipcr
    public function getIpcrStatusCounts()
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        // Assuming you have an Employee model with an 'office_code' field
        $employees = \App\Models\Employee::where('office_id', $officeId)->pluck('ControlNo');

        $statusCounts = \App\Models\TargetPeriod::whereIn('control_no', $employees)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json($statusCounts);
    }



    // list of emplotee dont have ipcr
    public function listOfEmployeeNoIpcr($semester, $year)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Get all employees in the same office
        $employees = \App\Models\Employee::select('ControlNo','name','position','office_id','status','job_title')->where('office_id', $user->office_id)
            ->whereNotIn('status', ['Contractual', 'Job Order'])->get();

        // Get control numbers of employees WHO ALREADY HAVE a target period
        $withTargetPeriod = \App\Models\TargetPeriod::where('office_id', $user->office_id)
            ->where('semester', $semester)
            ->where('year', $year)

            ->pluck('control_no')
            ->toArray();

        // Filter out employees who already have a target period
        $employeesWithoutIpcr = $employees->whereNotIn('ControlNo', $withTargetPeriod)->values();

        return response()->json($employeesWithoutIpcr);
    }
}
