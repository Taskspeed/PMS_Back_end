<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\Qpef;
use App\Models\UnitWorkPlan;
use App\Services\StructureService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceivingService
{
    use ApiResponseTrait;

    protected  StructureService $structureService;

    public function __construct(StructureService $structureService)
    {
        $this->structureService = $structureService;
    }

    // get the list of ApproveIpcr
    public function ipcr(?int $year, ?string $semester, ?string $office)
    {

        $employee = Employee::select('ControlNo', 'name', 'rank', 'office', 'status', 'job_title', 'position')
            ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER'])
            ->whereNotIn('job_title', ['Office Head'])
            ->when($office, fn($q) => $q->where('office', $office))

            // ✅ Filter: only employees who have an Approved target period for this semester/year
            ->whereHas('targetPeriods', function ($query) use ($year, $semester) {
                $query->where('year', $year)
                    ->where('semester', $semester)
                    ->whereHas('ipcrLastestRecord', function ($q) {
                  $q->whereIn('status',['Approved Target','Received Target','Discussed Target']);
                    });
            })

            // ✅ Eager load the matching target period with its latest record
            ->with(['targetPeriods' => function ($query) use ($year, $semester) {
                $query->select('id', 'control_no', 'year', 'semester')
                    ->where('year', $year)
                    ->where('semester', $semester)
                    ->with('ipcrLastestRecord'); // ✅ load latest record on the period
            }])
            ->get();

        $data = $employee->map(function ($item) {
            $ipcr          = $item->targetPeriods->first();
            $latestRecord  = $ipcr?->ipcrLastestRecord;

            return [
                'ipcr_id'            => $ipcr?->id,
                'ControlNo'          => $item->ControlNo,
                'name'               => $item->name,
                'rank'               => $item->rank,
                'office'             => $item->office,
                'job_title'          => $item->job_title,
                'position'           => $item->position,
                'emp_status'         => $item->status,

                'semester'           => $ipcr?->semester,
                'ipcr_status'        => $latestRecord?->status,       // ✅ from latest record
                'processed_by_name'  => $latestRecord?->processed_by_name,
                'date'               => $latestRecord?->date,
                'year'               => $ipcr?->year,
                'has_ipcr'           => $ipcr !== null,
            ];
        });

        if ($data->isEmpty()) {
         return $this->infoMessage('There is no data available for IPCR.');
        }

        return $this->successMessage($data, 'Successfully fetch');
    }

    // get the targetperiod of  office opcr,unitworkplan,office
    public function unitworkplan(?int $year, ?string $semester, ?string $office)
    {

        // =====================
        // 1. GET UNIT WORK PLAN
        // =====================
        $unitworkplan = UnitWorkPlan::select('id', 'office_name', 'semester', 'year')
            ->where('semester', $semester)
            ->where('year', $year)
            ->when($office, fn($q) => $q->where('office_name', $office))
            ->whereHas('unitworkplanLastestRecord', function ($query) {
                 $query->whereIn('status', ['Draft','Received Target']);
            })
            ->with('unitworkplanLastestRecord')
            ->get()
            ->keyBy('office_name');

        $data = $unitworkplan->map(function ($uwp) {

            $structure = $this->structureService->structure($uwp->office_name);

            return [
                'office'              => $uwp->office_name,      
                'semester'            => $uwp->semester,          
                'year'                => $uwp->year,               
                'unitworkplan_id'     => $uwp->id,
                'unitworkplan_status' => $uwp->unitworkplanLastestRecord?->status,
                'structure'           => $structure,
            ];
        })->values();

        if ($data->isEmpty()) {
            return $this->infoMessage('There is no data available for unit work plans');
        }

        return $this->successMessage($data, 'Successfully fetched.');
    }


     // fetch Qpef
    public function qpef()
    {
        $data = Qpef::select('id', 'control_no', 'quarterly', 'year', 'status', 'created_at')
            ->where('status', 'Pending')
            ->get();

        // Batch-fetch employees for all control numbers in one query
        $controlNos = $data->pluck('control_no')->unique()->values();

        $employees = Employee::select('ControlNo', 'name', 'office')
            ->whereIn('ControlNo', $controlNos)
            ->get()
            ->keyBy('ControlNo');

        $result = $data->map(function ($qpef) use ($employees) {
            $credential = $employees->get($qpef->control_no);

            return [
                'id'          => $qpef->id,
                'name'        => $credential->name ?? null,
                'control_no'  => $qpef->control_no,
                'quarterly'   => $qpef->quarterly,
                'year'        => $qpef->year,
                'status'      => $qpef->status,
                'office'      => $credential->office ?? null,
            ];
        });

        return $this->successMessage($result, 'success', 200);
    }

}
