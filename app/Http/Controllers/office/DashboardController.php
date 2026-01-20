<?php

namespace App\Http\Controllers\office;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //

    //getting the  total employee in the office
    public function getTotalEmployee(Request $request)
    {
        $user = Auth::user();

        $officeId = $user->office_id;

        // Get semester & year from request
        // $semester = $request->input('semester');   // example: January-June / July-December
        // $year = $request->input('year');           // example: 2025

        // Assuming you have an Employee model with an 'office_code' field
        $totalEmployees = \App\Models\Employee::where('office_id', $officeId)->count();

        return response()->json([
            'total_employees' => $totalEmployees]);
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

    // unitworkplan status by office2/group/division/section/unit

    
}
