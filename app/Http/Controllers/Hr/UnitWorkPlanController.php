<?php

namespace App\Http\Controllers\Hr;


use App\Http\Controllers\Controller;
use App\Http\Requests\TrackerRequest;
use App\Services\TrackerService;

class UnitWorkPlanController extends Controller
{

    // monitoring the unitworkplan by hr
    //  status if unitworkplan of office
    public function updateUnitworkplan(TrackerRequest $request,TrackerService $trackerService) {

        $validated = $request->validated();

        $unitworkplan = $trackerService->unitworkplanStatus($validated);

        return response()->json([
            'message' => 'Unit workplan status created successfully.',
            'data' => $unitworkplan
        ], 201);
    }





}
