<?php

namespace App\Http\Controllers\office;

use App\Models\mfo;
use App\Models\User; // Ensure this is the Eloquent User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Library\MfoStoreRequest;
use App\Http\Requests\Library\MfoUpdateRequest;
use App\Models\F_category;
use App\Services\MfoService;

class MfoController extends Controller
{

    // getting the mfo  of the user
    public function Mfo(MfoService $mfoService)
    {

        $user = $mfoService->getUserMfo();

        return response()->json($user);
    }

    // addMfo
    public function addMfo(MfoStoreRequest $request,MfoService $mfoService)  // store
    {
        $validated = $request->validate();

        $mfo = $mfoService->store($validated);

        activity()
            ->performedOn($mfo)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $mfo->name])
            ->log('MFO Created');
        return response()->json(['message' => 'MFO created successfully', 'mfo' => $mfo]);
    }

    // update mfo
    public function updateMfo(MfoUpdateRequest $request, $id, MfoService $mfoService) // update
    {
        $validated = $request->validate();
        // find the MFO by id

        $mfo = $mfoService->update($id, $validated);

        return response()->json([
            'message' => 'MFO updated successfully',
            'mfo' => $mfo->fresh() // Return fresh data from database
        ]);
    }

    // Delete for MFO
    public function delete($id){

        $mfos = mfo::findOrFail($id);
        $mfos->delete();

        activity()
            ->performedOn($mfos)
            ->causedBy(Auth::user())
            ->withProperties(['name' =>   $mfos->name])
            ->log('MFO soft deleted');

        return response()->json(['message' => 'MFO soft deleted successfully']);

    }


}
