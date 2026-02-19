<?php

namespace App\Services;

use App\Models\F_outpot;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Auth;

class OutputService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }


    public function store(array $data)
    {
        // $outputData = [
        //     'f_category_id' => $request->f_category_id,
        //     'office_id' => $request->office_id,
        //     'name' => $request->name
        // ];

        // Only add mfo_id if it's present in the request
        // if ($request->has('mfo_id')) {
        //     $request->validate(['mfo_id' => 'exists:mfos,id']);
        //     $outputData['mfo_id'] = $request->mfo_id;
        // }

        $output = F_outpot::create($data);

        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $output->name])
            ->log('Output created');

        return $output;

    }


    public function update($request, $id)
    {

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

        return  $output;
    }
}
