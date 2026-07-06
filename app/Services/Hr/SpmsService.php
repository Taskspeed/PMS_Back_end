<?php

namespace App\Services\Hr;

use App\Models\Employee;
use App\Models\OfficeOpcr;
use App\Models\TargetPeriod;
use App\Models\UnitWorkPlan;
use App\Services\StructureService;
use App\Traits\ApiResponseTrait;

class SpmsService
{
    use ApiResponseTrait;

    protected StructureService $structureService;

    public function __construct(StructureService $structureService)
    {
        $this->structureService = $structureService;
    }


    //list of IPCR target period of spms
    public function listOfIpcr(?int $year, ?string $semester,?string $office )
    {

        // ipcr
       $employee = Employee::select('ControlNo', 'name', 'rank', 'office', 'status', 'job_title', 'position')
            ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER'])
            ->whereNotIn('job_title', ['Office Head'])
            ->when($office, fn($q) => $q->where('office', $office))

            // ✅ Filter: only employees who have an Approved target period for this semester/year
            ->whereHas('targetPeriods', function ($query) use ($year, $semester) {
                $query->where('year', $year)
                    ->where('semester', $semester)
                    ->whereHas('ipcrLastestRecord', function ($q) {
                  $q->whereIn('status',['Received Target','Received Accomplishment']); 
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


        return  $this->successMessage($data, 'IPCR list fetched successfully.');
    }


    //list of UnitWorkPlan target period of spms
    public function listOfUnitWorkPlan(?int $year, ?string $semester, ?string $office)

    {

           $unitworkplan = UnitWorkPlan::select('id', 'office_name', 'semester', 'year')
            ->where('semester', $semester)
            ->where('year', $year)
            ->when($office, fn($q) => $q->where('office_name', $office))
            ->whereHas('unitworkplanLastestRecord', function ($query) {
                 $query->whereIn('status', ['Received Target','Received Accomplishment']);
            })
            ->with('unitworkplanLastestRecord')
            ->get()
            ->keyBy('office_name');

        if ($unitworkplan->isEmpty()) {
            return $this->infoMessage('There is no data available for unit work plans.', 200);
        }

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
            return $this->infoMessage('No records found.', 200);
        }


        $data = $unitworkplan->map(function ($item) {

            $structure = $this->structureService->structure($item->office_name);
            return [
                'id'          => $item->id,
                'office_name' => $item->office_name,
                'semester'    => $item->semester,
                'year'        => $item->year,
                'date'        => $item->unitworkplanLastestRecord?->date,
                'status'      => $item->unitworkplanLastestRecord?->status,
                'remarks'     => $item->unitworkplanLastestRecord?->remarks,
                'structure'     => $structure,
            ];
        });

        return $this->successMessage($data, 'Unit Work Plans fetched successfully.');
    }


    //list of OPCR target period of spms
    public function listOfOpcr(int $year, string $semester)
    {
        $opcr = OfficeOpcr::select('id', 'office_name', 'semester', 'year')
            ->where('semester', $semester)
            ->where('year', $year)
            ->with('officeOpcrRecordLastestRecord')
            ->get()
            ->keyBy('office_name');

        if ($opcr->isEmpty()) {
            return $this->errorMessage('There is no data available for OPCR.', 404);
        }

        $officeNames = $opcr->keys();

        $officeHeads = Employee::select('ControlNo', 'name', 'job_title', 'office_id', 'office')
            ->whereIn('office', $officeNames)
            ->where('job_title', 'Office Head')
            ->get()
            ->keyBy('office');

        // ✅ Fix 1: correct closure syntax — was `=>` should be `use(...) { return [...] }`
        // ✅ Fix 2: $head was undefined — get it from $officeHeads inside the closure
        // ✅ Fix 3: $opcrItem was undefined — the variable is $item
        $data = $opcr->map(function ($item) use ($officeHeads) {
            $head = $officeHeads->get($item->office_name); // ✅ resolve head per office

            return [
                'opcr_id'    => $item->id,
                'ControlNo'  => $head?->ControlNo,
                'name'       => $head?->name,
                'office'     => $item->office_name,        //was $opcrItem->office_name
                // 'office_name' => $item->office_name,
                'semester'   => $item->semester,
                'year'       => $item->year,
                'date'       => $item->officeOpcrRecordLastestRecord?->date,
                'status'     => $item->officeOpcrRecordLastestRecord?->status,
                'remarks'    => $item->officeOpcrRecordLastestRecord?->remarks,
            ];
        })->values();

        return $this->successMessage($data, 'OPCR fetched successfully.');
    }
}
