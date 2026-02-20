<?php

namespace App\Services;

use App\Models\office;
use App\Models\TargetPeriod;
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

    const STATUS_MONITORED = 'monitored';
    const STATUS_PENDING = 'Pending';


    // updating the unitworkplan of office
    public function unitworkplanStatus(array $validatedData)
    {
        // Get office from database
        $office = office::findOrFail($validatedData['office_id']);

        // Secure override
        $validatedData['office_name'] = $office->name;

        // Store tracker
        $tracker = Tracker::create($validatedData);

        // Apply additional business logic
        $this->updateTargetPeriodStatusIfMonitored($tracker);

        return $tracker;
    }


    /**
     * Update target period employee status if tracker is monitored
     */
    private function updateTargetPeriodStatusIfMonitored(Tracker $tracker): void
    {
        // Only run if monitored
        if (strtolower($tracker->status) !== self::STATUS_MONITORED) {
            return;
        }

        TargetPeriod::where('year', $tracker->year)
            ->where('semester', $tracker->semester)
            ->where('office_id', $tracker->office_id)
            ->update([
                'status' => 'Pending'
            ]);
    }
}
