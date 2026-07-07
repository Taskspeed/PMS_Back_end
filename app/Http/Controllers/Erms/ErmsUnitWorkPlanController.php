<?php

namespace App\Http\Controllers\Erms;

use App\Http\Controllers\Controller;
use App\Services\Erms\ErmsUnitWorkPlanService;


class ErmsUnitWorkPlanController extends Controller
{
    //
    protected ErmsUnitWorkPlanService $ErmsUnitWorkPlanService;

    public function __construct(ErmsUnitWorkPlanService $ErmsUnitWorkPlanService)
    {
       $this->ErmsUnitWorkPlanService = $ErmsUnitWorkPlanService;
    }

    // find the Department Head and supervisory on office
    public function findManagerial(int $year, string $semester, string $mfo, int $officeId)
    {

        $result = $this->ErmsUnitWorkPlanService->supervisoryDeductionOfSuccessIndicator($year, $semester, $mfo, $officeId);

        return $result;
    }
}

