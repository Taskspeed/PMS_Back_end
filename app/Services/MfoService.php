<?php

namespace App\Services;

use App\Models\mfo;
use Illuminate\Support\Facades\Auth;

class MfoService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }


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

        return $result;
    }

    public function store($validated)  // store
    {

        $mfo = mfo::create($validated);

        activity()
            ->performedOn($mfo)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $mfo->name])
            ->log('MFO Created');
        return  $mfo;
    }

    public function update($id,$validated) // update
    {

        // find the MFO by id
        $mfo = mfo::findOrFail($id);

        // update the MFO
        $mfo->update($validated);

        // Log activity
        activity()
            ->performedOn($mfo)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $mfo->name])
            ->log('MFO updated');

        return $mfo;
    }
}
