<?php

namespace App\Http\Controllers\Hr;


use App\Http\Controllers\Controller;
use App\Http\Requests\TrackerRequest;
use App\Models\TargetPeriodLock;
use App\Services\TrackerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

use function Symfony\Component\Clock\now;

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



    // lock the target period
    public function lockTargetPeriod(Request $request){

        $validated = $request->validate([
            'semester' =>  'required',
            'year' =>  'required',
            'status' =>  'required',
        ]);

        $user = Auth::user();

        $lock = TargetPeriodLock::create([
            'semester' => $validated['semester'],
            'year' => $validated['year'],
            'status' => $validated['status'],
            'date' => now()->format('m/d/Y'),
            'lock_by' => $user->id,
        ]);

        return response()->json($lock);

    }


}
