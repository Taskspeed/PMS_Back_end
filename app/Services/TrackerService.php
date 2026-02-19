<?php

namespace App\Services;

use App\Models\office;
use App\Models\Tracker;

class TrackerService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    
    // updating the unitworkplan of office
    public function unitworkplanStatus($validatedData)
    {
        // Get office from database
        $office = office::findOrFail($validatedData['office_id']);

        // Override office_name from database (secure way)
        $validatedData['office_name'] = $office->name;

        // Create tracker record
        return Tracker::create($validatedData);
    }
}
