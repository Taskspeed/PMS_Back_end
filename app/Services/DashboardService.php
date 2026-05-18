<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\Month;
use App\Models\OfficeOpcr;
use App\Models\PerformanceRating;
use App\Models\PerformanceStandard;
use App\Models\TargetPeriod;
use App\Models\UnitWorkPlan;
use App\Models\vwActive;
use App\Models\vwplantillastructure;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardService
{

    use ApiResponseTrait;

    protected $structureService;

    public function __construct(StructureService $structureService)
    {
        $this->structureService = $structureService;
    }

    //-----------------------------HR------------------------------------------//
    public function dashboard($year, $semester)
    {


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
        $current_data = collect($statuses)->mapWithKeys(function ($status) use ($counts) {
            return [$status => $counts->get($status, 0)];
        });


        // filterEmployee status args $semester,$year
        $previous_data = EmployeeStatus::where('year', $year)
            ->where('semester', $semester)
            ->first();

        if (!$previous_data) {
            return response()->json([
                'message' => 'There is no data available yet.'
            ], 200); // use 200,
        }


        return response()->json([
            'current_status_of_employee' => $current_data,
            'previous_status_of_employee' =>   $previous_data,

        ]);
    }

    // end of the June and December store on the employee status table
    public function  storeEmployeeStatus()
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

    // current target period 
    public function currentTargetPeriod($year, $semester)
    {
        $user = Auth::user();

        $targetPeriod = TargetPeriod::where('semester', $semester)
            ->where('year', $year)
            ->first();
        $employee = Employee::count();

        // ─── OPCR ───────────────────────────────────────────────────────────────
        // Only the latest record per OfficeOpcr counts.
        // Use officeOpcrRecordLastestRecord (hasOne → latestOfMany).
        $opcrBase = OfficeOpcr::where('semester', $semester)
            ->where('year', $year)
            ->with('officeOpcrRecordLastestRecord')
            ->get();

        $opcrCounts = [
            'Pending'  => $opcrBase->filter(fn($o) => optional($o->officeOpcrRecordLastestRecord)->status === 'Pending')->count(),
            'Approved' => $opcrBase->filter(fn($o) => optional($o->officeOpcrRecordLastestRecord)->status === 'Approved')->count(),
            'Draft'    => $opcrBase->filter(fn($o) => optional($o->officeOpcrRecordLastestRecord)->status === 'Draft')->count(),
            'total_opcr' => $opcrBase->count(),

        ];

        // ─── IPCR ───────────────────────────────────────────────────────────────
        // TargetPeriod.status is the direct status field (no separate record table).
        $ipcrCounts = [
            'Pending'  => TargetPeriod::where('semester', $semester)->where('year', $year)->where('status', 'Pending')->count(),
            'Approved' => TargetPeriod::where('semester', $semester)->where('year', $year)->where('status', 'Approved')->count(),
            'Draft'    => TargetPeriod::where('semester', $semester)->where('year', $year)->where('status', 'Draft')->count(),
            'Reviewed'    => TargetPeriod::where('semester', $semester)->where('year', $year)->where('status', 'Reviewed')->count(),

            'total_ipcr' => TargetPeriod::where('semester', $semester)->where('year', $year)->count(),
        ];

        // ─── Unit Work Plan ─────────────────────────────────────────────────────
        // Only the latest record per UnitWorkPlan counts.
        $uwpBase = UnitWorkPlan::where('semester', $semester)
            ->where('year', $year)
            ->with('unitworkplanLastestRecord')
            ->get();

        $uwpCounts = [
            'Pending'  => $uwpBase->filter(fn($u) => optional($u->unitworkplanLastestRecord)->status === 'Pending')->count(),
            'Approved' => $uwpBase->filter(fn($u) => optional($u->unitworkplanLastestRecord)->status === 'Approved')->count(),
            'Draft'    => $uwpBase->filter(fn($u) => optional($u->unitworkplanLastestRecord)->status === 'Draft')->count(),
            'total_unitworkplan' => $uwpBase->count(),
        ];

        // ─── Plantilla Structure ─────────────────────────────────────────────────
        $plantilla = $this->plantillaStructure();  // ✅ call the method
        $structure = $plantilla['structure'];       // ✅ extract the counts



        return [
            'total_employee' => $employee,
            'structure'      => $structure,

            'opcr'         => $opcrCounts,
            'ipcr'         => $ipcrCounts,
            'uwp'          => $uwpCounts,
        ];
    }

    //list of IPCR target period of spms
    public function listOfIpcr($year, $semester)
    {

        // ipcr
        $ipcrList = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->where('semester', $semester)
            ->where('year', $year)->with('xPersonal:ControlNo,Surname,Firstname') //eager load only needed fields
            ->get()
            ->map(fn($ipcr) => [
                'id'         => $ipcr->id,
                'control_no' => $ipcr->control_no,
                'semester'   => $ipcr->semester,
                'year'       => $ipcr->year,
                'status'     => $ipcr->status,
                'name'      => optional($ipcr->xPersonal)->Firstname . ' ' . optional($ipcr->xPersonal)->Surname
            ]);

        if ($ipcrList->isEmpty()) {
            return $this->errorMessage('There is no data available for IPCR.', 404);
        }


        return  $this->successMessage($ipcrList, 'IPCR list fetched successfully.');
    }


    //list of UnitWorkPlan target period of spms
    public function listOfUnitWorkPlan($year, $semester, $office)

    {

        $unitworkplan = UnitWorkPlan::select('id', 'office_name', 'semester', 'year')
            ->where('semester', $semester)
            ->where('year', $year)
            ->when($office, fn($q) => $q->where('office_name', $office)) // only filter if provided

            ->with('unitworkplanLastestRecord')
            ->get();

        if ($unitworkplan->isEmpty()) {
            return $this->errorMessage('There is no data available for unit work plans.', 404);
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
    public function listOfOpcr($year, $semester)
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

    
    private function plantillaStructure()
    {
        $rows = vwplantillastructure::from('vwplantillaStructure as p')
            ->leftJoin('vwofficearrangement as o', 'o.Office', '=', 'p.office')
            ->select('p.*', 'p.level', 'p.ControlNo', 'p.Name4', 'o.office_sort')
            ->orderBy('o.office_sort')
            ->orderBy('p.office2')
            ->orderBy('p.group')
            ->orderBy('p.division')
            ->orderBy('p.section')
            ->orderBy('p.unit')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $result       = [];
        $officeGroups = $rows->groupBy('office');

        // ✅ Initialize counters
        $counts = [
            'office'   => 0,
            'office2'  => 0,
            'group'    => 0,
            'division' => 0,
            'section'  => 0,
            'unit'     => 0,
        ];

        foreach ($officeGroups as $officeName => $officeRows) {
            $counts['office']++; // ✅ count office

            $officeData = [
                'office'      => $officeName,
                'level'       => $officeRows->first()->level,
                'office_sort' => $officeRows->first()->office_sort,
                'employees'   => [],
                'office2'     => []
            ];

            $officeEmployees = $officeRows->filter(
                fn($r) => is_null($r->office2) && is_null($r->group) &&
                    is_null($r->division) && is_null($r->section) && is_null($r->unit)
            );

            $officeData['employees'] = $officeEmployees
                ->sortBy('ItemNo')
                ->map(fn($r) => $this->mapEmployee($r))
                ->values();

            $remainingOfficeRows = $officeRows->reject(
                fn($r) => is_null($r->office2) && is_null($r->group) &&
                    is_null($r->division) && is_null($r->section) && is_null($r->unit)
            );

            foreach ($remainingOfficeRows->groupBy('office2') as $office2Name => $office2Rows) {
                $counts['office2']++; // ✅ count office2

                $office2Data = [
                    'office2'   => $office2Name,
                    'employees' => [],
                    'groups'    => []
                ];

                $office2Employees = $office2Rows->filter(
                    fn($r) => is_null($r->group) && is_null($r->division) &&
                        is_null($r->section) && is_null($r->unit)
                );

                $office2Data['employees'] = $office2Employees
                    ->sortBy('ItemNo')
                    ->map(fn($r) => $this->mapEmployee($r))
                    ->values();

                $remainingOffice2Rows = $office2Rows->reject(
                    fn($r) => is_null($r->group) && is_null($r->division) &&
                        is_null($r->section) && is_null($r->unit)
                );

                foreach ($remainingOffice2Rows->groupBy('group') as $groupName => $groupRows) {
                    $counts['group']++; // ✅ count group

                    $groupData = [
                        'group'     => $groupName,
                        'employees' => [],
                        'divisions' => []
                    ];

                    $groupEmployees = $groupRows->filter(
                        fn($r) => is_null($r->division) && is_null($r->section) && is_null($r->unit)
                    );

                    $groupData['employees'] = $groupEmployees
                        ->sortBy('ItemNo')
                        ->map(fn($r) => $this->mapEmployee($r))
                        ->values();

                    $remainingGroupRows = $groupRows->reject(
                        fn($r) => is_null($r->division) && is_null($r->section) && is_null($r->unit)
                    );

                    foreach ($remainingGroupRows->sortBy('divordr')->groupBy('division') as $divisionName => $divisionRows) {
                        $counts['division']++; // ✅ count division

                        $divisionData = [
                            'division'  => $divisionName,
                            'employees' => [],
                            'sections'  => []
                        ];

                        $divisionEmployees = $divisionRows->filter(
                            fn($r) => is_null($r->section) && is_null($r->unit)
                        );

                        $divisionData['employees'] = $divisionEmployees
                            ->sortBy('ItemNo')
                            ->map(fn($r) => $this->mapEmployee($r))
                            ->values();

                        $remainingDivisionRows = $divisionRows->reject(
                            fn($r) => is_null($r->section) && is_null($r->unit)
                        );

                        foreach ($remainingDivisionRows->sortBy('secordr')->groupBy('section') as $sectionName => $sectionRows) {
                            $counts['section']++; // ✅ count section

                            $sectionData = [
                                'section'   => $sectionName,
                                'employees' => [],
                                'units'     => []
                            ];

                            $sectionEmployees = $sectionRows->filter(fn($r) => is_null($r->unit));

                            $sectionData['employees'] = $sectionEmployees
                                ->sortBy('ItemNo')
                                ->map(fn($r) => $this->mapEmployee($r))
                                ->values();

                            $remainingSectionRows = $sectionRows->reject(fn($r) => is_null($r->unit));

                            foreach ($remainingSectionRows->sortBy('unitordr')->groupBy('unit') as $unitName => $unitRows) {
                                $counts['unit']++; // ✅ count unit

                                $sectionData['units'][] = [
                                    'unit'      => $unitName,
                                    'employees' => $unitRows
                                        ->sortBy('ItemNo')
                                        ->map(fn($r) => $this->mapEmployee($r))
                                        ->values()
                                ];
                            }

                            $divisionData['sections'][] = $sectionData;
                        }

                        $groupData['divisions'][] = $divisionData;
                    }

                    $office2Data['groups'][] = $groupData;
                }

                $officeData['office2'][] = $office2Data;
            }

            $result[] = $officeData;
        }

        $result = collect($result)->sortBy('office_sort')->values()->all();

        // Return structure with counts + data
        return [
            'structure' => $counts,
            'data'      => $result,
        ];
    }

    private function mapEmployee($row): array
    {
        return [
            'controlNo' => $row->ControlNo, // ✅ fixed
            'name'      => $row->Name4,     // ✅ fixed
        ];
    }


    //-----------------------------HR------------------------------------------//


    //-----------------------------Planning------------------------------------------//

    // number of status of opcr
    public function status($semester, $year)
    {
        $opcrs = OfficeOpcr::with(['officeOpcrRecordLastestRecord'])
            ->where('semester', $semester)
            ->where('year', $year)
            ->whereHas('officeOpcrRecordLastestRecord')
            ->get();

        if ($opcrs->isEmpty()) {
            return response()->json([
                'message' => 'There is no data available yet.'
            ], 200);
        }

        $counts = $opcrs->groupBy(function ($opcr) {
            return $opcr->officeOpcrRecordLastestRecord->status ?? 'Unknown';
        })->map(fn($group) => $group->count());

        return response()->json([
            'opcr_status' => array_merge(
                ['Total' => $opcrs->count()],
                $counts->toArray()
            )
        ]);
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
