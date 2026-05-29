<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use Illuminate\Http\Request;

class RankController extends Controller
{

    // fetch rank
    public function getRank()
    {

        $rank = Rank::select('id', 'rank_name')->get();

        return response()->json($rank);
    }

    // store
    public function storerank(Request $request)
    {
        $validation = $request->validate([
            'rank_name' => 'required|string'
        ]);

        $rank = Rank::create([
            'rank_name' => $validation['rank_name']
        ]);

        return response()->json($rank);
    }

    // update
    public function updaterank(int $rankId, Request $request)
    {
        $rank = Rank::findOrFail($rankId);

        // Validate the incoming data
        $validated = $request->validate([
            'rank_name' => 'required|string'
        ]);

        // Update using validated data
        $rank->update($validated);


        return response()->json([
            'success' => true,
            'message' => 'rank Updated successfully.',

        ]);
    }

    // delete
    public function deleterank(int $rankId)
    {
        $rank = rank::findOrFail($rankId);

        $rank->delete(); // ✔ correct

        return response()->json([
            'success' => true,
            'message' => 'rank deleted successfully.',
            'deleted_id' => $rankId
        ]);
    }
}
