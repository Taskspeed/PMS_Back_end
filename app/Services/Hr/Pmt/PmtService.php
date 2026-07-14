<?php

namespace App\Services\Hr\Pmt;

use App\Models\Employee;
use App\Models\OfficeOpcr;
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


    // // fetch the list of employee ipcr
    // public function EmployeeIpcr(Authenticatable $pmt_user, int $year, string $semester, ?string $office)
    // {
    //     $assignedOfficeIds = $pmt_user->pmt_assign->pluck('office_id');

    //     if ($assignedOfficeIds->isEmpty()) {
    //         return  new \Exception('No assigned offices found for this user.');
    //     }

    //     $employee = Employee::select('ControlNo', 'name', 'rank', 'office', 'status', 'job_title', 'position')
    //         ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER'])
    //         ->when($office, fn($q) => $q->where('office', $office))

    //       //Filter: only employees who have an Approved target period for this semester/year
    //         ->whereHas('targetPeriods', function ($query) use ($year, $semester) {
    //             $query->where('year', $year)
    //                 ->where('semester', $semester)
    //                 ->whereHas('ipcrLastestRecord', function ($q) {
    //                 $q->whereIn('status',['Received Target','Reviewed Target']);
    //                 });
    //         })

    //         // Eager load the matching target period with its latest record
    //         ->with(['targetPeriods' => function ($query) use ($year, $semester) {
    //             $query->select('id', 'control_no', 'year', 'semester')
    //                 ->where('year', $year)
    //                 ->where('semester', $semester)
    //                 ->with('ipcrLastestRecord'); //load latest record on the period
    //         }])
    //         ->get();

    //     if ($employee->isEmpty()) {
    //         throw new \Exception('No records found');
    //     }

    //     return  $employee->map(function ($item) {
    //         $ipcr = $item->targetPeriods->first();
    //          $latestRecord  = $ipcr?->ipcrLastestRecord;

    //         return [
    //             'ControlNo'   => $item->ControlNo,
    //             'name'        => $item->name,
    //             'rank'        => $item->rank,
    //             'office'      => $item->office,
    //             'job_title'   => $item->job_title,
    //             'position'    => $item->position,
    //             'emp_status'  => $item->status,
    //             'ipcr_id' =>    $ipcr->id, 
    //             'ipcr_status' => $latestRecord?->status, 
    //             'year'        => $ipcr?->year,
    //             'semester'    => $ipcr?->semester,
    //             'has_ipcr'    => $ipcr !== null,
    //         ];
    //     });
    // }

    public function EmployeeIpcr(Authenticatable $pmt_user, int $year, string $semester, ?string $office)
{
    $assignedOfficeIds = $pmt_user->pmt_assign->pluck('office_id');

    if ($assignedOfficeIds->isEmpty()) {
        throw new \Exception('No assigned offices found for this user.');
    }

    $employee = Employee::select('ControlNo', 'name', 'rank', 'office', 'status', 'job_title', 'position')
        ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER'])
        ->whereIn('office_id', $assignedOfficeIds) // <-- restrict to assigned offices
        ->when($office, fn($q) => $q->where('office', $office))
        ->whereHas('targetPeriods', function ($query) use ($year, $semester) {
            $query->where('year', $year)
                ->where('semester', $semester)
                ->whereHas('ipcrLastestRecord', function ($q) {
                    $q->whereIn('status', ['Received Target', 'Reviewed Target']);
                });
        })
        ->with(['targetPeriods' => function ($query) use ($year, $semester) {
            $query->select('id', 'control_no', 'year', 'semester')
                ->where('year', $year)
                ->where('semester', $semester)
                ->with('ipcrLastestRecord');
        }])
        ->get();

    if ($employee->isEmpty()) {
        throw new \Exception('No records found');
    }

    return $employee->map(function ($item) {
        $ipcr = $item->targetPeriods->first();
        $latestRecord = $ipcr?->ipcrLastestRecord;

        return [
            'ControlNo'   => $item->ControlNo,
            'name'        => $item->name,
            'rank'        => $item->rank,
            'office'      => $item->office,
            'job_title'   => $item->job_title,
            'position'    => $item->position,
            'emp_status'  => $item->status,
            'ipcr_id'     => $ipcr->id,
            'ipcr_status' => $latestRecord?->status,
            'year'        => $ipcr?->year,
            'semester'    => $ipcr?->semester,
            'has_ipcr'    => $ipcr !== null,
        ];
    });
}

    // list of the employee for pmt
    public function officeEmployeePmt(string $office)
    {
        if (!$office) {
            throw new \Exception('office is required');
        }

        $existingUsers = User::where('role_id', 5)
            ->whereNotNull('control_no')
            ->pluck('control_no')
            ->toArray();

        $data = vwActive::select('ControlNo', 'BirthDate', 'Office', 'name4', 'Designation', 'status')
            ->where('Office', $office)
            ->whereNotIn('ControlNo', $existingUsers)
            ->get();

        if ($data->isEmpty()) {
            throw new \Exception('No employees found for this office.');
        }

        return $data;
    }

    
    // list of  opcr Received
 public function opcr(string $semester, int $year, Authenticatable $pmt_user)
{
    $assignedOfficeIds = $pmt_user->pmt_assign->pluck('office_id');

if ($assignedOfficeIds->isEmpty()) {
    throw new \Exception('No assigned offices found for this user.');
}
    $data = OfficeOpcr::select(
        'office_opcrs.id',
        'office_opcrs.office_id',
        'office_opcrs.office_name',
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
        },
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
        ->whereIn('office_id', $assignedOfficeIds) // <-- this was missing
        ->where('semester', $semester)
        ->where('year', $year)
        ->whereHas('officeOpcrRecordLastestRecord', function ($query) {
            $query->whereIn('status', ['Reviewed Target', 'Approved Target', 'Reviewed Accomplishment', 'Approved Accomplishment']);
        })->get();

    return $data;
}
}
