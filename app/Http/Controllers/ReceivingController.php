<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OfficeOpcr;
use App\Models\OfficeOpcrRecord;
use App\Models\Qpef;
use App\Models\TargetPeriod;
use App\Models\TargetPeriodRecord;
use App\Models\UnitWorkPlan;
use App\Models\UnitWorkPlanRecord;
use App\Services\StructureService;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivingController extends Controller
{
    //
    use ApiResponseTrait;

    protected  StructureService $structureService;

    public function __construct(StructureService $structureService)
    {
        $this->structureService = $structureService;
    }

    // get the list of ApproveIpcr
    public function getApproveIpcr(Request $request)
    {

        $user = Auth::user();

        // ✅ Actually enforce the role check
        if ($user->role_id != 6) {
            return response()->json([
                'message' => 'Unauthorized. Access restricted to authorized person only.'
            ], 403);
        }


        $year     = $request->input('year');
        $semester = $request->input('semester');
        $office   = $request->input('office');

        $employee = Employee::select('ControlNo', 'name', 'rank', 'office', 'status', 'job_title', 'position')
            ->whereNotIn('status', ['CONTRACTUAL', 'JOB ORDER'])
            ->whereNotIn('job_title', ['Office Head'])
            ->when($office, fn($q) => $q->where('office', $office))

            // ✅ Filter: only employees who have an Approved target period for this semester/year
            ->whereHas('targetPeriods', function ($query) use ($year, $semester) {
                $query->where('year', $year)
                    ->where('semester', $semester)
                    ->whereHas('ipcrLastestRecord', function ($q) {
                  $q->whereIn('status',['Approved Target','Received Target','Discussed Target']); // ✅ fix typo: was 'Aprroved'
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
            return $this->infoMessage('No records found');
        }

        return $this->successMessage($data, 'Successfully fetch');
    }

    // get the targetperiod of  office opcr,unitworkplan,office
    public function getUnitworkplan(Request $request)
    {

        $user = Auth::user();

        // Actually enforce the role check
        if ($user->role_id != 6) {
            return response()->json([
                'message' => 'Unauthorized. Access restricted to authorized person only.'
            ], 403);
        }

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
                 $query->whereIn('status', ['Draft','Received Target']);
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
        // $opcr = OfficeOpcr::select('id', 'office_name', 'semester', 'year')
        //     ->where('semester', $semester)
        //     ->where('year', $year)
        //     ->when($office, fn($q) => $q->where('office_name', $office))
        //     ->whereHas('officeOpcrRecordLastestRecord', function ($query) {
        //         $query->where('status', 'Draft');
        //     })
        //     ->with('officeOpcrRecordLastestRecord')
        //     ->get()
        //     ->keyBy('office_name');

        // if ($opcr->isEmpty()) {
        //     return $this->infoMessage('There is no data available for OPCR.', 200);
        // }

        // =====================
        // 3. GET OFFICE HEADS
        // =====================
        // $officeNames = $opcr->keys();

        // $officeHeads = Employee::select('ControlNo', 'name', 'job_title', 'office_id', 'office')
        //     ->whereIn('office', $officeNames)
        //     ->where('job_title', 'Office Head')
        //     ->get()
        //     ->keyBy('office');

        // =====================
        // 4. MERGE ALL DATA + STRUCTURE
        // =====================
        // $data = $opcr->map(function ($opcrItem) use ($unitworkplan, $officeHeads) {
        //     $uwp       = $unitworkplan->get($opcrItem->office_name);
        //     $head      = $officeHeads->get($opcrItem->office_name);
        $data = $unitworkplan->map(function ($uwp) {

            $structure = $this->structureService->structure($uwp->office_name);

            return [
                'office'              => $uwp->office_name,        // ✅ was $unitworkplan->office_name
                'semester'            => $uwp->semester,           // ✅ was $unitworkplan->semester
                'year'                => $uwp->year,               // ✅ was $unitworkplan->year
                'unitworkplan_id'     => $uwp->id,
                'unitworkplan_status' => $uwp->unitworkplanLastestRecord?->status,
                'structure'           => $structure,
            ];
        })->values();

        if ($data->isEmpty()) {
            return $this->infoMessage('No records found.', 200);
        }

        return $this->successMessage($data, 'Successfully fetched.');
    }

    // fetch Qpef
    public function getAllQpef(){

        $data = Qpef::select('id','control_no','quarterly','year','status','created_at')->where('status','Pending')->get();

        return $this->successMessage($data,'success',200);
    }

}
