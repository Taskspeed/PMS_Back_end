<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IpcrController extends Controller
{


    // // getting the ipcr of the employee based on controlno and year
    // public function getIpcr($controlNo, $year, $semester)
    // {
    //     $employee = Employee::where('ControlNo', $controlNo)
    //         ->with([
    //             'targetPeriods' => function ($q) use ($year, $semester) {
    //             $q->where('year', $year)
    //                 ->where('semester', $semester)
    //                     ->with(['performanceStandards', 'standardOutcomes']);
    //             }
    //         ])
    //         ->first();

    //     if (!$employee) {
    //         return response()->json(['message' => 'Employee not found'], 404);
    //     }

    //     return response()->json($employee);
    // }
}
