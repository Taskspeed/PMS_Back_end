<?php

namespace App\Services\Hr\Pmt;

use App\Models\Employee;
use App\Models\User;
use App\Models\vwActive;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class PmtService
{

    public function getoffice(Authenticatable $pmt_user)
    {
        $assignedOfficeIds = $pmt_user->pmt_assign->pluck('office_id');

        $offices = DB::table('offices')
            ->select('id', 'name')
            ->whereIn('id', $assignedOfficeIds)
            ->get();

        if ($offices->isEmpty()) {
            throw new \Exception('No office record found');
        }

        return $offices;
    }


    // fetch the list of employee ipcr
    public function EmployeeIpcr(Authenticatable $pmt_user, int $year, string $semester, string $office)
    {
        $assignedOfficeIds = $pmt_user->pmt_assign->pluck('office_id');

        if ($assignedOfficeIds->isEmpty()) {
            return  new Exception('No assigned offices found for this user.');
        }

        $employee = Employee::select('ControlNo', 'name', 'rank', 'office', 'status', 'job_title', 'position')
            ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER'])
            ->when($office, fn($q) => $q->where('office', $office))

            // ✅ Only return employees WHO HAVE an approved target period
            ->whereHas('targetPeriods', function ($query) use ($year, $semester) {
                $query->where('status', 'Receive')
                    ->where('year', $year)
                    ->where('semester', $semester);
            })

            // Eager load the matching target period for display
            ->with(['targetPeriods' => function ($query) use ($year, $semester) {
                $query->select('control_no', 'year', 'semester', 'status')
                    ->where('status', 'Receive')
                    ->where('year', $year)
                    ->where('semester', $semester);
            }])
            ->whereIn('office_id', $assignedOfficeIds)
            ->get();

        if ($employee->isEmpty()) {
            throw new \Exception('No records found');
        }

        return  $employee->map(function ($item) {
            $ipcr = $item->targetPeriods->first();

            return [
                'ControlNo'   => $item->ControlNo,
                'name'        => $item->name,
                'rank'        => $item->rank,
                'office'      => $item->office,
                'job_title'   => $item->job_title,
                'position'    => $item->position,
                'emp_status'  => $item->status,
                'ipcr_status' => $ipcr?->status,
                'year'        => $ipcr?->year,
                'semester'    => $ipcr?->semester,
                'has_ipcr'    => $ipcr !== null,
            ];
        });
    }


    // list of the employee for pmt
    public function officeEmployeePmt(string $office_name)
    {
        if (!$office_name) {
            throw new \Exception('office_name is required');
        }

        $existingUsers = User::where('role_id', 5)
            ->whereNotNull('control_no')
            ->pluck('control_no')
            ->toArray();

        $data = vwActive::select('ControlNo', 'BirthDate', 'Office', 'name4', 'Designation', 'status')
            ->where('Office', $office_name)
            ->whereNotIn('ControlNo', $existingUsers)
            ->get();

        if ($data->isEmpty()) {
            throw new \Exception('No employees found for this office.');
        }

        return $data;
    }
}
