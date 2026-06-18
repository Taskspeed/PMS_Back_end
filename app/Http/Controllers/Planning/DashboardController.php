<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfficeOpcrPendingResource;
use App\Services\DashboardService;
use App\Services\OpcrService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    use ApiResponseTrait;

    protected  DashboardService $dashboardService;
    protected  OpcrService $opcrService;


    public function __construct(DashboardService $dashboardService,OpcrService $opcrService)
    {
       $this->dashboardService = $dashboardService;
       $this->opcrService = $opcrService;
    }


    // number of opcr status
    public function numberOfStatus(string $semester, int $year)
    {

        $result  =  $this->dashboardService->status($semester, $year);

        return $result;
    }

    //list of the opcr draft
    public function listOfOpcrPending(string $semester, int $year)
    {

        /** @var Collection $result */
        $result  =  $this->dashboardService->opcrPending($semester, $year);

        if ($result->isEmpty()) {
            return  $this->infoMessage('No records found', 200);
        }

         return $this->successMessage(
            OfficeOpcrPendingResource::collection($result),
            'Successfully fetched',
            200
        );
    }

        //list of the opcr draft
    public function listOfOpcrReceived(string $semester, int $year)
    {

        /** @var Collection $result */
        $result  =  $this->opcrService->opcrReceived($semester, $year);

        if ($result->isEmpty()) {
            return  $this->infoMessage('No records found', 200);
        }

       
         return $this->successMessage(
            OfficeOpcrPendingResource::collection($result),
            'Successfully fetched',
            200
        );
    }
}
