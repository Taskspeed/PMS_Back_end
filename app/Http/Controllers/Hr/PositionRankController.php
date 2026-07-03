<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\PositionRank;
use Illuminate\Http\Request;

class PositionRankController extends Controller
{
    
    // fetch position rank
    public function getPositionRank()
    {

        $positionRank = PositionRank::select('id', 'position_name')->get();

        return response()->json($positionRank);
    }

    // store
    public function storePositionRank(Request $request)
    {
        $validation = $request->validate([
            'position_name' => 'required|string'
        ]);

        $positionRank = PositionRank::create([
            'position_name' => $validation['position_name']
        ]);

        return response()->json($positionRank);
    }

    // update
    public function updatePositionRank(int $positionRankId, Request $request)
    {
        $positionRank = PositionRank::findOrFail($positionRankId);

        // Validate the incoming data
        $validated = $request->validate([
            'position_name' => 'required|string'
        ]);

        // Update using validated data
        $positionRank->update($validated);


        return response()->json([
            'success' => true,
            'message' => 'Position rank  Updated successfully.',

        ]);
    }

    // delete
    public function deletePositionRank(int $positionRankId)
    {
        $positionRank = PositionRank::findOrFail($positionRankId);

        $positionRank->delete(); // ✔ correct

        return response()->json([
            'success' => true,
            'message' => 'Position rank deleted successfully.',
            'deleted_id' => $positionRankId
        ]);
    }
}
