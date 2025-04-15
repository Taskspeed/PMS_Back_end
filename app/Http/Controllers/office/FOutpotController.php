<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\F_outpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Output\Output;

class FOutpotController extends Controller
{

    public function getAllOutputs(Request $request)
    {
        $office_id = $request->input('office_id');

        $outputs = F_outpot::with(['category', 'mfo'])
            ->when($office_id, function ($query) use ($office_id) {
                $query->where('office_id', $office_id);
            })
            ->get();

        return response()->json($outputs);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'f_category_id' => 'required|exists:f_categories,id',
            'office_id' => 'required|exists:offices,id'
        ]);

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


    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            // 'mfo_id' => 'required|exists:mfos,id',
            'name' => 'required|string|max:255',
        ]);

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


    // Fetch only active (non-deleted) outputs
    public function index()
    {
        $outputs = F_outpot::whereNull('deleted_at')->get(); // Only fetch non-deleted data
        return response()->json($outputs);
    }

    // fetch_output_SoftDeleted
    public function getSoftDeleted()
    {
        $outputs = F_outpot::onlyTrashed()->get(); // Fetch only soft-deleted records

        return response()->json($outputs);
    }


    // softDelete for outputs
    public function softDelete($id)
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



    // Restore soft-deleted data
    public function restore($id)
    {
        $output = F_outpot::onlyTrashed()->findOrFail($id);
        $output->restore();

        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $output->name])
            ->log('Output restored');

        return response()->json(['message' => 'Output restored successfully']);
    }


    public function Outputs(Request $request)
    {
        $outputs = F_outpot::all();

        return response()->json($outputs);
    }

}
