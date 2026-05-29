<?php

namespace App\Services;

use App\Models\F_outpot;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Auth;

class OutputService
{


    public function store(?array $validated)
    {
        $output = F_outpot::create($validated);

        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $output->name])
            ->log('Output created');

        return $output;

    }

    public function update($validated, $id)
    {

        // Find the output by ID
        $output = F_outpot::findOrFail($id);

        // Update the output
        $output->update($validated);

        // Log activity
        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' => $output->name])
            ->log('Output updated');

        return  $output;
    }
}
