<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OfficeOpcr;
use App\Models\UnitWorkPlan;
use App\Services\StructureService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivingController extends Controller
{
    //
    use ApiResponseTrait;

    protected $structureService;

    public function __construct(StructureService $structureService)
    {
        $this->structureService = $structureService;
    }

    // get the list of draftIpcr
    public function getDraftIpcr(Request $request)
    {

        $year     = $request->input('year');
        $semester = $request->input('semester');
        $office   = $request->input('office');


        $employee = Employee::select('ControlNo', 'name', 'rank', 'office', 'status', 'job_title', 'position')
            ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER'])
            ->when($office, fn($q) => $q->where('office', $office))

            // ✅ Only return employees WHO HAVE an approved target period
            ->whereHas('targetPeriods', function ($query) use ($year, $semester) {
                $query->where('status', 'Draft')
                    ->where('year', $year)
                    ->where('semester', $semester);
            })

            // Eager load the matching target period for display
            ->with(['targetPeriods' => function ($query) use ($year, $semester) {
                $query->select('control_no', 'year', 'semester', 'status')
                    ->where('status', 'Draft')
                    ->where('year', $year)
                    ->where('semester', $semester);
            }])
            // ->whereIn('office_id', $assignedOfficeIds)
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

    // get the targetperiod of  office opcr,unitworkplan,office
public function getTargetPeriod(Request $request)
{
    $year     = $request->input('year');
    $semester = $request->input('semester');
    $office   = $request->input('office');

    // =====================
    // 1. GET UNIT WORK PLAN
    // =====================
    $unitworkplan = UnitWorkPlan::select('id', 'office_name', 'semester', 'year')
        ->where('semester', $semester)
        ->where('year', $year)
        ->when($office, fn($q) => $q->where('office_name', $office))
        ->whereHas('unitworkplanLastestRecord', function ($query) {
            $query->where('status', 'Draft');
        })
        ->with('unitworkplanLastestRecord')
        ->get()
        ->keyBy('office_name');

    if ($unitworkplan->isEmpty()) {
        return $this->infoMessage('There is no data available for unit work plans.', 200);
    }

    // =====================
    // 2. GET OPCR
    // =====================
    $opcr = OfficeOpcr::select('id', 'office_name', 'semester', 'year')
        ->where('semester', $semester)
        ->where('year', $year)
        ->when($office, fn($q) => $q->where('office_name', $office))
        ->whereHas('officeOpcrRecordLastestRecord', function ($query) {
            $query->where('status', 'Draft');
        })
        ->with('officeOpcrRecordLastestRecord')
        ->get()
        ->keyBy('office_name');

    if ($opcr->isEmpty()) {
        return $this->infoMessage('There is no data available for OPCR.', 200);
    }

    // =====================
    // 3. GET OFFICE HEADS
    // =====================
    $officeNames = $opcr->keys();

    $officeHeads = Employee::select('ControlNo', 'name', 'job_title', 'office_id', 'office')
        ->whereIn('office', $officeNames)
        ->where('job_title', 'Office Head')
        ->get()
        ->keyBy('office');

    // =====================
    // 4. MERGE ALL DATA + STRUCTURE
    // =====================
    $data = $opcr->map(function ($opcrItem) use ($unitworkplan, $officeHeads) {
        $uwp       = $unitworkplan->get($opcrItem->office_name);
        $head      = $officeHeads->get($opcrItem->office_name);

        // ✅ Call structure() with just the office_name string
        $structure = $this->structureService->structure($opcrItem->office_name);



        return [
            'ControlNo'           => $head?->ControlNo,
            'name'                => $head?->name,
            'office'              => $opcrItem->office_name,
            'semester'            => $opcrItem->semester,
            'year'                => $opcrItem->year,
            'opcr_id'             => $opcrItem->id,
            'opcr_status'         => $opcrItem->officeOpcrRecordLastestRecord?->status,
            'unitworkplan_id'     => $uwp?->id,
            'unitworkplan_status' => $uwp?->unitworkplanLastestRecord?->status,
            'structure'           => $structure, // ✅ merged here
        ];
    })->values();

    if ($data->isEmpty()) {
        return $this->infoMessage('No records found.', 200);
    }

    return $this->successMessage($data, 'Successfully fetched.');
}

}
