<?php

namespace App\Http\Controllers;

use App\Http\Requests\Library\TargetPeriodStoreRequest;
use App\Http\Requests\Library\TargetPeriodUpdateRequest;
use Illuminate\Http\Request;
use App\Models\TargetPeriodLib;

class TargetPeriodController extends Controller
{
    //crud

    // fetch target periods
    public function getTargetPeriods()
    {

        $targetPeriods =TargetPeriodLib::all();
        return response()->json($targetPeriods);

    }

    // store target period
    public function storeTargetPeriod(TargetPeriodStoreRequest $request)
    {
        $validated = $request->validated();

        $targetPeriod = TargetPeriodLib::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Target period created successfully',
            'data' => $targetPeriod

        ]);
    }

    // updating target period
    public function updateTargetPeriod(TargetPeriodUpdateRequest $request , $targetPeriodId)
    {
        $validated = $request->validated();

        $targetPeriod = TargetPeriodLib::findOrFail($targetPeriodId);

        $targetPeriod->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Target period updated successfully',
            'data' => $targetPeriod

        ]);
    }

    // delete target period
    public function deleteTargetPeriod($targetPeriodId)
    {
        $targetPeriod = TargetPeriodLib::findOrFail($targetPeriodId);

        $targetPeriod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Target period deleted successfully',
            'data' => $targetPeriod
        ]);
    }
}
