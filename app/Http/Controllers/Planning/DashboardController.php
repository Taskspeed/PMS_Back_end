<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfficeOpcrPendingResource;
use App\Services\DashboardService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    use ApiResponseTrait;

    protected $dashboardService;


    public function __construct(DashboardService $dashboardService)
    {
        return $this->dashboardService = $dashboardService;
    }


    // number of opcr status
    public function numberOfStatus($semester, $year)
    {

        $result  =  $this->dashboardService->status($semester, $year);

        return $result;
    }

    //list of the opcr pending
    public function listOfOpcrPending($semester, $year)
    {

        /** @var Collection $result */
        $result  =  $this->dashboardService->opcrPending($semester, $year);

        if ($result->isEmpty()) {
            return  $this->infoMessage('No records found', 200);
        }

        return OfficeOpcrPendingResource::collection($result);
    }
}
