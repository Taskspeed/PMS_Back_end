<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\OfficeOpcr;
use App\Models\TargetPeriod;
use App\Models\vwActive;
use Carbon\Carbon;


class DashboardService
{


    // getting the current employee
    public function currentEmployee()
    {
        $statuses = [
            'ELECTIVE',
            'APPOINTED',
            'CO-TERMINOUS',
            'TEMPORARY',
            'REGULAR',
            'CASUAL',
            'CONTRACTUAL',
            'HONORARIUM'
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
        $result = collect($statuses)->mapWithKeys(function ($status) use ($counts) {
            return [$status => $counts->get($status, 0)];
        });

        return $result;
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

    // get the employee data base on the args year and semester
    public function filterEmployeeStatus($year, $semester)
    {

        $data = EmployeeStatus::where('year', $year)
            ->where('semester', $semester)
            ->first();

        if (!$data) {
            return response()->json([
                'message' => 'There is no data available yet.'
            ], 200); // use 200,
        }

        return $data;
    }

    // fetching the available data of employee status
    public function availableDataEmployeeStatus()
    {

        $data = EmployeeStatus::select('id', 'year', 'semester')
            ->orderByDesc('year')
            ->orderByDesc('semester')->get();

        return $data;
    }




    //-----------------------------HR------------------------------------------//






















    //-----------------------------HR------------------------------------------//


    //-----------------------------Planning------------------------------------------//

    // number of status of opcr
    public function status($semester, $year)
    {

        $status = OfficeOpcr::with(['officeOpcrRecordLastestRecord'])->where('semester', $semester)->where('year', $year)->count();

        if (!$status) {
            return response()->json([
                'message' => 'There is no data available yet.'
            ], 200); // use 200,
        }


        return response()->json($status);
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


}
