<?php

namespace App\Listeners;

use App\Events\OpcrEvent;
use App\Models\Office;
use App\Models\OfficeOpcr;
use App\Models\OfficeOpcrRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class Opcr
{
    /**
     * Create the event listener.
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Handle the event.
     */
    // public function handle(object $event): void
    // {
    //     //
    // }

    public function handle(OpcrEvent $event): void
    {
        $records = $event->opcr;
        $year = $event->year;
        $semester = $event->semester;
         $user = $event->user; // This IS the TargetPeriod model

        $officeId = $records[0]->office_id;

        $office = Office::find($officeId);

        $officeOpcr = OfficeOpcr::create([
            'office_id' => $officeId,
            'office_name' => $office->name,
            'year' => $year,
            'semester' => $semester,
        ]);

        OfficeOpcrRecord::create([
            'office_opcr_id' => $officeOpcr->id,
            'date' => Carbon::now()->format('Y-m-d'), // ✅ standard DB date format
            'status' => 'Draft',
            'remarks' => 'Created',
            'processed_by' => $user->id,  
            'processed_by_name' => $user->name,  
        ]);

        Log::info('OPCR saved successfully!');
    }
}
