<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
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

    // get the number of employee base of status
    // old data
    public function previousEmployeeStatus(DashboardService $dashboardService,$year,$semester)
    {
        $employee = $dashboardService->filterEmployeeStatus($year,$semester);

        return response()->json($employee);
    }

    // fetching the list of data available employee status
    public function fetchEmployeeStatus(DashboardService $dashboardService)
    {
        $employee = $dashboardService->availableDataEmployeeStatus();

        return response()->json($employee);
    }


}
