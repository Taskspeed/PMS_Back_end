<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\vwActive;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    //

    public function dashboard()
    {
        $statuses = [
            'ELECTIVE',
            'APPOINTED',
            'CO-TERMINOUS',
            'TEMPORARY',
            'REGULAR',
            'CASUAL',
            'CONTRACT OF SERVICE',
            'HONORARIUM BASED'
        ];
        $counts = vwActive::select('status')
            ->whereIn('status', $statuses)
            ->get()
            ->groupBy(function ($item) {
                return strtoupper($item->status); // normalize casing
            })
            ->map(function ($group) {
                return count($group);
            });

        // Ensure all statuses are present even if count is 0
        $result = collect($statuses)->mapWithKeys(function ($status) use ($counts) {
            return [$status => $counts->get($status, 0)];
        });

        return response()->json($result);
    }
}
