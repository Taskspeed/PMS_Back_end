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
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    // get the opcr Office Head
    // public function opcrOfficeHead($controlNo, $semester, $year){

    //     $officeHeadOpcr = Employee::select('id', 'ControlNo', 'name','office_id','office')
    //         ->where('ControlNo', $controlNo)

    //         ->whereHas('targetPeriods', function ($q) use ($year, $semester) {
    //             $q->where('year', $year)
    //                 ->where('semester', $semester);
    //         })

    //         ->with([
    //             'targetPeriods' => function ($queryTargetPeriod) use ($year, $semester) {
    //                 $queryTargetPeriod
    //                     ->select('id', 'control_no', 'semester', 'year', 'status')
    //                     ->where('year', $year)
    //                     ->where('semester', $semester)
    //                     ->with([
    //                         'performanceStandards' => function ($queryPerformanceStandard) {
    //                             $queryPerformanceStandard->select(
    //                                 'id',
    //                                 'target_period_id',
    //                                 'category',
    //                                 'mfo',
    //                                 'output',
    //                                 'success_indicator',
    //                                 'core',
    //                                 'technical',
    //                                 'leadership',
    //                             )
    //                                 ->with([
    //                                     'opcr' => function ($queryopcr) {
    //                                         $queryopcr->select(
    //                                             'id',
    //                                             'performance_standard_id', // REQUIRED FK
    //                                             'competency',
    //                                             'budget',
    //                                             'accountable',
    //                                             // 'accomplishment',
    //                                             // 'rating_q',
    //                                             // 'rating_e',
    //                                             // 'rating_t',
    //                                             // 'rating_a',
    //                                             'profiency',
    //                                             'remarks',

    //                                         );
    //                                     }
    //                                 ]);
    //                         }
    //                     ]);
    //             }
    //         ])
    //         ->first();

    //     $opcr_status = OfficeOpcr::with(['officeOpcrRecordLastestRecord' => function ($query){
    //         $query->select(
    //             'office_opcrs_records.id',
    //             'office_opcrs_records.office_opcr_id',
    //             'office_opcrs_records.date',
    //             'office_opcrs_records.status',
    //             'office_opcrs_records.remarks',
    //             'office_opcrs_records.processed_by',
    //         );
    //     }])
    //     ->select('id','office_id','office_name','semester','year')
    //     ->where('office_id', $officeHeadOpcr->office_id)
    //     ->where('semester',$semester)->where('year',$year)->first();


    //     return [
    //         'employee'    => $officeHeadOpcr,
    //         'opcr_status' => $opcr_status,
    //     ];

    // }


    // store opcr
    public function storeAllotedBudget($validatedData)
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        DB::beginTransaction();

        try {

            $records = [];

            // foreach ($validatedData as $data) {

            //     $records[] = opcr::create([
            //         'office_id' => $officeId,
            //         'performance_standard_id' => $data['performance_standard_id'],
            //         'budget' => $data['budget'],
            //         'accountable' => $data['accountable'],
            //         'accomplishment' => $data['accomplishment'],
            //         // 'remarks' => $data['remarks'],
            //     ]);
            // }
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
    public function updateAllotedBudget($validatedData)
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
    public function opcrStoreStatus($validated)
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
    public function opcrReceived($semester, $year)
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
