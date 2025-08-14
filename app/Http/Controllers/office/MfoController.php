<?php

namespace App\Http\Controllers\office;

use App\Models\mfo;
use App\Models\User; // Ensure this is the Eloquent User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\F_category;

class MfoController extends Controller
{


    // Handle MFO Creation
    public function store(Request $request)  // store
    {
        // Validate the request
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'name' => 'required|string|max:255',
            'f_category_id' => 'required|exists:f_categories,id',
        ]);

        $mfo = Mfo::create([
            'office_id' => $request->office_id,
            'name'=>$request->name,
            'f_category_id'=>$request->f_category_id,
        ]);

        activity()
            ->performedOn($mfo)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $mfo->name])
            ->log('MFO Created');
        return response()->json(['message' => 'MFO created successfully', 'mfo' => $mfo]);
    }


   



    public function update(Request $request, $id) // update
    {
        // validate the request
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'name' => 'required|string|max:255',
            'f_category_id' => 'required|exists:f_categories,id',
        ]);

        // find the MFO by id
        $mfo = Mfo::findOrFail($id);

        // update the MFO
        $mfo->update([
            'office_id' => $request->office_id,
            'name' => $request->name,
            'f_category_id' => $request->f_category_id,
        ]);

        // Log activity
        activity()
            ->performedOn($mfo)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $mfo->name])
            ->log('MFO updated');

        return response()->json([
            'message' => 'MFO updated successfully',
            'mfo' => $mfo->fresh() // Return fresh data from database
        ]);
    }


     // Fetch only active (non-deleted) mfo
         public function index(){

          $mfos = mfo::whereNull('deleted_at')->get();

        return response()->json($mfos);
    }

    // fetch_mfo_SoftDeleted
    public function getSoftDeleted(){

        $mfos = mfo::onlyTrashed()->get();

        return response()->json($mfos);
    }

    // softDelete for MFO
    public function softDelete($id){

        $mfos = mfo::findOrFail($id);
        $mfos->delete();

        activity()
            ->performedOn($mfos)
            ->causedBy(Auth::user())
            ->withProperties(['name' =>   $mfos->name])
            ->log('MFO soft deleted');

        return response()->json(['message' => 'MFO soft deleted successfully']);

    }

    // restore soft-deleted data
    public function restore($id){

        $mfos = mfo::onlyTrashed()->findOrFail($id);
        $mfos->restore();

        activity()
            ->performedOn($mfos)
            ->causedBy(Auth::user())
            ->withProperties(['name' =>  $mfos->name])
            ->log('MFO restored');

        return response()->json(['message' => 'MFO restored successfully']);
    }
}
