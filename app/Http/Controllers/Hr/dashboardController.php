<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\employeeStatusRequest;
use App\Models\vwActive;
use App\Services\DashboardService;

class dashboardController extends Controller
{

    // get the number of employee base of status
    public function currentEmployeeStatus(DashboardService $dashboardService)
    {

    $employee = $dashboardService->currentEmployee();

    return response()->json($employee);

    }

    // store the status of employee
    // public function employeeStatus(employeeStatusRequest $request, DashboardService $dashboardService){


    // $validated = $request->validate();

    //  $employeeStatus = $dashboardService->storeEmployeeStatus($validated);

    //  return response()->json($employeeStatus);

    // }
}
