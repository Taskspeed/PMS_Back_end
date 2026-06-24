<?php

namespace App\Http\Controllers;

use App\Http\Requests\Spms\UpdateIpcrRequest;
use App\Http\Requests\Spms\UpdateOpcrRequest;
use App\Http\Requests\Spms\UpdateUnitWorkPlanRequest;
use App\Http\Requests\syncUnitWorkPlanIpcrOpcrRequest;
use App\Services\UpdateSpmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpmsProcessController extends Controller
{
    //

    protected UpdateSpmsService $updateService;

    public function __construct(UpdateSpmsService $updateService)
    {
      $this->updateService = $updateService;
    }

    // update unit work plan
    public function updateUnitWorkPlan(UpdateUnitWorkPlanRequest $request){

        $authUser = Auth::user();

        $validatedData = $request->validated();
        $data = $this->updateService->unitWorkPlan($validatedData, $authUser);

        return $data;

    }

    // update opcr
    public function updateOpcr(UpdateOpcrRequest $request){

        $authUser = Auth::user();

        $validatedData = $request->validated();
        $data = $this->updateService->opcr($validatedData, $authUser);

        return $data;

    }

    // update ipcr
    public function updateIpcr(UpdateIpcrRequest $request){

        $authUser = Auth::user();

        $validatedData = $request->validated();
        $data = $this->updateService->ipcr($validatedData, $authUser);

        return $data;

    }

    // update unit work plan calibrated / validated  also the ipcr and opcr
    public function syncUnitWorkPlanIpcrOpcr(syncUnitWorkPlanIpcrOpcrRequest $request){

        $authUser = Auth::user();

        $validatedData = $request->validated();
        $data = $this->updateService->updateUnitWorkPlanAndRelatedTargets($validatedData, $authUser);

        return $data;

    }
}
