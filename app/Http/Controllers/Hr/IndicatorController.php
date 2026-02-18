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
     public function getIndicator(){

        $indicator = Indicator::all();

        return response()->json($indicator);

     }

    // store
    public function storeIndicator(IndicatorStoreRequest $request)
    {
        // $validation = $request->validate([
        //     'indicator_name' => 'required|string'
        // ]);

        $indicator = Indicator::create([
            'indicator_name' => $request->indicator_name
        ]);

        return response()->json($indicator);
    }

    // update
    public function updateIndicator($indicatorId, IndicatorUpdateRequest $request)
    {
        $indicator = Indicator::findOrFail($indicatorId);

        // Validate the incoming data
        // $validated = $request->validate([
        //     'indicator_name' => 'required|string'
        // ]);

        // Update using validated data
        $indicator->update(['indicator_name' => $request->indicator_name]);


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
            'success' => true ,
            'message' => 'Indicator deleted successfully.',
            'deleted_id' => $indicatorId
        ]);
    }
}
