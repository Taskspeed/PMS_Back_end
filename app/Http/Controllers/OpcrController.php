<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit_work_plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OpcrController extends Controller
{
    //

    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        // Get unique divisions with work plans for this office
        $divisions = Unit_work_plan::where('office_id', $user->office_id)
            ->select('division', 'target_period', 'year', 'status', DB::raw('MAX(created_at) as created_at'))
            ->groupBy('division', 'target_period', 'year', 'status')
            ->orderBy('division')
            ->orderBy('year')
            ->orderBy('target_period')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'division' => $item->division,
                    'targetPeriod' => $item->target_period . ' ' . $item->year,
                    'dateCreated' => $item->created_at->format('F j, Y'),
                    // 'status' => $item->status ?? 'Draft'
                ];
            });

        return response()->json($divisions);
    }
}
