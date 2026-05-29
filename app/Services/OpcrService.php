<?php

namespace App\Services;

use App\Events\OpcrEvent;
use App\Models\opcr;
use App\Models\Employee;
use App\Models\OfficeOpcr;
use App\Models\OfficeOpcrRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use function Symfony\Component\Clock\now;

class OpcrService
{
    
    // store opcr
    public function storeAllotedBudget(?array $validatedData)
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        DB::beginTransaction();

        try {

            $records = [];

            foreach ($validatedData['data'] as $data) {

                $records[] = opcr::create([
                    'office_id' => $officeId,
                    'performance_standard_id' => $data['performance_standard_id'],
                    'budget' => $data['budget'],
                    'accountable' => $data['accountable'],
                    // 'accomplishment' => $data['accomplishment'],
                ]);
            }

            DB::commit();

            //Execute
            // dispatch event
            OpcrEvent::dispatch(
                $records,
                $validatedData['year'],
                $validatedData['semester'],
                $user
            );


            return $records;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // update AllotedBudget
    public function updateAllotedBudget(?array $validatedData)
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        DB::beginTransaction();

        try {
            $records = [];

            foreach ($validatedData as $data) {
                // ✅ Capture the returned model instance
                $record = opcr::updateOrCreate(
                    [
                        'office_id' => $officeId,
                        'performance_standard_id' => $data['performance_standard_id'],
                    ],
                    [
                        'budget' => $data['budget'],
                        'accountable' => $data['accountable'],
                        'accomplishment' => $data['accomplishment'],
                    ]
                );

                // ✅ Add to the records array
                $records[] = $record;
            }

            DB::commit();

            return $records; // ✅ Now this will have data!

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // storing status of opcr
    public function opcrStoreStatus(?array $validated)
    {
        $user =  Auth::user();

        $opcr = OfficeOpcrRecord::create([
            'office_opcr_id' => $validated['office_opcr_id'],
            'date' => now()->format('m-d-Y'),
            'status' => $validated['status'],
            'remarks' => $validated['remarks'],
            'processed_by' => $user->id,
            'processed_by_name' => $user->name,
        ]);

        return response()->json($opcr);
    }

    // list of  opcr Received
    public function opcrReceived(string $semester, int $year)
    {

        // opcr of office
        $data = OfficeOpcr::select(
            'office_opcrs.id',
            'office_opcrs.office_id',
            'office_opcrs.office_name', // add your fields here
            'office_opcrs.semester',
            'office_opcrs.year'
        )->with([
            'officeOpcrRecordLastestRecord' => function ($query) {
                $query->select(
                    'office_opcrs_records.id',
                    'office_opcrs_records.office_opcr_id',
                    'office_opcrs_records.date',
                    'office_opcrs_records.status'
                );
            }, // eager load office head per office
            'officeHead' => function ($query) {
                $query->select(
                    'employees.id',
                    'employees.office_id',
                    'employees.name',
                    'employees.job_title',
                    'employees.ControlNo'
                );
            },

        ])
            ->where('semester', $semester)
            ->where('year', $year)
            ->whereHas('officeOpcrRecordLastestRecord', function ($query) {
                $query->where('status', 'Received');
            })->get();

        return $data;
    }
}
