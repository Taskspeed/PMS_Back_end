<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //

    protected $dashboardService;


    public function __construct(DashboardService $dashboardService)
    {
         return $this->dashboardService = $dashboardService;
    }


    // number of opcr status
    public function numberOfStatus($semester,$year){


    $result  =  $this->dashboardService->status($semester, $year);


    return $result;

    }




}
