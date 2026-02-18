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

class MfoController extends Controller
{

    // getting the mfo  of the user
    public function getUserMfo()
    {
        $user = Auth::user();
        $office = $user->office;

        // Get all categories having either MFOs or outputs for this office
        $categoryIds = $office->mfos()->pluck('f_category_id')
            ->merge($office->f_outpots()->pluck('f_category_id'))
            ->unique()
            ->toArray();

        $categories = \App\Models\F_category::whereIn('id', $categoryIds)->get();

        $result = [
            'categories' => []
        ];

        foreach ($categories as $category) {
            // MFOs for this office and category
            $mfos = $office->mfos()->where('f_category_id', $category->id)->get();
            $mfosArr = [];
            foreach ($mfos as $mfo) {
                $mfosArr[] = [
                    'id' => $mfo->id,
                    'name' => $mfo->name,
                    'outputs' => $mfo->outpots()->get()->toArray(),
                ];
            }

            // Outputs directly under category+office (not attached to any MFO)
            $outputs = $office->f_outpots()
                ->where('f_category_id', $category->id)
                ->whereNull('mfo_id')
                ->get();

            $result['categories'][] = [
                'id' => $category->id,
                'name' => $category->name,
                'mfos' => $mfosArr,
                'category_outputs' => $outputs,
            ];
        }

        return response()->json($result);
    }



    // Handle MFO Creation
    public function storeMfo(MfoStoreRequest $request)  // store
    {
        // Validate the request

        $mfo = mfo::create([
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




    public function updateMfo(MfoUpdateRequest $request, $id) // update
    {
        // validate the request
        // $request->validate([
        //     'office_id' => 'required|exists:offices,id',
        //     'name' => 'required|string|max:255',
        //     'f_category_id' => 'required|exists:f_categories,id',
        // ]);

        // find the MFO by id
        $mfo = mfo::findOrFail($id);

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
