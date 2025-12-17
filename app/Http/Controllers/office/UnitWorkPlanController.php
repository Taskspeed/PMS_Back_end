<?php


namespace App\Http\Controllers\office;

use App\Models\Mfo;
use App\Models\Employee;
use App\Models\F_outpot;
use App\Models\F_category;
use App\Models\TargatPeriod;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use App\Models\Unit_work_plan;
use App\Models\StandardOutcome;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceStandard;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;

class UnitWorkPlanController extends Controller
{

    // unit work plan store function
    public function storeUnitWorkPlan(Request $request) //  old working store function
    {
        $validated = $request->validate([

            'employees' => 'required|array|min:1',

             // employee details
            'employees.*.control_no' => 'required|string',
            'employees.*.office' => 'required|string',
            'employees.*.office2' => 'nullable|string',
            'employees.*.group' => 'nullable|string',
            'employees.*.division' => 'nullable|string',
            'employees.*.section' => 'nullable|string',
            'employees.*.unit' => 'nullable|string',

            // semester and year
            'employees.*.semester' => 'required|string',
            'employees.*.year' => 'required|integer',

            // performance standards
            'employees.*.performance_standards' => 'required|array|min:1',
            'employees.*.performance_standards.*.category' => 'required|string',
            'employees.*.performance_standards.*.mfo' => 'required|string',
            'employees.*.performance_standards.*.output' => 'required|string',
            'employees.*.performance_standards.*.core_competency' => 'nullable|array',
            'employees.*.performance_standards.*.technical_competency' => 'nullable|array',
            'employees.*.performance_standards.*.leadership_competency' => 'nullable|array',
            'employees.*.performance_standards.*.success_indicator' => 'required|string',
            'employees.*.performance_standards.*.performance_indicator' => 'required|string',
            'employees.*.performance_standards.*.required_output' => 'required|string',

            // standatd outcomes / ratings
            'employees.*.performance_standards.*.ratings' => 'required|array|min:1',
            'employees.*.performance_standards.*.ratings.*.rating' => 'nullable|integer',
            'employees.*.performance_standards.*.ratings.*.quantity' => 'nullable|string',
            'employees.*.performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
            'employees.*.performance_standards.*.ratings.*.timeliness' => 'nullable|string',

            'employees.*.performance_standards.*.config' => 'required|array',
            'employees.*.performance_standards.*.config.*.quantity' => 'required|string',
            'employees.*.performance_standards.*.config.*.timeliness' => 'required|string',
            'employees.*.performance_standards.*.config.*.type' => 'required|string',




        ]);

        DB::beginTransaction(); // Start transaction

        try {
            foreach ($validated['employees'] as $employeeData) {
                // Check if already exists
                $existing = TargetPeriod::where('control_no', $employeeData['control_no'])
                    ->where('semester', $employeeData['semester'])
                    ->where('year', $employeeData['year'])
                    ->first();

                if ($existing) {
                    throw new \Exception("Employee ({$employeeData['control_no']}) already has a Unit Work Plan for {$employeeData['semester']} {$employeeData['year']}.");
                }

                // Create Target Period
                $targetPeriod = TargetPeriod::create([
                    'control_no' => $employeeData['control_no'],
                    'semester' => $employeeData['semester'],
                    'year' => $employeeData['year'],
                    'office' => $employeeData['office'],
                    'office2' => $employeeData['office2'] ?? null,
                    'group' => $employeeData['group'] ?? null,
                    'division' => $employeeData['division'] ?? null,
                    'section' => $employeeData['section'] ?? null,
                    'unit' => $employeeData['unit'] ?? null,
                    'status' => 'pending',
                ]);

                // Create Performance Standards
                foreach ($employeeData['performance_standards'] as $standard) {
                    $performanceStandard = PerformanceStandard::create([
                        'target_period_id' => $targetPeriod->id,
                        'category' => $standard['category'],
                        'mfo' => $standard['mfo'],
                        'output' => $standard['output'],
                        'core' => $standard['core_competency'] ?? [],
                        'technical' => $standard['technical_competency'] ?? [],
                        'leadership' => $standard['leadership_competency'] ?? [],
                        'performance_indicator' => $standard['performance_indicator'],
                        'success_indicator' => $standard['success_indicator'],
                        'required_output' => $standard['required_output'],
                    ]);

                    foreach ($standard['ratings'] as $rating) {
                        StandardOutcome::create([
                            'target_period_id' => $targetPeriod->id,
                            'rating' => $rating['rating'],
                            'quantity_target' => $rating['quantity'],
                            'effectiveness_criteria' => $rating['effectiveness'],
                            'timeliness_range' => $rating['timeliness'],
                        ]);
                    }

                    foreach ($standard['config'] as $config) {
                        Configuration::create([
                            'performance_standard_id' => $performanceStandard->id,
                            'quantity' => $config['quantity'],
                            'timeliness' => $config['timeliness'],
                            'type' => $config['type'],
                        ]);
                    }
                }
            }

            DB::commit(); // Commit transaction

            return response()->json([
                'success' => true,
                'message' => 'Unit Work Plans for all employees created successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error occurs

            return response()->json([
                'success' => false,
                'message' => 'Failed to create Unit Work Plan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //updating the unit work plan of employee
    public function updateUnitWorkPlan(Request $request, $controlNo, $semester, $year)
    {
        $validated = $request->validate([
            'performance_standards' => 'required|array|min:1',
            'performance_standards.*.category' => 'required|string',
            'performance_standards.*.mfo' => 'required|string',
            'performance_standards.*.output' => 'required|string',
            'performance_standards.*.core_competency' => 'nullable|array',
            'performance_standards.*.technical_competency' => 'nullable|array',
            'performance_standards.*.leadership_competency' => 'nullable|array',
            'performance_standards.*.success_indicator' => 'required|string',
            'performance_standards.*.performance_indicator' => 'required|string',
            'performance_standards.*.required_output' => 'required|string',

            'performance_standards.*.ratings' => 'required|array|min:1',
            'performance_standards.*.ratings.*.rating' => 'nullable|integer',
            'performance_standards.*.ratings.*.quantity' => 'nullable|string',
            'performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
            'performance_standards.*.ratings.*.timeliness' => 'nullable|string',


            'performance_standards.*.config' => 'required|array',
            'performance_standards.*.config.*.quantity' => 'required|string',
            'performance_standards.*.config.*.timeliness' => 'required|string',
            'performance_standards.*.config.*.type' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // ✅ FIND TARGET PERIOD USING control_no + semester + year
            $targetPeriod = TargetPeriod::where('control_no', $controlNo)
                ->where('semester', $semester)
                ->where('year', $year)
                ->firstOrFail();

            // OPTIONAL: prevent update if already approved
            // if ($targetPeriod->status === 'approved') {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Approved Unit Work Plan cannot be edited.'
            //     ], 403);
            // }

            // RESET STATUS
            $targetPeriod->update([
                'status' => 'pending',
            ]);



            // ✅ delete ALL old data correctly
            StandardOutcome::where('target_period_id', $targetPeriod->id)->delete();
            PerformanceStandard::where('target_period_id', $targetPeriod->id)->delete();


            // RE-CREATE PERFORMANCE STANDARDS
            foreach ($validated['performance_standards'] as $standard) {
                $performanceStandard = PerformanceStandard::create([
                    'target_period_id' => $targetPeriod->id,
                    'category' => $standard['category'],
                    'mfo' => $standard['mfo'],
                    'output' => $standard['output'],
                    'core' => $standard['core_competency'] ?? null,
                    'technical' => $standard['technical_competency'] ?? null,
                    'leadership' => $standard['leadership_competency'] ?? null,
                    'performance_indicator' => $standard['performance_indicator'],
                    'success_indicator' => $standard['success_indicator'],
                    'required_output' => $standard['required_output'],
                ]);

                foreach ($standard['ratings'] as $rating) {
                    StandardOutcome::create([
                        'target_period_id' => $targetPeriod->id,
                        'rating' => $rating['rating'],
                        'quantity_target' => $rating['quantity'],
                        'effectiveness_criteria' => $rating['effectiveness'],
                        'timeliness_range' => $rating['timeliness'],
                    ]);
                }

                foreach ($standard['config'] as $config) {
                    Configuration::create([
                        'performance_standard_id' => $performanceStandard->id,
                        'quantity' => $config['quantity'],
                        'timeliness' => $config['timeliness'],
                        'type' => $config['type'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unit Work Plan updated successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Unit Work Plan.',
                'error' => $e->getMessage()
            ], 500);
        }
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
            ->with([
                'targetPeriods' => function ($q) use ($year, $semester) {
                    $q->where('year', $year)
                        ->where('semester', $semester)
                        ->with(['performanceStandards.configurations', 'standardOutcomes']);
                }
            ])
            ->first();

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }

        // Transform the response
        $employee = $employee->toArray();

        foreach ($employee['target_periods'] as &$period) {
            $configs = [];
            foreach ($period['performance_standards'] as $ps) {
                if (!empty($ps['configurations'])) {
                    $configs = array_merge($configs, $ps['configurations']);
                }
                // Remove configurations from inside performance_standards
                unset($ps['configurations']);
            }
            $period['configurations'] = $configs;
        }

        return response()->json($employee);
    }

    // public function getUnitworkplan($controlNo, $semester, $year)
    // {
    //     $employee = Employee::where('ControlNo', $controlNo)
    //         ->with([
    //             'targetPeriods' => function ($q) use ($year, $semester) {
    //                 $q->where('year', $year)
    //                     ->where('semester', $semester)
    //                     ->with(['performanceStandards.configurations', 'standardOutcomes', ]);
    //             }
    //         ])
    //         ->first();

    //     if (!$employee) {
    //         return response()->json(['message' => 'Employee not found'], 404);
    //     }

    //     return response()->json($employee);
    // }




    // public function storeUnitWorkPlan(Request $request)
    // {
    //     $validated = $request->validate([
    //         'employees' => 'required|array|min:1',
    //         'employees.*.control_no' => 'required|string',
    //         'employees.*.office' => 'required|string',
    //         'employees.*.office2' => 'nullable|string',
    //         'employees.*.group' => 'nullable|string',
    //         'employees.*.division' => 'nullable|string',
    //         'employees.*.section' => 'nullable|string',
    //         'employees.*.unit' => 'nullable|string',
    //         'employees.*.semester' => 'required|string',
    //         'employees.*.year' => 'required|integer',
    //         'employees.*.performance_standards' => 'required|array|min:1',
    //         'employees.*.performance_standards.*.category' => 'required|string',
    //         'employees.*.performance_standards.*.mfo' => 'required|string',
    //         'employees.*.performance_standards.*.output' => 'required|string',
    //         'employees.*.performance_standards.*.core_competency' => 'nullable|array',
    //         'employees.*.performance_standards.*.technical_competency' => 'nullable|array',
    //         'employees.*.performance_standards.*.leadership_competency' => 'nullable|array',
    //         'employees.*.performance_standards.*.success_indicator' => 'required|string',
    //         'employees.*.performance_standards.*.performance_indicator' => 'required|string',
    //         'employees.*.performance_standards.*.required_output' => 'required|string',
    //         'employees.*.performance_standards.*.ratings' => 'required|array|min:1',
    //         'employees.*.performance_standards.*.ratings.*.rating' => 'nullable|integer',
    //         'employees.*.performance_standards.*.ratings.*.quantity' => 'nullable|string',
    //         'employees.*.performance_standards.*.ratings.*.effectiveness' => 'nullable|string',
    //         'employees.*.performance_standards.*.ratings.*.timeliness' => 'nullable|string',
    //     ]);

    //     foreach ($validated['employees'] as $employeeData) {

    //         // UPDATE OR CREATE TARGET PERIOD
    //         $targetPeriod = TargetPeriod::updateOrCreate(
    //             [
    //                 'control_no' => $employeeData['control_no'],
    //                 'semester' => $employeeData['semester'],
    //                 'year' => $employeeData['year'],
    //             ],
    //             [
    //                 'office' => $employeeData['office'],
    //                 'office2' => $employeeData['office2'] ?? null,
    //                 'group' => $employeeData['group'] ?? null,
    //                 'division' => $employeeData['division'] ?? null,
    //                 'section' => $employeeData['section'] ?? null,
    //                 'unit' => $employeeData['unit'] ?? null,
    //                 'status' => 'pending', // always set to pending
    //             ]
    //         );

    //         // DELETE existing performance standards and outcomes to replace with new data
    //         $targetPeriod->performanceStandards()->delete();

    //         $targetPeriod->standardOutcomes()->delete();

    //         // CREATE MULTIPLE PERFORMANCE STANDARDS
    //         foreach ($employeeData['performance_standards'] as $standard) {
    //             $performanceStandard = PerformanceStandard::create([
    //                 'target_period_id' => $targetPeriod->id,
    //                 'category' => $standard['category'],
    //                 'mfo' => $standard['mfo'],
    //                 'output' => $standard['output'],
    //                 'core' => $standard['core_competency'] ?? NULL,
    //                 'technical' => $standard['technical_competency'] ?? NULL,
    //                 'leadership' => $standard['leadership_competency'] ?? NULL,
    //                 'performance_indicator' => $standard['performance_indicator'],
    //                 'success_indicator' => $standard['success_indicator'],
    //                 'required_output' => $standard['required_output'],
    //             ]);

    //             foreach ($standard['ratings'] as $rating) {
    //                 StandardOutcome::create([
    //                     'target_period_id' => $targetPeriod->id,
    //                     'rating' => $rating['rating'],
    //                     'quantity_target' => $rating['quantity'],
    //                     'effectiveness_criteria' => $rating['effectiveness'],
    //                     'timeliness_range' => $rating['timeliness'],
    //                 ]);
    //             }
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Unit Work Plans for all employees created or updated successfully.'
    //     ]);
    // }



    // Add a new endpoint for getting counts
    // public function getEmployeeCountAndUnitworkplan(Request $request)
    // {
    //     $user = Auth::user();
    //     $officeId = $user->office_id;

    //     if (!$officeId) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Office ID is required'
    //         ], 400);
    //     }

    //     // Helper: Only return counts (NO employee list)
    //     $addWorkplanInfo = function ($grouped) {
    //         return $grouped->map(function ($group) {
    //             $total = $group->count();
    //             $withWorkplan = $group->filter(function ($emp) {
    //                 return \App\Models\TargetPeriod::where('control_no', $emp->ControlNo)->exists();
    //             })->count();

    //             return [
    //                 'unitWorkPlan' => "$withWorkplan/$total"
    //             ];
    //         });
    //     };

    //     // OFFICE (no division/section/unit)
    //     $officeEmployees = Employee::where('office_id', $officeId)
    //         ->whereNull('division')
    //         ->whereNull('section')
    //         ->whereNull('unit')
    //         ->select('id', 'name', 'ControlNo')
    //         ->get();

    //     $officeWorkplan = $officeEmployees->filter(function ($emp) {
    //         return TargetPeriod::where('control_no', $emp->ControlNo)->exists();
    //     })->count();

    //     // DIVISIONS
    //     $divisionData = Employee::where('office_id', $officeId)
    //         ->whereNotNull('division')
    //         ->whereNull('section')
    //         ->whereNull('unit')
    //         ->select('division', 'ControlNo')
    //         ->get()
    //         ->groupBy('division');

    //     $divisionData = $addWorkplanInfo($divisionData);

    //     // SECTIONS
    //     $sectionData = Employee::where('office_id', $officeId)
    //         ->whereNotNull('section')
    //         ->whereNull('unit')
    //         ->select('section', 'ControlNo')
    //         ->get()
    //         ->groupBy('section');

    //     $sectionData = $addWorkplanInfo($sectionData);

    //     // UNITS
    //     $unitData = Employee::where('office_id', $officeId)
    //         ->whereNotNull('unit')
    //         ->select('unit', 'ControlNo')
    //         ->get()
    //         ->groupBy('unit');

    //     $unitData = $addWorkplanInfo($unitData);

    //     // GROUPS
    //     $groupData = Employee::where('office_id', $officeId)
    //         ->whereNotNull('group')
    //         ->select('group', 'ControlNo')
    //         ->get()
    //         ->groupBy('group');

    //     $groupData = $addWorkplanInfo($groupData);

    //     // OFFICE2
    //     $office2Data = Employee::where('office_id', $officeId)
    //         ->whereNotNull('office2')
    //         ->select('office2', 'ControlNo')
    //         ->get()
    //         ->groupBy('office2');

    //     $office2Data = $addWorkplanInfo($office2Data);


    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'office' => [
    //                 'unitWorkPlan' => $officeWorkplan . "/" . $officeEmployees->count()
    //             ],
    //             'divisions' => $divisionData,
    //             'sections' => $sectionData,
    //             'units' => $unitData,
    //             'groups' => $groupData,
    //             'office2' => $office2Data
    //         ]
    //     ]);
    // }
    //     public function getEmployeeCountAndUnitworkplan(Request $request)
    //     {
    //         $user = Auth::user();
    //         $officeId = $user->office_id;

    //         if (!$officeId) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Office ID is required'
    //             ], 400);
    //         }

    //         // 1. Get office name
    //         $officeName = DB::table('offices')->where('id', $officeId)->value('name');
    //         if (!$officeName) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Office not found'
    //             ], 404);
    //         }

    //         // 2. Fetch plantilla structure records
    //         $plantilla = DB::table('vwplantillastructure')
    //             ->where('office', $officeName)
    //             ->orderBy('office2')
    //             ->orderBy('group')
    //             ->orderBy('division')
    //             ->orderBy('section')
    //             ->orderBy('unit')
    //             ->get();

    //         // 3. Helper count function
    //         $countWorkplan = function ($query) {
    //             $total = $query->count();
    //             $with = $query->filter(function ($emp) {
    //                 return TargetPeriod::where('control_no', $emp->ControlNo)->exists();
    //             })->count();

    //             return "$with/$total";
    //         };

    //         // 4. Preload all employees of this office (1 query only)
    //         $employees = Employee::where('office_id', $officeId)
    //             ->select('ControlNo', 'office2', 'group', 'division', 'section', 'unit')
    //             ->get();

    //         // 5. Start final output - FIX: Use filter instead of where for null checks
    //         $result = [
    //             "office" => [
    //                 "name" => $officeName,
    //                 "unitWorkPlan" => $countWorkplan($employees->filter(function ($e) {
    //                     return is_null($e->division) && is_null($e->section) && is_null($e->unit);
    //                 }))
    //             ],
    //             "office2" => []
    //         ];

    //         /* ============================================================
    //    LOOP OFFICE2
    // ============================================================ */
    //         foreach ($plantilla->unique('office2') as $o2) {

    //             $office2Name = $o2->office2;
    //             $office2Employees = $employees->where('office2', $office2Name);

    //             $office2Block = [
    //                 "office2" => $office2Name,
    //                 "unitWorkPlan" => $countWorkplan($office2Employees),
    //                 "group" => []
    //             ];

    //             /* ============================================================
    //        LOOP GROUP
    //     ============================================================ */
    //             foreach ($plantilla->where('office2', $office2Name)->unique('group') as $grp) {

    //                 $groupName = $grp->group;
    //                 $groupEmployees = $office2Employees->where('group', $groupName);

    //                 $groupBlock = [
    //                     "group" => $groupName,
    //                     "unitWorkPlan" => $countWorkplan($groupEmployees),
    //                     "divisions" => [],
    //                     "sections_without_division" => [],
    //                     "units_without_division" => []
    //                 ];

    //                 /* ============================================================
    //            DIVISIONS
    //         ============================================================ */
    //                 $divisions = $plantilla
    //                     ->where('office2', $office2Name)
    //                     ->where('group', $groupName)
    //                     ->whereNotNull('division')
    //                     ->unique('division');

    //                 foreach ($divisions as $div) {
    //                     $divisionName = $div->division;
    //                     $divisionEmployees = $groupEmployees->where('division', $divisionName);

    //                     $divisionBlock = [
    //                         "division" => $divisionName,
    //                         "unitWorkPlan" => $countWorkplan($divisionEmployees),
    //                         "sections" => [],
    //                         "units_without_section" => []
    //                     ];

    //                     // Sections under this division
    //                     $sections = $plantilla
    //                         ->where('division', $divisionName)
    //                         ->whereNotNull('section')
    //                         ->unique('section');

    //                     foreach ($sections as $sec) {
    //                         $sectionName = $sec->section;
    //                         $sectionEmployees = $divisionEmployees->where('section', $sectionName);

    //                         $sectionBlock = [
    //                             "section" => $sectionName,
    //                             "unitWorkPlan" => $countWorkplan($sectionEmployees),
    //                             "units" => []
    //                         ];

    //                         // Units under this section
    //                         $units = $plantilla
    //                             ->where('division', $divisionName)
    //                             ->where('section', $sectionName)
    //                             ->pluck('unit')
    //                             ->unique()
    //                             ->filter()
    //                             ->values();

    //                         foreach ($units as $unitName) {
    //                             $unitEmployees = $sectionEmployees->where('unit', $unitName);

    //                             $sectionBlock["units"][] = [
    //                                 "unit" => $unitName,
    //                                 "unitWorkPlan" => $countWorkplan($unitEmployees)
    //                             ];
    //                         }

    //                         $divisionBlock["sections"][] = $sectionBlock;
    //                     }

    //                     // Units without section - FIX: Use filter for null checks
    //                     $unitsWithoutSection = $plantilla
    //                         ->where('division', $divisionName)
    //                         ->filter(function ($item) {
    //                             return is_null($item->section);
    //                         })
    //                         ->pluck('unit')
    //                         ->unique()
    //                         ->filter()
    //                         ->values();

    //                     foreach ($unitsWithoutSection as $u) {
    //                         $unitEmployees = $divisionEmployees->filter(function ($emp) use ($u) {
    //                             return is_null($emp->section) && $emp->unit == $u;
    //                         });
    //                         $divisionBlock["units_without_section"][] = [
    //                             "unit" => $u,
    //                             "unitWorkPlan" => $countWorkplan($unitEmployees)
    //                         ];
    //                     }

    //                     $groupBlock["divisions"][] = $divisionBlock;
    //                 }

    //                 /* ============================================================
    //            SECTIONS WITHOUT DIVISION
    //         ============================================================ */
    //                 $sectionsWithoutDivision = $plantilla
    //                     ->filter(function ($item) {
    //                         return is_null($item->division);
    //                     })
    //                     ->whereNotNull('section')
    //                     ->where('group', $groupName)
    //                     ->where('office2', $office2Name)
    //                     ->unique('section');

    //                 foreach ($sectionsWithoutDivision as $sec) {

    //                     $sectionName = $sec->section;
    //                     // FIX: Use filter for null checks on collections
    //                     $sectionEmployees = $groupEmployees->filter(function ($emp) use ($sectionName) {
    //                         return is_null($emp->division) && $emp->section == $sectionName;
    //                     });

    //                     $sectionBlock = [
    //                         "section" => $sectionName,
    //                         "unitWorkPlan" => $countWorkplan($sectionEmployees),
    //                         "units" => []
    //                     ];

    //                     $units = $plantilla
    //                         ->where('section', $sectionName)
    //                         ->filter(function ($item) {
    //                             return is_null($item->division);
    //                         })
    //                         ->pluck('unit')
    //                         ->unique()
    //                         ->filter()
    //                         ->values();

    //                     foreach ($units as $u) {
    //                         $unitEmployees = $sectionEmployees->where('unit', $u);
    //                         $sectionBlock["units"][] = [
    //                             "unit" => $u,
    //                             "unitWorkPlan" => $countWorkplan($unitEmployees)
    //                         ];
    //                     }

    //                     $groupBlock["sections_without_division"][] = $sectionBlock;
    //                 }

    //                 /* ============================================================
    //            UNITS WITHOUT DIVISION & SECTION
    //         ============================================================ */
    //                 $unitsWithoutDiv = $plantilla
    //                     ->filter(function ($item) {
    //                         return is_null($item->division) && is_null($item->section);
    //                     })
    //                     ->where('group', $groupName)
    //                     ->pluck('unit')
    //                     ->unique()
    //                     ->filter()
    //                     ->values();

    //                 foreach ($unitsWithoutDiv as $u) {
    //                     $unitEmployees = $groupEmployees->filter(function ($emp) use ($u) {
    //                         return is_null($emp->division) && is_null($emp->section) && $emp->unit == $u;
    //                     });
    //                     $groupBlock["units_without_division"][] = [
    //                         "unit" => $u,
    //                         "unitWorkPlan" => $countWorkplan($unitEmployees)
    //                     ];
    //                 }

    //                 $office2Block["group"][] = $groupBlock;
    //             }

    //             $result["office2"][] = $office2Block;
    //         }

    //         return response()->json($result);
    //     }




}


