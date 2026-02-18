<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Http\Requests\Library\OutputRequest;

use App\Http\Requests\Library\OutputUpdateRequest;
use App\Models\F_outpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Output\Output;

class FOutpotController extends Controller
{


    public function storeOutput(OutputRequest $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'f_category_id' => 'required|exists:f_categories,id',
        //     'office_id' => 'required|exists:offices,id'
        // ]);

        $outputData = [
            'f_category_id' => $request->f_category_id,
            'office_id' => $request->office_id,
            'name' => $request->name
        ];

        // Only add mfo_id if it's present in the request
        if ($request->has('mfo_id')) {
            $request->validate(['mfo_id' => 'exists:mfos,id']);
            $outputData['mfo_id'] = $request->mfo_id;
        }

        $output = F_outpot::create($outputData);

        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $output->name])
            ->log('Output created');

        return response()->json([
            'message' => 'Output created successfully',
            'output' => $output
        ]);
    }


    public function updateOutput(OutputUpdateRequest $request, $id)
    {
        // Validate the request
        // $request->validate([
        //     // 'mfo_id' => 'required|exists:mfos,id',
        //     'name' => 'required|string|max:255',
        // ]);

        // Find the output by ID
        $output = F_outpot::findOrFail($id);

        // Update the output
        $output->update([
            // 'mfo_id' => $request->mfo_id,
            'name' => $request->name,
        ]);

        // Log activity
        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $output->name])
            ->log('Output updated');

        return response()->json(['message' => 'Output updated successfully', 'output' => $output]);
    }



    // softDelete for outputs
    public function deleteOutput($id)
    {
        $output = F_outpot::findOrFail($id); // Ensure the model uses SoftDeletes
        $output->delete(); // Soft deletes the record

        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' =>  $output->name])
            ->log('Output soft deleted');

        return response()->json(['message' => 'Output soft deleted successfully']);
    }



}
