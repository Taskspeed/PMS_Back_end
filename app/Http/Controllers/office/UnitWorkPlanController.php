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

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->officeId = $this->user->office_id;

            return $next($request);
        });
    }


    // unit work plan store function
    // public function storeUnitWorkPlan(Request $request) //  old working store function
    // {
    //     $validated = $request->validate([

    //         'employees' => 'required|array|min:1',

    //         // employee details
    //         'employees.*.control_no' => 'required|string',
    //         'employees.*.office' => 'required|string',
    //         'employees.*.office2' => 'nullable|string',
    //         'employees.*.group' => 'nullable|string',
    //         'employees.*.division' => 'nullable|string',
    //         'employees.*.section' => 'nullable|string',
    //         'employees.*.unit' => 'nullable|string',

    //         // semester and year
    //         'employees.*.semester' => 'required|string',
    //         'employees.*.year' => 'required|integer',

    //         // performance standards
    //         'employees.*.performance_standards' => 'required|array|min:1',
    //         'employees.*.performance_standards.*.category' => 'required|string',
    //         'employees.*.performance_standards.*.mfo' => 'nullable|string',
    //         'employees.*.performance_standards.*.output' => 'nullable|string',
    //         'employees.*.performance_standards.*.output_name' => 'nullable|string',
    //         'employees.*.performance_standards.*.core_competency' => 'nullable|array',
    //         'employees.*.performance_standards.*.technical_competency' => 'nullable|array',
    //         'employees.*.performance_standards.*.leadership_competency' => 'nullable|array',
    //         'employees.*.performance_standards.*.success_indicator' => 'required|string',
    //         'employees.*.performance_standards.*.performance_indicator' => 'required|array',
    //         'employees.*.performance_standards.*.required_output' => 'required|string',

    //         // standatd outcomes / ratings
    //         'employees.*.performance_standards.*.ratings' => 'required|array|min:1',
    //         'employees.*.performance_standards.*.ratings.*.rating' => 'nullable|integer',
    //         'employees.*.performance_standards.*.ratings.*.quantity' => 'nullable|string',
    //         'employees.*.performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
    //         'employees.*.performance_standards.*.ratings.*.timeliness' => 'nullable|string',

    //         // CONFIG (single object)
    //         'employees.*.performance_standards.*.config' => 'required|array',
    //         'employees.*.performance_standards.*.config.target_output' => 'required|string',
    //         'employees.*.performance_standards.*.config.quantity_indicator' => 'required|string',
    //         'employees.*.performance_standards.*.config.timeliness_indicator' => 'required|string',

    //         'employees.*.performance_standards.*.config.timelinessType' => 'required|array',
    //         'employees.*.performance_standards.*.config.timelinessType.range' => 'required|boolean',
    //         'employees.*.performance_standards.*.config.timelinessType.date' => 'required|boolean',
    //         'employees.*.performance_standards.*.config.timelinessType.description' => 'required|boolean',



    //     ]);

    //     DB::beginTransaction(); // Start transaction

    //     try {
    //         foreach ($validated['employees'] as $employeeData) {
    //             // Check if already exists
    //             $existing = TargetPeriod::where('control_no', $employeeData['control_no'])
    //                 ->where('semester', $employeeData['semester'])
    //                 ->where('year', $employeeData['year'])
    //                 ->first();

    //             if ($existing) {
    //                 throw new \Exception("Employee ({$employeeData['control_no']}) already has a Unit Work Plan for {$employeeData['semester']} {$employeeData['year']}.");
    //             }

    //             // Create Target Period
    //             $targetPeriod = TargetPeriod::create([
    //                 'control_no' => $employeeData['control_no'],
    //                 'semester' => $employeeData['semester'],
    //                 'year' => $employeeData['year'],
    //                 'office' => $employeeData['office'],
    //                 'office2' => $employeeData['office2'] ?? null,
    //                 'group' => $employeeData['group'] ?? null,
    //                 'division' => $employeeData['division'] ?? null,
    //                 'section' => $employeeData['section'] ?? null,
    //                 'unit' => $employeeData['unit'] ?? null,
    //                 'status' => 'pending',
    //             ]);

    //             // Create Performance Standards
    //             foreach ($employeeData['performance_standards'] as $standard) {
    //                 $performanceStandard = PerformanceStandard::create([
    //                     'target_period_id' => $targetPeriod->id,
    //                     'category' => $standard['category'],
    //                     'mfo' => $standard['mfo'],
    //                     'output' => $standard['output'],
    //                     'output_name' => $standard['output_name'],
    //                     'core' => $standard['core_competency'] ?? NULL,
    //                     'technical' => $standard['technical_competency'] ?? NULL,
    //                     'leadership' => $standard['leadership_competency'] ??  NULL,
    //                     'performance_indicator' => $standard['performance_indicator'],
    //                     'success_indicator' => $standard['success_indicator'],
    //                     'required_output' => $standard['required_output'],
    //                 ]);

    //                 foreach ($standard['ratings'] as $rating) {
    //                     StandardOutcome::create([
    //                         'performance_standard_id' => $performanceStandard->id,
    //                         'rating' => $rating['rating'],
    //                         'quantity_target' => $rating['quantity'],
    //                         'effectiveness_criteria' => $rating['effectiveness'],
    //                         'timeliness_range' => $rating['timeliness'],
    //                     ]);
    //                 }



    //                 $config = $standard['config']; // single object

    //                 PerformanceConfigurations::create([
    //                     'performance_standard_id' => $performanceStandard->id,
    //                     'target_output' => $config['target_output'],
    //                     'quantity_indicator' => $config['quantity_indicator'],
    //                     'timeliness_indicator' => $config['timeliness_indicator'],
    //                     'timeliness_range' => $config['timelinessType']['range'],
    //                     'timeliness_date' => $config['timelinessType']['date'],
    //                     'timeliness_description' => $config['timelinessType']['description'],
    //                 ]);
    //             }
    //         }

    //         DB::commit(); // Commit transaction

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Unit Work Plans for all employees created successfully.'
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Rollback if any error occurs

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create Unit Work Plan.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // storing unit work plan
    public function addUnitWorkPlan(addEmployeeUnitWorkPlanRequest $request, UnitWorkPlanService $unitWorkPlanService)
    {
        $validated = $request->validated();

        $unitworkplan = $unitWorkPlanService->store($validated);

        return response()->json([
            'success' => true,
            'message' => 'Unit Work Plans for all employees created successfully.',
            'target_period' => $unitworkplan['target_period'],
            'performance_standard' => $unitworkplan['performance_standard'],
            'standard_outcome' => $unitworkplan['standard_outcome'],
            'configuration' => $unitworkplan['configuration'],
        ]);
    }


    // updating the unit work plan of employee
    // public function updateUnitWorkPlan(Request $request, $controlNo, $semester, $year)
    // {
    //     $validated = $request->validate([
    //         'performance_standards' => 'required|array|min:1',
    //         'performance_standards.*.category' => 'required|string',
    //         'performance_standards.*.mfo' => 'required|string',
    //         'performance_standards.*.output' => 'nullable|string',
    //         'performance_standards.*.output_name' => 'required|string', // ✅ Should match store
    //         'performance_standards.*.core_competency' => 'nullable|array',
    //         'performance_standards.*.technical_competency' => 'nullable|array',
    //         'performance_standards.*.leadership_competency' => 'nullable|array',
    //         'performance_standards.*.success_indicator' => 'required|string',
    //         'performance_standards.*.performance_indicator' => 'required|array',
    //         'performance_standards.*.required_output' => 'required|string',

    //         'performance_standards.*.ratings' => 'required|array|min:1',
    //         'performance_standards.*.ratings.*.rating' => 'nullable|integer',
    //         'performance_standards.*.ratings.*.quantity' => 'nullable|string',
    //         'performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
    //         'performance_standards.*.ratings.*.timeliness' => 'nullable|string',

    //         'performance_standards.*.config' => 'required|array',
    //         'performance_standards.*.config.target_output' => 'required|string',
    //         'performance_standards.*.config.quantity_indicator' => 'required|string',
    //         'performance_standards.*.config.timeliness_indicator' => 'required|string',

    //         'performance_standards.*.config.timelinessType' => 'required|array',
    //         'performance_standards.*.config.timelinessType.range' => 'required|boolean',
    //         'performance_standards.*.config.timelinessType.date' => 'required|boolean',
    //         'performance_standards.*.config.timelinessType.description' => 'required|boolean',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         // ✅ STEP 1: Employee + office restriction
    //         $employee = Employee::where('ControlNo', $controlNo)
    //             ->where('office_id', $this->officeId)
    //             ->first();

    //         if (!$employee) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Employee not found or access denied.'
    //             ], 404);
    //         }

    //         // ✅ STEP 2: Target Period
    //         $targetPeriod = $employee->targetPeriods()
    //             ->where('year', $year)
    //             ->where('semester', $semester)
    //             ->first();

    //         if (!$targetPeriod) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unit Work Plan not found.'
    //             ], 404);
    //         }

    //         // OPTIONAL: prevent update if approved
    //         // if ($targetPeriod->status === 'approve') {
    //         //     return response()->json([
    //         //         'success' => false,
    //         //         'message' => 'Approved Unit Work Plan cannot be edited.'
    //         //     ], 403);
    //         // }

    //         // ✅ Reset status
    //         $targetPeriod->update([
    //             'status' => 'pending',
    //         ]);

    //         // ✅ DELETE CHILD RECORDS IN CORRECT ORDER (reverse dependency)
    //         // 1. Delete PerformanceConfigurations first
    //         PerformanceConfigurations::whereIn(
    //             'performance_standard_id',
    //             PerformanceStandard::where('target_period_id', $targetPeriod->id)->pluck('id')
    //         )->delete();

    //         // 2. Delete StandardOutcome
    //         StandardOutcome::whereIn(
    //             'performance_standard_id',
    //             PerformanceStandard::where('target_period_id', $targetPeriod->id)->pluck('id')
    //         )->delete();

    //         // 3. Delete PerformanceStandard last
    //         PerformanceStandard::where('target_period_id', $targetPeriod->id)->delete();

    //         // ✅ RE-CREATE PERFORMANCE STANDARDS
    //         foreach ($validated['performance_standards'] as $standard) {
    //             $performanceStandard = PerformanceStandard::create([
    //                 'target_period_id' => $targetPeriod->id,
    //                 'category' => $standard['category'],
    //                 'mfo' => $standard['mfo'],
    //                 'output' => $standard['output'],
    //                 'output_name' => $standard['output_name'], // ✅ ADDED - was missing
    //                 'core' => $standard['core_competency'] ??  NULL,
    //                 'technical' => $standard['technical_competency'] ??  NULL,
    //                 'leadership' => $standard['leadership_competency'] ??  NULL,
    //                 'performance_indicator' => $standard['performance_indicator'],
    //                 'success_indicator' => $standard['success_indicator'],
    //                 'required_output' => $standard['required_output'],
    //             ]);

    //             // ✅ Create ratings (StandardOutcome)
    //             foreach ($standard['ratings'] as $rating) {
    //                 StandardOutcome::create([
    //                     'performance_standard_id' => $performanceStandard->id,
    //                     'rating' => $rating['rating'],
    //                     'quantity_target' => $rating['quantity'],
    //                     'effectiveness_criteria' => $rating['effectiveness'],
    //                     'timeliness_range' => $rating['timeliness'],
    //                 ]);
    //             }

    //             // ✅ Create config
    //             $config = $standard['config'];

    //             PerformanceConfigurations::create([
    //                 'performance_standard_id' => $performanceStandard->id,
    //                 'target_output' => $config['target_output'],
    //                 'quantity_indicator' => $config['quantity_indicator'],
    //                 'timeliness_indicator' => $config['timeliness_indicator'],
    //                 'timeliness_range' => $config['timelinessType']['range'],
    //                 'timeliness_date' => $config['timelinessType']['date'],
    //                 'timeliness_description' => $config['timelinessType']['description'],
    //             ]);
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Unit Work Plan updated successfully.'
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to update Unit Work Plan.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // updating unit work plan

    // args controlno, semester, year
    public function updateUnitWorkPlan(updateEmployeeUnitWorkPlanRequest $request, $controlNo, $semester, $year, UnitWorkPlanService $unitworkplanService){

        $validated = $request->validated();

        $unitworkplan = $unitworkplanService->update($validated, $controlNo, $semester, $year);


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

        $organization = $unitWorkPlanService->organization($request);

        return new UnitWorkPlanOrganizationResource($organization);
    }
}
