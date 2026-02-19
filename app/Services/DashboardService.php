<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeStatus;
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
}
