<?php


namespace App\Http\Controllers\office;

use App\Http\Requests\addEmployeeUnitWorkPlanRequest;
use App\Http\Requests\updateEmployeeUnitWorkPlanRequest;
use App\Http\Resources\UnitWorkPlanOrganizationResource;
use App\Models\Employee;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use App\Models\StandardOutcome;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceStandard;
use App\Models\PerformanceConfigurations;
use App\Services\UnitWorkPlanService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use PHPUnit\Framework\MockObject\ReturnValueNotConfiguredException;

class UnitWorkPlanController extends BaseController
{


    protected $user;
    protected $officeId;
    protected $unitWorkPlanService;

    public function __construct(UnitWorkPlanService $unitWorkPlanService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->officeId = $this->user->office_id;

            return $next($request);
        });

        $this->unitWorkPlanService = $unitWorkPlanService;
    }




    // storing unit work plan
    public function addUnitWorkPlan(addEmployeeUnitWorkPlanRequest $request)
    {
        $validated = $request->validated();

        try {
            $unitworkplan = $this->unitWorkPlanService->store($validated);

            return response()->json([
                'success' => true,
                'message' => 'Unit Work Plans for all employees created successfully.',
                'target_period' => $unitworkplan['target_period'],
                'performance_standard' => $unitworkplan['performance_standard'],
                'standard_outcome' => $unitworkplan['standard_outcome'],
                'configuration' => $unitworkplan['configuration'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Unit Work Plan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // updating unit work plan

    // args controlno, semester, year
    public function updateUnitWorkPlan(updateEmployeeUnitWorkPlanRequest $request, $controlNo, $semester, $year, UnitWorkPlanService $unitworkplanService){

        $validated = $request->validated();

        $unitworkplan = $this->unitWorkPlanService->update($validated, $controlNo, $semester, $year);

        return response()->json([
            'success' => true,
            'message' => 'Unit Work Plan updated successfully.',
            'data' => $unitworkplan
        ]);

    }


    // find employee
    public function findEmployee(Request $request, $controlNo)
    {
        $employee = Employee::where('ControlNo', $controlNo)->with(['targetPeriods'])->first();


        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }


    // view the unitworkplant of the employee based on controlno , semester and year
    public function getUnitworkplan($controlNo, $semester, $year)
    {
        $employee = Employee::where('ControlNo', $controlNo)
            ->where('office_id', $this->officeId)
            ->with([
                'targetPeriods' => function ($q) use ($year, $semester) {
                    $q->select('id', 'control_no', 'year', 'semester', 'status')
                        ->where('year', $year)
                        ->where('semester', $semester)
                        ->with([
                            'performanceStandards.configurations',
                            'performanceStandards.standardOutcomes:id,performance_standard_id,rating,quantity_target as quantity,effectiveness_criteria as effectiveness,timeliness_range as timeliness'
                        ]);
                }
            ])
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // Transform the response
        $employee = $employee->toArray();
        foreach ($employee['target_periods'] as &$period) {

            foreach ($period['performance_standards'] as &$ps) {

                // Rename standard_outcomes → ratings
                if (isset($ps['standard_outcomes'])) {
                    $ps['ratings'] = $ps['standard_outcomes'];
                    unset($ps['standard_outcomes']);
                }

                // Attach config PER performance standard
                if (!empty($ps['configurations'])) {
                    $config = $ps['configurations'][0]; // usually 1 per PS

                    $ps['config'] = [
                        'targetOutput' => $config['target_output'],
                        'quantityIndicator' => $config['quantity_indicator'],
                        'timelinessIndicator' => $config['timeliness_indicator'],
                        'timelinessType' => [
                            'range' => (bool) $config['timeliness_range'],
                            'date' => (bool) $config['timeliness_date'],
                            'description' => (bool) $config['timeliness_description'],
                        ],
                    ];
                } else {
                    $ps['config'] = null;
                }

                // remove raw configurations
                unset($ps['configurations']);
            }
        }

        unset($period, $ps);


        return response()->json($employee);
    }


    // delete the unit work plan of the employee based on semester and year
    public function deleteUnitWorkPlan($controlNo, $semester, $year)
    {
        // ✅ STEP 1: Find employee with office restriction
        $employee = Employee::where('ControlNo', $controlNo)
            ->where('office_id', $this->officeId)
            ->firstOrFail();

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found or access denied'
            ], 404);
        }

        // ✅ STEP 2: Find target period (Unit Work Plan)
        $targetPeriod = $employee->targetPeriods()
            ->where('year', $year)
            ->where('semester', $semester)
            ->first();

        if (!$targetPeriod) {
            return response()->json([
                'message' => 'Unit Work Plan not found'
            ], 404);
        }

        // ✅ STEP 3: Delete target period
        $targetPeriod->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit Work Plan deleted successfully',
            'data' => $targetPeriod,
        ]);
    }



    //     public function plantilla(Request $request)
    // {

    //             $request->validate([
    //             'officeId' => 'required|integer|exists:offices,id',
    //             'office_name' => 'required',
    //             'organization' => 'required',
    //               'year' => 'required',
    //               'semester' => 'required'
    //             ]);


    //         $rows = DB::table('employees as e')
    //             ->leftJoin('vwActive as a', 'a.ControlNo', '=', 'e.ControlNo')
    //             ->leftJoin('offices as o', 'o.Office', '=', 'e.office') // or actual related column

    //             // ->leftJoin('offices as o', 'o.name', '=', 'e.office')

    //             ->select(
    //                 'e.*',
    //                 'a.Birthdate as birthdate',
    //                 'a.Surname as lastname',
    //                 'a.Firstname as firstname',
    //                 'a.MIddlename as middlename',
    //                 'e.rank',
    //                 'e.ControlNo as controlNo',
    //                 'o.id as office_id'   // ✅ FETCH OFFICE ID


    //             )
    //             ->where('o.id', $request->officeId) // Filter here
    //             ->orderBy('e.office2')
    //             ->orderBy('e.group')
    //             ->orderBy('e.division')
    //             ->orderBy('e.section')
    //             ->orderBy('e.unit')
    //             // ->orderBy('p.itemNo')
    //             ->get();


    //         if ($rows->isEmpty()) {
    //             return response()->json([]);
    //         }


    //         $result = [];

    //         foreach ($rows->groupBy('office') as $officeName => $officeRows) {

    //             // $officeLevel = $officeRows->first()->level;
    //             $officeId = $officeRows->first()->office_id; // ✅ HERE
    //             $officeData = [
    //                 'office_id' => (int) $officeId,   // ✅ Correct integer cast
    //                 'office_name'      => $officeName,
    //                 // 'level'       => $officeLevel,
    //                 'employees'   => [],
    //                 'office2'     => []
    //             ];

    //             $officeEmployees = $officeRows->filter(
    //                 fn($r) =>
    //                 is_null($r->office2) &&
    //                     is_null($r->group) &&
    //                     is_null($r->division) &&
    //                     is_null($r->section) &&
    //                     is_null($r->unit)
    //             );
    //             $officeData['employees'] = $officeEmployees
    //                 ->sortBy('ItemNo')
    //                 // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                 ->map(fn($r) => $this->mapEmployee($r))

    //                 ->values();

    //             $remainingOfficeRows = $officeRows->reject(
    //                 fn($r) =>
    //                 is_null($r->office2) &&
    //                     is_null($r->group) &&
    //                     is_null($r->division) &&
    //                     is_null($r->section) &&
    //                     is_null($r->unit)
    //             );

    //             foreach ($remainingOfficeRows->groupBy('office2') as $office2Name => $office2Rows) {
    //                 $office2Data = [
    //                     'office2'   => $office2Name,
    //                     'employees' => [],
    //                     'groups'    => []
    //                 ];

    //                 $office2Employees = $office2Rows->filter(
    //                     fn($r) =>
    //                     is_null($r->group) &&
    //                         is_null($r->division) &&
    //                         is_null($r->section) &&
    //                         is_null($r->unit)
    //                 );
    //                 $office2Data['employees'] = $office2Employees
    //                     ->sortBy('ItemNo')
    //                     // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                     ->map(fn($r) => $this->mapEmployee($r))

    //                     ->values();

    //                 $remainingOffice2Rows = $office2Rows->reject(
    //                     fn($r) =>
    //                     is_null($r->group) &&
    //                         is_null($r->division) &&
    //                         is_null($r->section) &&
    //                         is_null($r->unit)
    //                 );

    //                 foreach ($remainingOffice2Rows->groupBy('group') as $groupName => $groupRows) {
    //                     $groupData = [
    //                         'group'     => $groupName,
    //                         'employees' => [],
    //                         'divisions' => []
    //                     ];

    //                     $groupEmployees = $groupRows->filter(
    //                         fn($r) =>
    //                         is_null($r->division) &&
    //                             is_null($r->section) &&
    //                             is_null($r->unit)
    //                     );
    //                     $groupData['employees'] = $groupEmployees
    //                         ->sortBy('ItemNo')
    //                         // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                         ->map(fn($r) => $this->mapEmployee($r))

    //                         ->values();

    //                     $remainingGroupRows = $groupRows->reject(
    //                         fn($r) =>
    //                         is_null($r->division) &&
    //                             is_null($r->section) &&
    //                             is_null($r->unit)
    //                     );

    //                     // ----- SORT HERE by divordr -----
    //                     foreach ($remainingGroupRows->sortBy('divordr')->groupBy('division') as $divisionName => $divisionRows) {
    //                         $divisionData = [
    //                             'division'  => $divisionName,
    //                             'employees' => [],
    //                             'sections'  => []
    //                         ];

    //                         $divisionEmployees = $divisionRows->filter(
    //                             fn($r) =>
    //                             is_null($r->section) &&
    //                                 is_null($r->unit)
    //                         );
    //                         $divisionData['employees'] = $divisionEmployees
    //                             ->sortBy('ItemNo')
    //                             // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                             ->map(fn($r) => $this->mapEmployee($r))

    //                             ->values();

    //                         $remainingDivisionRows = $divisionRows->reject(
    //                             fn($r) =>
    //                             is_null($r->section) &&
    //                                 is_null($r->unit)
    //                         );

    //                         // ----- SORT HERE by secordr -----
    //                         foreach ($remainingDivisionRows->sortBy('secordr')->groupBy('section') as $sectionName => $sectionRows) {
    //                             $sectionData = [
    //                                 'section'   => $sectionName,
    //                                 'employees' => [],
    //                                 'units'     => []
    //                             ];

    //                             $sectionEmployees = $sectionRows->filter(
    //                                 fn($r) =>
    //                                 is_null($r->unit)
    //                             );
    //                             $sectionData['employees'] = $sectionEmployees
    //                                 ->sortBy('ItemNo')
    //                                 // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                                 ->map(fn($r) => $this->mapEmployee($r))

    //                                 ->values();

    //                             $remainingSectionRows = $sectionRows->reject(
    //                                 fn($r) =>
    //                                 is_null($r->unit)
    //                             );

    //                             // ----- SORT HERE by unitordr -----
    //                             foreach ($remainingSectionRows->sortBy('unitordr')->groupBy('unit') as $unitName => $unitRows) {
    //                                 $sectionData['units'][] = [
    //                                     'unit'      => $unitName,
    //                                     'employees' => $unitRows
    //                                         ->sortBy('ItemNo')
    //                                         // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                                         ->map(fn($r) => $this->mapEmployee($r))

    //                                         ->values()
    //                                 ];
    //                             }

    //                             $divisionData['sections'][] = $sectionData;
    //                         }

    //                         $groupData['divisions'][] = $divisionData;
    //                     }

    //                     $office2Data['groups'][] = $groupData;
    //                 }

    //                 $officeData['office2'][] = $office2Data;
    //             }

    //             $result[] = $officeData;
    //         }



    //         return response()->json($result);
    //     }

    //     private function mapEmployee($row)
    //     {
    //         return [
    //             'controlNo'   => $row->controlNo, // ✅ FIX
    //             'lastname'    => $row->lastname,
    //             'firstname'   => $row->firstname,
    //             'middlename'  => $row->middlename,
    //             'rank'   => $row->rank, // ✅ FIX

    //         ];
    //     }



    // fetch unit work plan of the division base on the division and other organization
    // public function getUniWorkPlanOfficeOrganization(Request $request) // original
    // {
    //     $request->validate([
    //         'office_name' => 'required|string',
    //         'organization' => 'required|string',
    //         'semester' => 'required',
    //         'year' => 'required',
    //     ]);

    //     /**
    //      * =====================================
    //      * 0️⃣ VALIDATE ORGANIZATION BELONGS TO OFFICE
    //      * =====================================
    //      */
    //     $orgExistsInOffice = DB::table('employees')
    //         ->where('office', $request->office_name)
    //         ->where(function ($q) use ($request) {
    //             $q->where('office2', $request->organization)
    //                 ->orWhere('group', $request->organization)
    //                 ->orWhere('division', $request->organization)
    //                 ->orWhere('section', $request->organization)
    //                 ->orWhere('unit', $request->organization);
    //         })
    //         ->exists();

    //     if (!$orgExistsInOffice) {
    //         return response()->json([
    //             // 'message' => 'Invalid organization. The organization does not belong to the selected office.'
    //             'message' => 'There are no employees assigned to the selected organization in this office.'

    //         ], 422);
    //     }

    //     /**
    //      * ===============================
    //      * 1️⃣ OFFICE HEAD
    //      * ===============================
    //      */
    //     $officeEmployee = DB::table('employees')
    //         ->where('office', $request->office_name)
    //         ->whereNull('division')
    //         ->whereNull('section')
    //         ->whereNull('unit')
    //         ->select('ControlNo', 'name', 'rank', 'position', 'sg', 'level')
    //         ->first();

    //     if (!$officeEmployee) {
    //         return response()->json([
    //             'message' => 'Office head not found.'
    //         ], 404);
    //     }

    //     /**
    //      * ===============================
    //      * 2️⃣ ORGANIZATION EMPLOYEES
    //      * ===============================
    //      */
    //     $employees = DB::table('employees')
    //         ->where('office', $request->office_name)
    //         ->where(function ($q) use ($request) {
    //             $q->where('office2', $request->organization)
    //                 ->orWhere('group', $request->organization)
    //                 ->orWhere('division', $request->organization)
    //                 ->orWhere('section', $request->organization)
    //                 ->orWhere('unit', $request->organization);
    //         })
    //         ->select('ControlNo', 'name', 'rank', 'position', 'sg', 'level')
    //         ->get();

    //     $controlNos = $employees->pluck('ControlNo');

    //     $organizationTargetPeriods = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->with([
    //         'employee:ControlNo,name,rank,position,sg,level',
    //         'performanceStandards.standardOutcomes' => function ($query) {
    //             $query->select(
    //                 'id',
    //                 'performance_standard_id',
    //                 'rating',
    //                 'quantity_target',
    //                 'effectiveness_criteria',
    //                 'timeliness_range'
    //             );
    //         },
    //     ])
    //         ->whereIn('control_no', $controlNos)
    //         ->where('semester', $request->semester)
    //         ->where('year', $request->year)
    //         ->get();

    //     /**
    //      * ===============================
    //      * 3️⃣ GET ORGANIZATION MFOs
    //      * ===============================
    //      */
    //     // Extract unique MFOs from organization employees
    //     $organizationMFOs = $organizationTargetPeriods
    //         ->pluck('performanceStandards')
    //         ->flatten()
    //         ->pluck('mfo')
    //         ->unique()
    //         ->values()
    //         ->toArray();

    //     /**
    //      * ===============================
    //      * 4️⃣ FETCH OFFICE HEAD TARGET PERIOD WITH FILTERED MFOs
    //      * ===============================
    //      */
    //     $officeTargetPeriod = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->with([
    //         'employee:ControlNo,name,rank,position', // this maps via control_no
    //         'performanceStandards' => function ($query) use ($organizationMFOs) {
    //             $query->select('id', 'target_period_id', 'mfo', 'output', 'core as core_competencies', 'technical as technical_competencies', 'leadership as leadership_competencies', 'required_output', 'success_indicator')->whereIn('mfo', $organizationMFOs);
    //         },
    //         'performanceStandards.standardOutcomes' => function ($query) {
    //             $query->select(
    //                 'id',
    //                 'performance_standard_id',
    //                 'rating',
    //                 'quantity_target',
    //                 'effectiveness_criteria',
    //                 'timeliness_range'
    //             );
    //         },
    //     ])
    //         ->where('control_no', $officeEmployee->ControlNo)
    //         ->where('semester', $request->semester)
    //         ->where('year', $request->year)
    //         ->first();


    //     /**
    //      * ===============================
    //      * FINAL RESPONSE
    //      * ===============================
    //      */
    //     return response()->json([
    //         'office' => [
    //             'name' => $request->office_name,
    //             'employee' => [
    //                 'ControlNo' => $officeEmployee->ControlNo,
    //                 'name'      => $officeEmployee->name,
    //                 'rank'      => $officeEmployee->rank,
    //                 'position'  => $officeEmployee->position,
    //                 'sg'  => $officeEmployee->sg,
    //                 'level'  => $officeEmployee->level,
    //             ],
    //             'target_periods' => $officeTargetPeriod
    //                 ? collect([$officeTargetPeriod])->map(function ($tp) {
    //                     return [
    //                         'id' => $tp->id,
    //                         'control_no' => $tp->control_no,
    //                         'semester' => $tp->semester,
    //                         'year' => $tp->year,
    //                         'status' => $tp->status,
    //                         'performance_standards' => $tp->performanceStandards,
    //                     ];
    //                 })
    //                 : []
    //         ],

    //         'organization' => [
    //             'name' => $request->organization,
    //             'employees' => $organizationTargetPeriods
    //                 ->groupBy('control_no')
    //                 ->map(function ($periods) {
    //                     $employee = $periods->first()->employee;

    //                     return [
    //                         'employee' => [
    //                             'ControlNo' => $employee->ControlNo,
    //                             'name' => $employee->name,
    //                             'rank' => $employee->rank,
    //                             'position' => $employee->position,
    //                             'sg'  => $employee->sg,
    //                             'level'  => $employee->level,
    //                         ],
    //                         'target_periods' => $periods->map(function ($tp) {
    //                             return [
    //                                 'id' => $tp->id,
    //                                 'control_no' => $tp->control_no,
    //                                 'semester' => $tp->semester,
    //                                 'year' => $tp->year,
    //                                 'status' => $tp->status,
    //                                 'performance_standards' => $tp->performanceStandards,
    //                             ];
    //                         })->values()
    //                     ];
    //                 })->values()
    //         ]
    //     ]);
    // }


    /// need to finish the response
    public function getUniWorkPlanOfficeOrganization(Request $request, UnitWorkPlanService $unitWorkPlanService)
    {

        $request->validate([
            'office_name' => 'required|string',
            'organization' => 'required|string',
            'semester' => 'required',
            'year' => 'required',
        ]);

        $organization = $this->unitWorkPlanService->organization($request);

        return new UnitWorkPlanOrganizationResource($organization);
    }



    //    /// need to finish the response
    // public function managerialSuccessIndicator(Request $request, UnitWorkPlanService $unitWorkPlanService)
    // {

    //   $validated  =   $request->validate([
    //         'year' => 'required'
    //         'semester' => 'required',
    //     ]);

    //     $result = $unitWorkPlanService->findManagerial($validated);

    //     return ($organization);
    // }

    // find  managerial on the office base on the  year and semester to get the data
    // and supervisory
    // public function findManagerial($year, $semester,$mfo)
    // {


    //     $user = Auth::user();

    //     // Get the managerial (office head) of this office
    //     $managerial = Employee::where('rank', 'Managerial')
    //         ->where('office_id', $user->office_id)
    //         ->first();

    //     if (!$managerial) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No managerial employee found.'
    //         ], 404);
    //     }




    //     // Get the managerial's target period with performance standards
    //     $targetPeriod = TargetPeriod::with('performanceStandards')
    //         ->where('control_no', $managerial->ControlNo)
    //         ->where('year', $year)
    //         ->where('semester', $semester)
    //         ->first();

    //     if (!$targetPeriod) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No target period found for this managerial.'
    //         ], 404);
    //     }

    //     // Get all employees under the same office (excluding managerial)
    //     $employeeControlNos = Employee::where('office_id', $user->office_id)
    //         ->where('rank', '!=', 'Managerial')
    //         ->pluck('ControlNo');


    //     // $employeeControlNos = Employee::where('office_id', $user->office_id)
    //     //     ->where('rank', '!=', 'Supervisory')
    //     //     ->pluck('ControlNo');

    //     // Get all employee target periods for same year and semester
    //     $employeeTargetPeriods = TargetPeriod::with('performanceStandards')
    //         ->whereIn('control_no', $employeeControlNos)
    //         ->where('year', $year)
    //         ->where('semester', $semester)
    //         ->get();


    //     $standards = $mfo
    //         ? $targetPeriod->performanceStandards->where('mfo', $mfo)
    //         : $targetPeriod->performanceStandards;


    //     // Build result: for each managerial MFO/success_indicator, compute available
    //     // $result = $targetPeriod->performanceStandards->map(function ($standard) use ($employeeTargetPeriods) {
    //     $result = $standards->map(function ($standard) use ($employeeTargetPeriods) {
    //         // Extract the target number from managerial's success_indicator
    //         $totalTarget = $this->extractNumber($standard->success_indicator);

    //         // Sum up all employees' claimed output for the same MFO + success_indicator

    //         $claimed = 0;

    //         foreach ($employeeTargetPeriods as $empPeriod) {
    //             foreach ($empPeriod->performanceStandards as $empStandard) {
    //                 // Match by MFO only
    //                 if ($empStandard->mfo === $standard->mfo) {
    //                     $claimed += $this->extractNumber($empStandard->success_indicator);
    //                 }
    //             }
    //         }

    //         $available = $totalTarget - $claimed;

    //         return [
    //             'category'           => $standard->category,
    //             'mfo'              => $standard->mfo,
    //             'output' => $standard->output,
    //             'output_name' => $standard->output_name,
    //             'performance_indicator' => $standard->performance_indicator,
    //             'performance_indicator' => $standard->performance_indicator,
    //             'success_indicator' => $standard->success_indicator,
    //             'total_target'      => $totalTarget,
    //             'claimed'           => $claimed,
    //             'available'         => max(0, $available), // never go negative
    //         ];
    //     });

    //     return response()->json([
    //         // 'success' => true,
    //         'controlNo' => $managerial->ControlNo,
    //         'name' => $managerial->name,
    //         'rank' => $managerial->rank,
    //         'office' => $managerial->office,
    //         'year'    => $year,
    //         'semester' => $semester,
    //         // 'managerial' => $managerial->ControlNo,
    //         'mfos' => $result
    //     ], 200);
    // }


    // old code

    // public function findManagerial($year, $semester, $mfo)
    // {
    //     $user = Auth::user();

    //     // Get the managerial (office head) of this office
    //     $managerial = Employee::where('rank', 'Office Head')
    //         ->where('office_id', $user->office_id)
    //         ->first();

    //     if (!$managerial) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No managerial employee found.'
    //         ], 404);
    //     }

    //     // Get ALL supervisory employees of this office
    //     $supervisories = Employee::whereIn('rank',['Sub-Office Head','Group Head','Division Head','Section Head','Unit Head'])
    //         ->where('office_id', $user->office_id)
    //         ->get();

    //     // Get the managerial's target period with performance standards
    //     $targetPeriod = TargetPeriod::with('performanceStandards')
    //         ->where('control_no', $managerial->ControlNo)
    //         ->where('year', $year)
    //         ->where('semester', $semester)
    //         ->first();

    //     if (!$targetPeriod) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No target period found for this managerial.'
    //         ], 404);
    //     }

    //     // Get all supervisory control numbers
    //     $supervisoryControlNos = $supervisories->pluck('ControlNo');

    //     // Get ALL supervisory target periods
    //     $supervisoryTargetPeriods = TargetPeriod::with('performanceStandards')
    //         ->whereIn('control_no', $supervisoryControlNos)
    //         ->where('year', $year)
    //         ->where('semester', $semester)
    //         ->get();

    //     // Get all rank-and-file employees (excluding managerial and all supervisory)
    //     $employeeControlNos = Employee::where('office_id', $user->office_id)
    //         ->whereNotIn('rank', ['Managerial', 'Supervisory'])
    //         ->pluck('ControlNo');

    //     // Get all rank-and-file employee target periods for same year and semester
    //     $employeeTargetPeriods = TargetPeriod::with('performanceStandards')
    //         ->whereIn('control_no', $employeeControlNos)
    //         ->where('year', $year)
    //         ->where('semester', $semester)
    //         ->get();

    //     $standards = $mfo
    //         ? $targetPeriod->performanceStandards->where('mfo', $mfo)
    //         : $targetPeriod->performanceStandards;

    //     // Build result for managerial MFOs
    //     // Claimed = all supervisory claimed + all rank-and-file claimed
    //     $result = $standards->map(function ($standard) use ($employeeTargetPeriods, $supervisoryTargetPeriods) {
    //         $totalTarget = $this->extractNumber($standard->success_indicator);

    //         $claimed = 0;

    //         // Sum claimed from ALL supervisory employees
    //         foreach ($supervisoryTargetPeriods as $supPeriod) {
    //             foreach ($supPeriod->performanceStandards as $supStandard) {
    //                 if ($supStandard->mfo === $standard->mfo) {
    //                     $claimed += $this->extractNumber($supStandard->success_indicator);
    //                 }
    //             }
    //         }

    //         // Sum claimed from rank-and-file employees
    //         foreach ($employeeTargetPeriods as $empPeriod) {
    //             foreach ($empPeriod->performanceStandards as $empStandard) {
    //                 if ($empStandard->mfo === $standard->mfo) {
    //                     $claimed += $this->extractNumber($empStandard->success_indicator);
    //                 }
    //             }
    //         }

    //         $available = $totalTarget - $claimed;

    //         return [
    //             'category'              => $standard->category,
    //             'mfo'                   => $standard->mfo,
    //             'output'                => $standard->output,
    //             'output_name'           => $standard->output_name,
    //             'performance_indicator' => $standard->performance_indicator,
    //             'success_indicator'     => $standard->success_indicator,
    //             'total_target'          => $totalTarget,
    //             'claimed'               => $claimed,
    //             'available'             => max(0, $available),
    //         ];
    //     });

    //     // Build supervisory MFO data for EACH supervisory employee
    //     $supervisoryData = $supervisories->map(function ($supervisory) use (
    //         $supervisoryTargetPeriods,
    //         $employeeTargetPeriods,
    //         $mfo
    //     ) {
    //         $supTargetPeriod = $supervisoryTargetPeriods
    //             ->where('control_no', $supervisory->ControlNo)
    //             ->first();

    //         if (!$supTargetPeriod) {
    //             return [
    //                 'controlNo' => $supervisory->ControlNo,
    //                 'name'      => $supervisory->name,
    //                 'rank'      => $supervisory->rank,
    //                 'mfos'      => null,
    //             ];
    //         }

    //         $supStandards = $mfo
    //             ? $supTargetPeriod->performanceStandards->where('mfo', $mfo)
    //             : $supTargetPeriod->performanceStandards;

    //         // Claimed from rank-and-file only (against this supervisory's targets)
    //         $mfos = $supStandards->map(function ($standard) use ($employeeTargetPeriods) {
    //             $totalTarget = $this->extractNumber($standard->success_indicator);

    //             $claimed = 0;

    //             foreach ($employeeTargetPeriods as $empPeriod) {
    //                 foreach ($empPeriod->performanceStandards as $empStandard) {
    //                     if ($empStandard->mfo === $standard->mfo) {
    //                         $claimed += $this->extractNumber($empStandard->success_indicator);
    //                     }
    //                 }
    //             }

    //             $available = $totalTarget - $claimed;

    //             return [
    //                 'category'              => $standard->category,
    //                 'mfo'                   => $standard->mfo,
    //                 'output'                => $standard->output,
    //                 'output_name'           => $standard->output_name,
    //                 'performance_indicator' => $standard->performance_indicator,
    //                 'success_indicator'     => $standard->success_indicator,
    //                 'total_target'          => $totalTarget,
    //                 'claimed'               => $claimed,
    //                 'available'             => max(0, $available),
    //             ];
    //         });

    //         return [
    //             'controlNo' => $supervisory->ControlNo,
    //             'name'      => $supervisory->name,
    //             'rank'      => $supervisory->rank,
    //             'mfos'      => $mfos->values(),
    //         ];
    //     });

    //     return response()->json([
    //         'controlNo'    => $managerial->ControlNo,
    //         'name'         => $managerial->name,
    //         'rank'         => $managerial->rank,
    //         'office'       => $managerial->office,
    //         'year'         => $year,
    //         'semester'     => $semester,
    //         'mfos'         => $result,
    //         'supervisories' => $supervisoryData->values(),
    //     ], 200);
    // }
    // // Extract the leading number from a success_indicator string
    // private function extractNumber(string $string): int
    // {
    //     preg_match('/^\d+/', trim($string), $matches);
    //     return isset($matches[0]) ? (int) $matches[0] : 0;
    // }

    // find the office head and supervisory on office
    public function findManagerial($year, $semester, $mfo)
    {

    $result = $this->unitWorkPlanService->supervisoryDeductionOfSuccessIndicator($year,$semester,$mfo);

    return $result;

    }

}
