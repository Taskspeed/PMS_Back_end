<?php

namespace App\Http\Controllers;

use App\Events\TargetPeriodLockEvent;
use App\Http\Requests\Library\TargetPeriodStoreRequest;
use App\Http\Requests\Library\TargetPeriodUpdateRequest;
use App\Models\TargetPeriodLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TargetPeriodController extends Controller
{
    //crud

    // fetch target periods
    public function getTargetPeriods()
    {
        $targetPeriods = DB::table('target_period_lib as tp')
            ->leftJoin('target_period_locks as tpl', function ($join) {
                $join->on('tp.semester', '=', 'tpl.semester')
                    ->on('tp.year', '=', 'tpl.year');
            })
            ->select(
                'tp.id',
                'tp.semester',
                'tp.year',
                'tp.created_at',
                'tp.updated_at',
                'tpl.status',
                'tpl.date',
                'tpl.lock_by'
            )
            ->get();

        return response()->json($targetPeriods);
    }

    // store target period
    public function storeTargetPeriod(TargetPeriodStoreRequest $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $validated = $request->validated();

        $targetPeriod = TargetPeriodLib::create($validated);

        TargetPeriodLockEvent::dispatch($targetPeriod, $user);

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
