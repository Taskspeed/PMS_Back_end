<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PmtController extends Controller
{

    use ApiResponseTrait;

    // fetch the  list of office assign on the user pmt 
    public function office()
    {
        $pmt_user = Auth::user();

        // Get office IDs assigned to this PMT user
        $assignedOfficeIds = $pmt_user->pmt_assign->pluck('office_id');

        // Fetch only offices assigned to this user
        $offices = DB::table('offices')
            ->select('id', 'name')
            ->whereIn('id', $assignedOfficeIds)
            ->get();
        if ($offices->isEmpty()) {
            return $this->infoMessage('No office record found');
        }

        return $this->successMessage($offices, 'Successfully fetch');
    }

    // fetch the list of employee ipcr
    public function listOfEmployeeIpcr(Request $request)
    {
        $year     = $request->input('year');
        $semester = $request->input('semester');
        $office   = $request->input('office');
        $pmt_user = Auth::user();

        $assignedOfficeIds = $pmt_user->pmt_assign->pluck('office_id');

        if ($assignedOfficeIds->isEmpty()) {
            return $this->infoMessage('No assigned offices found for this user.');
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

        $data = $employee->map(function ($item) {
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

        if ($data->isEmpty()) {
            return $this->infoMessage('No records found');
        }

        return $this->successMessage($data, 'Successfully fetch');
    }

    // list of the employee for pmt
    public function getOfficeEmployeePmt(Request $request)
    {
        $office_name = $request->query('office_name');

        if (!$office_name) {
            return $this->errorMessage('office_name is required', 422);
        }

        // Get control_nos that already have role_id 5
        $existingUsers = User::where('role_id', 5)
            ->whereNotNull('control_no')
            ->pluck('control_no')
            ->toArray();

        $data = DB::table('vwActive')
            ->select(
                'ControlNo',
                'BirthDate',
                'Office',
                'name4',
                'Designation',
                'status'
            )
            ->where('Office', $office_name)
            ->whereNotIn('ControlNo', $existingUsers) // exclude employees already with role_id 5
            ->get();

        if ($data->isEmpty()) {
            return $this->infoMessage('No employees found for this office.', 200);
        }

        return response()->json($data);
    }
}
