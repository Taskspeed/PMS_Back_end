<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OfficeOpcr;
use App\Models\TargetPeriod;
use App\Models\UnitWorkPlan;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    use ApiResponseTrait;

    //getting the  total employee in the office
    public function dashboardStatus(Request $request)
    {
        $user     = Auth::user();
        $officeId = $user->office_id;

        $year     = $request->input('year');
        $semester = $request->input('semester');

        // get control_nos belonging to this office
        $employeeControlNos = \App\Models\Employee::where('office_id', $officeId)->pluck('ControlNo');

        // total employee count in office
        $totalEmployees = $employeeControlNos->count();

        // ipcr status counts — filtered by office employees only ✅
        $ipcr_status = \App\Models\TargetPeriod::whereIn('control_no', $employeeControlNos)
            ->when($year,     fn($q) => $q->where('year', $year))
            ->when($semester, fn($q) => $q->where('semester', $semester))
            ->selectRaw('LOWER(status) as status, COUNT(*) as count')
            ->groupBy(DB::raw('LOWER(status)'))
            ->pluck('count', 'status');

        $ipcr_data = [
            'ipcr' => [
                'Pending'  => (int) $ipcr_status->get('pending', 0),
                'Approved' => (int) $ipcr_status->get('approved', 0),
                'Draft'    => (int) $ipcr_status->get('draft', 0),
                'Reviewed' => (int) $ipcr_status->get('reviewed', 0),
                'total_ipcr' => (int) $ipcr_status->sum(),
            ]
        ];

        // opcr get the status of opcr
        $opcr = OfficeOpcr::select('id', 'office_name', 'semester', 'year', 'office_id')
            ->where('semester', $semester)
            ->where('year', $year)
            ->where('office_id', $officeId)
            ->with('officeOpcrRecordLastestRecord')
            ->get();

        // if ($opcr->isEmpty()) {
        //     return $this->errorMessage('There is no data available for OPCR.', 404);
        // }

     $opcr_data = $opcr->isNotEmpty()
        ? $opcr->map(fn($item) => [
            'id'          => $item->id,
            'office_name' => $item->office_name,
            'semester'    => $item->semester,
            'year'        => $item->year,
            'date'        => $item->officeOpcrRecordLastestRecord?->date,
            'status'      => $item->officeOpcrRecordLastestRecord?->status,
            'remarks'     => $item->officeOpcrRecordLastestRecord?->remarks,
        ]) : 'No record found'; // ← no error, just null // ←


        // unit work plan status 
        $unitworkplan = UnitWorkPlan::select('id', 'office_name', 'semester', 'year')
            ->where('semester', $semester)
            ->where('year', $year)
             ->where('office_id', $officeId)

            ->with('unitworkplanLastestRecord')
            ->get();

        // if ($unitworkplan->isEmpty()) {
        //     return $this->errorMessage('There is no data available for unit work plans.', 404);
        // }

          $unitworkplan_data = $unitworkplan->isNotEmpty()
        ? $unitworkplan->map(fn($item) => [
            'id'          => $item->id,
            'office_name' => $item->office_name,
            'semester'    => $item->semester,
            'year'        => $item->year,
            'date'        => $item->unitworkplanLastestRecord?->date,
            'status'      => $item->unitworkplanLastestRecord?->status,
            'remarks'     => $item->unitworkplanLastestRecord?->remarks,
        ])
        : 'No record found'; // ← no error, just null



        return $this->successMessage([
            'opcr' => $opcr_data,
            'ipcr_status'    => $ipcr_data,
            'unitworkplan_status'    => $unitworkplan_data,
            'total_employee' => $totalEmployees,
        ], 'Successfully fetch');
    }


    // list of employee without ipcr  base on the year and semester
    public function listOfEmployeeNoIpcr(Request $request)
    {
        $user = Auth::user();

        $year     = $request->input('year');
        $semester = $request->input('semester');

        // Get all employees in the same office (excluding contractual/job order)
        $employees = Employee::select('ControlNo', 'name', 'position', 'office_id', 'status', 'job_title')
            ->where('office_id', $user->office_id)
            ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER', 'Contractual', 'Job Order']) // handle both casings
            ->get();

        // Get control numbers of employees WHO ALREADY HAVE a target period
        // Use the employee ControlNos scoped to this office instead of office_id on TargetPeriod
        $officeControlNos = $employees->pluck('ControlNo')->toArray();

        $withTargetPeriod = \App\Models\TargetPeriod::whereIn('control_no', $officeControlNos)
            ->where('semester', $semester)
            ->where('year', $year)
            ->pluck('control_no')
            ->toArray();

        // Filter out employees who already have a target period
        $employeesWithoutIpcr = $employees->whereNotIn('ControlNo', $withTargetPeriod)->values();

        return $this->successMessage($employeesWithoutIpcr, 'Successfully fetch');
    }
}
