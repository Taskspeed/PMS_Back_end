<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfficeOpcrPendingResource;
use App\Services\DashboardService;
use Illuminate\Database\Eloquent\Collection;
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

    //list of the opcr pending
    public function listOfOpcrPending($semester, $year)
    {

        /** @var Collection $result */
        $result  =  $this->dashboardService->opcrPending($semester, $year);

        if ($result->isEmpty()) {
            return response()->json([
                'message' => 'There is no data available yet.'
            ], 200); // use 200,
        }

        return OfficeOpcrPendingResource::collection($result);
    }




}
