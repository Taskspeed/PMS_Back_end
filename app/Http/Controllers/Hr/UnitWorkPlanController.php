<?php

namespace App\Http\Controllers\Hr;


use App\Http\Controllers\Controller;
use App\Http\Requests\TrackerRequest;
use App\Services\TrackerService;

class UnitWorkPlanController extends Controller
{

    // monitoring the unitworkplan by hr
    //  status if unitworkplan of office
    public function monitorUnitworkplan(TrackerRequest $request,TrackerService $trackerService) {
        $validatedData = $request->validated();

        $unitworkplan = $trackerService->unitworkplanStatus($validatedData);

        return response()->json([
            'message' => 'Unit workplan status created successfully.',
            'data' => $unitworkplan
        ], 201);
    }


}
