<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\OfficeOpcr;
use App\Models\TargetPeriod;
use App\Models\UnitWorkPlan;
use App\Models\vwActive;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardService
{

    use ApiResponseTrait;

    //-----------------------------HR------------------------------------------//

    // // getting the current employee
    // public function currentEmployee()
    // {
    //     $statuses = [
    //         'ELECTIVE',
    //         'APPOINTED',
    //         'CO-TERMINOUS',
    //         'TEMPORARY',
    //         'REGULAR',
    //         'CASUAL',
    //         'CONTRACTUAL',
    //         'HONORARIUM'
    //     ];
    //     $counts = vwActive::select('status')
    //         ->whereIn('status', $statuses)
    //         ->get()
    //         ->groupBy(function ($item) {
    //             return strtoupper($item->status); // normalize casing
    //         })
    //         ->map(function ($group) {
    //             return count($group);
    //         });

    //     // Ensure all statuses are present even if count is 0
    //     $result = collect($statuses)->mapWithKeys(function ($status) use ($counts) {
    //         return [$status => $counts->get($status, 0)];
    //     });

    //     return $result;
    // }



    // get the employee data base on the args year and semester
    // public function filterEmployeeStatus($year, $semester)
    // {

    //     $data = EmployeeStatus::where('year', $year)
    //         ->where('semester', $semester)
    //         ->first();

    //     if (!$data) {
    //         return response()->json([
    //             'message' => 'There is no data available yet.'
    //         ], 200); // use 200,
    //     }

    //     return response()->json($data);
    // }

    // // fetching the available data of employee status
    // public function availableDataEmployeeStatus()
    // {

    //     $data = EmployeeStatus::select('id', 'year', 'semester')
    //         ->orderByDesc('year')
    //         ->orderByDesc('semester')->get();

    //     return $data;
    // }

    public function dashboard($year,$semester){


        $statuses = [
            'ELECTIVE',
            'APPOINTED',
            'CO-TERMINOUS',
            'TEMPORARY',
            'REGULAR',
            'CASUAL',
            'CONTRACTUAL',
            'HONORARIUM',
            'LSB',
            'PROBATIONARY',
            'SUBSTITUTE',
            'JOB ORDER',
            'RE-ELECT',
            'EMERGENCY',
            'PERMANENT',
            'PROVISIONAL',
            'NOT KNOWN',
            'CONSULTANT',
        ];

        $counts = vwActive::select('status')
            ->whereIn('status', $statuses)
            ->get()
            ->groupBy(function ($item) {
                return strtoupper($item->status); // normalize casing
            })
            ->map(function ($group) {
                return count($group);
            });

        // Ensure all statuses are present even if count is 0
        $current_data = collect($statuses)->mapWithKeys(function ($status) use ($counts) {
            return [$status => $counts->get($status, 0)];
        });


        // filterEmployee status args $semester,$year
        $previous_data = EmployeeStatus::where('year', $year)
            ->where('semester', $semester)
            ->first();

        if (!$previous_data) {
            return response()->json([
                'message' => 'There is no data available yet.'
            ], 200); // use 200,
        }


        return response()->json([
            'current_status_of_employee' => $current_data,
            'previous_status_of_employee' =>   $previous_data,

        ]);


    }

    // end of the June and December store on the employee status table
    public function storeEmployeeStatus()
    {
        $now = Carbon::now();
        $year = $now->year;

        // Determine semester based on month
        $semester = $now->month <= 6 ? '1st semester' : '2nd semester';

        $statuses = [
            'ELECTIVE',
            'APPOINTED',
            'CO-TERMINOUS',
            'TEMPORARY',
            'REGULAR',
            'CASUAL',
            'CONTRACTUAL',
            'HONORARIUM',
            'LSB',
            'PROBATIONARY',
            'SUBSTITUTE',
            'JOB ORDER',
            'RE-ELECT',
            'EMERGENCY',
            'PERMANENT',
            'PROVISIONAL',
            'NOT KNOWN',
            'CONSULTANT',
        ];

        // Get counts per status from the view
        $counts = vwActive::select('status')
            ->get()
            ->groupBy(function ($item) {
                return strtoupper(trim($item->status));
            })
            ->map(function ($group) {
                return count($group);
            });

        // Map status names to database column names
        $columnMap = [
            'ELECTIVE'      => 'elective',
            'APPOINTED'     => 'appointed',
            'CO-TERMINOUS'  => 'co_terminous',
            'TEMPORARY'     => 'temporary',
            'REGULAR'       => 'regular',
            'CASUAL'        => 'casual',
            'CONTRACTUAL'   => 'contractual',
            'HONORARIUM'    => 'honorarium',
            'LSB'           => 'lsb',
            'PROBATIONARY'  => 'probationary',
            'SUBSTITUTE'    => 'substitute',
            'JOB ORDER'     => 'job_order',
            'RE-ELECT'      => 're_elect',
            'EMERGENCY'     => 'emergency',
            'PERMANENT'     => 'permanent',
            'PROVISIONAL'   => 'provisional',
            'NOT KNOWN'     => 'not_known',
            'CONSULTANT'    => 'consultant',
        ];

        // Build data array — map each status count to its column
        $data = [
            'year'           => $year,
            'semester'       => $semester,
            'total_employee' => vwActive::count(), // total all active employees
        ];

        foreach ($columnMap as $statusKey => $column) {
            $data[$column] = $counts->get($statusKey, 0); // 0 if status not found
        }

        // Save one row per year + semester (no duplicates)
        EmployeeStatus::updateOrCreate(
            [
                'year'     => $year,
                'semester' => $semester,
            ],
            $data
        );
    }

    //-----------------------------HR------------------------------------------//


    //-----------------------------Planning------------------------------------------//

    // number of status of opcr
    public function status($semester, $year)
    {
        $opcrs = OfficeOpcr::with(['officeOpcrRecordLastestRecord'])
            ->where('semester', $semester)
            ->where('year', $year)
            ->whereHas('officeOpcrRecordLastestRecord')
            ->get();

        if ($opcrs->isEmpty()) {
            return response()->json([
                'message' => 'There is no data available yet.'
            ], 200);
        }

        $counts = $opcrs->groupBy(function ($opcr) {
            return $opcr->officeOpcrRecordLastestRecord->status ?? 'Unknown';
        })->map(fn($group) => $group->count());

        return response()->json([
            'opcr_status' => array_merge(
                ['Total' => $opcrs->count()],
                $counts->toArray()
            )
        ]);
    }


    // list of  opcr pending
    public function opcrPending($semester, $year)
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
            // // nested eager load — officeHead's targetPeriod
            // 'officeHead.officeHeadTargetPeriod' => function ($query) use ($semester, $year) {
            //     $query->select(
            //         'target_periods.id',
            //         'target_periods.control_no', // FK back to employees
            //         'target_periods.semester',
            //         'target_periods.year'
            //     )->where('semester', $semester)->where('year', $year);
            // }

        ])
            ->where('semester', $semester)
            ->where('year', $year)
            ->whereHas('officeOpcrRecordLastestRecord', function ($query) {
                $query->where('status', 'Pending');
            })->get();

        return $data;
    }


    public function currentTargetPeriod($year,$semester)
    {

        $targetPeriod = TargetPeriod::where('semester', $semester)
            ->where('year', $year)
            ->first();

        // ─── OPCR ───────────────────────────────────────────────────────────────
        // Only the latest record per OfficeOpcr counts.
        // Use officeOpcrRecordLastestRecord (hasOne → latestOfMany).
        $opcrBase = OfficeOpcr::where('semester', $semester)
            ->where('year', $year)
            ->with('officeOpcrRecordLastestRecord')
            ->get();

        $opcrCounts = [
            'Pending'  => $opcrBase->filter(fn($o) => optional($o->officeOpcrRecordLastestRecord)->status === 'Pending')->count(),
            'Approved' => $opcrBase->filter(fn($o) => optional($o->officeOpcrRecordLastestRecord)->status === 'Approved')->count(),
            'Draft'    => $opcrBase->filter(fn($o) => optional($o->officeOpcrRecordLastestRecord)->status === 'Draft')->count(),
        ];

        // ─── IPCR ───────────────────────────────────────────────────────────────
        // TargetPeriod.status is the direct status field (no separate record table).
        $ipcrCounts = [
            'Pending'  => TargetPeriod::where('semester', $semester)->where('year', $year)->where('status', 'Pending')->count(),
            'Approved' => TargetPeriod::where('semester', $semester)->where('year', $year)->where('status', 'Approved')->count(),
            'Draft'    => TargetPeriod::where('semester', $semester)->where('year', $year)->where('status', 'Draft')->count(),
        ];

        // ─── Unit Work Plan ─────────────────────────────────────────────────────
        // Only the latest record per UnitWorkPlan counts.
        $uwpBase = UnitWorkPlan::where('semester', $semester)
            ->where('year', $year)
            ->with('unitworkplanLastestRecord')
            ->get();

        $uwpCounts = [
            'Pending'  => $uwpBase->filter(fn($u) => optional($u->unitworkplanLastestRecord)->status === 'Pending')->count(),
            'Approved' => $uwpBase->filter(fn($u) => optional($u->unitworkplanLastestRecord)->status === 'Approved')->count(),
            'Draft'    => $uwpBase->filter(fn($u) => optional($u->unitworkplanLastestRecord)->status === 'Draft')->count(),
        ];

        return [
            // 'targetPeriod' => $targetPeriod,
            'opcr'         => $opcrCounts,
            'ipcr'         => $ipcrCounts,
            'uwp'          => $uwpCounts,
        ];
    }

    //list of IPCR target period of spms
    public function listOfIpcr($year,$semester){

    // ipcr
        $ipcrList = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->where('semester', $semester)
            ->where('year', $year)->with('xPersonal:ControlNo,Surname,Firstname') //eager load only needed fields
            ->get()
            ->map(fn($ipcr) => [
                'id'         => $ipcr->id,
                'control_no' => $ipcr->control_no,
                'semester'   => $ipcr->semester,
                'year'       => $ipcr->year,
                'status'     => $ipcr->status,
                'name'      => optional($ipcr->xPersonal)->Firstname . ' ' . optional($ipcr->xPersonal)->Surname
            ]);

        if($ipcrList->isEmpty()){
            return $this->errorMessage('There is no data available for IPCR.', 404);
        }

        return  $this->successMessage($ipcrList, 'IPCR list fetched successfully.');
    }


    //list of UnitWorkPlan target period of spms
    public function listOfUnitWorkPlan($year,$semester,$office)

    {   

        $unitworkplan = UnitWorkPlan::select('id', 'office_name', 'semester', 'year')
            ->where('semester', $semester)
            ->where('year', $year)
            ->when($office, fn($q) => $q->where('office_name', $office)) // only filter if provided

            ->with('unitworkplanLastestRecord')
            ->get();

        if ($unitworkplan->isEmpty()) {
            return $this->errorMessage('There is no data available for unit work plans.', 404);
        }

        $data = $unitworkplan->map(function ($item) {
            return [
                'id'          => $item->id,
                'office_name' => $item->office_name,
                'semester'    => $item->semester,
                'year'        => $item->year,
                'date'        => $item->unitworkplanLastestRecord?->date,
                'status'      => $item->unitworkplanLastestRecord?->status,
                'remarks'     => $item->unitworkplanLastestRecord?->remarks,
            ];
        });

        return $this->successMessage($data, 'Unit Work Plans fetched successfully.');
    }


    //list of OPCR target period of spms
    public function listOfOpcr($year, $semester)
    {
        $opcr = OfficeOpcr::select('id', 'office_name', 'semester', 'year')
            ->where('semester', $semester)
            ->where('year', $year)
            ->with('officeOpcrRecordLastestRecord')
            ->get();

        if ($opcr->isEmpty()) {
            return $this->errorMessage('There is no data available for OPCR.', 404);
        }

        $data = $opcr->map(fn($item) => [
            'id'          => $item->id,
            'office_name' => $item->office_name,
            'semester'    => $item->semester,
            'year'        => $item->year,
            'date'        => $item->officeOpcrRecordLastestRecord?->date,
            'status'      => $item->officeOpcrRecordLastestRecord?->status,
            'remarks'     => $item->officeOpcrRecordLastestRecord?->remarks,
        ]);

        return $this->successMessage($data, 'OPCR fetched successfully.');
    }

}
