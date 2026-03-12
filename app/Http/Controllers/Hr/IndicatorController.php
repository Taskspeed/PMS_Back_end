<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Library\IndicatorStoreRequest;
use App\Http\Requests\Library\IndicatorUpdateRequest;
use App\Models\Indicator;
use Illuminate\Http\Request;

class IndicatorController extends Controller
{

    // fetch
    public function getIndicator()
    {

        $indicator = Indicator::with('category')->get();

        return response()->json($indicator);
    }

    // store
    public function storeIndicator(IndicatorStoreRequest $request)
    {
        $validated = $request->validated();

        $indicator = Indicator::create($validated);

        return response()->json($indicator);
    }

    // update
    public function updateIndicator($indicatorId, IndicatorUpdateRequest $request)
    {

        $validated = $request->validated();
        $indicator = Indicator::findOrFail($indicatorId);


        // Update using validated data
        $indicator->update($validated);


        return response()->json([
            'success' => true,
            'message' => 'Indicator Updated successfully.',

        ]);
    }

    // delete
    public function deleteIndicator($indicatorId)
    {
        $indicator = Indicator::findOrFail($indicatorId);

        $indicator->delete(); // ✔ correct

        return response()->json([
            'success' => true,
            'message' => 'Indicator deleted successfully.',
            'deleted_id' => $indicatorId
        ]);
    }
}
