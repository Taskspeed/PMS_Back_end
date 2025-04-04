<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\F_outpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Output\Output;

class FOutpotController extends Controller
{

    // Handle MFO Creation
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'mfo_id' => 'required|exists:mfos,id',
            'name' => 'required|string|max:255',
        ]);

        $output = F_outpot::create([
            'mfo_id' => $request->mfo_id,
            'name' => $request->name,

        ]);

        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' =>  $output->name])
            ->log('Output created');
        return response()->json(['message' => 'Output created successfully', ' Output' =>  $output]);
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'mfo_id' => 'required|exists:mfos,id',
            'name' => 'required|string|max:255',
        ]);

        // Find the output by ID
        $output = F_outpot::findOrFail($id);

        // Update the output
        $output->update([
            'mfo_id' => $request->mfo_id,
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


}
