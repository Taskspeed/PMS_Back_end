<?php

namespace App\Listeners;

use App\Events\OpcrEvent;
use App\Models\OfficeOpcr;
use App\Models\OfficeOpcrRecord;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Models\Office;

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
            'date' => now()->format('m-d-Y'),
            'status' => 'Draft',
            'remarks' => 'Created',
            'reviewed_by' => null,
        ]);

        Log::info('OPCR saved successfully!');
    }
}
