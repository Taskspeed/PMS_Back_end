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
    public function store(Request $request)
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

    public function index_data(){

    $data = mfo::all();
    return response()->json($data);
    }

    public function getUserData(Request $request)
    {
        // Get authenticated user
        $user = Auth::user();

        // Check if authenticated
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Load office relationship
        if ($user instanceof \App\Models\User) {
            $user->load('office');
        }

        // Validate if user has an office
        if (!$user->office) {
            return response()->json(['error' => 'User does not have an associated office'], 400);
        }

        // Fetch MFOs with category name using eager loading
        $mfos = Mfo::with('category:id,name')
            ->where('office_id', $user->office_id)
            ->get();

        return response()->json([
            'user' => $user,
            'mfos' => $mfos
        ]);
    }


    public function update(Request $request, $id){

        // validate the request
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'name' => 'required|string|max:255',
            'f_category_id' => 'required|exists:f_categories,id',
        ]);

        //find the output by id
        $mfos = mfo::findOrFail($id);

        //update the mfos
        $mfos->update([
            'office_id'=> $request->office_id,
            'name'=> $request->name,
            'f_category_id' => $request->f_category_id,
        ]);
        // Log activity
        activity()
            ->performedOn($mfos)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $mfos->name])
            ->log('MFO updated');

        return response()->json(['message' => 'MFO updated successfully', 'mfo' => $mfos]);
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
