<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\vwplantillastructure;
use App\Services\SpmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class SpmsController extends BaseController
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


    // // this structure
    // public function plantillaStructureSpms(Request $request)
    // {
    //     $officeId = $request->input('office_id');

    //     if (!$officeId) return response()->json([]);

    //     $officeName = DB::table('offices')->where('id', $officeId)->value('name');
    //     if (!$officeName) return response()->json([]);

    //     // BASE RESULT STRUCTURE
    //     $officeData = [
    //         'officeId' => $officeId,
    //         'office' => $officeName,
    //         'office2' => []
    //     ];

    //     // GET ALL RECORDS FOR THE OFFICE
    //     $allunits = DB::table('vwplantillastructure')->where('office', $officeName)
    //         ->orderBy('office2')
    //         ->orderBy('group')
    //         ->orderBy('division')
    //         ->orderBy('section')
    //         ->orderBy('unit')
    //         ->get();

    //     /* ============================================================
    //    1. PROCESS OFFICE2
    // ============================================================ */

    //     $office2List = $allunits->unique('office2');

    //     foreach ($office2List as $office2Row) {

    //         $office2Name = $office2Row->office2 ?? null;

    //         $office2Data = [
    //             'office2' => $office2Name,
    //             'group' => []
    //         ];

    //         // FILTER ALL RECORDS UNDER THIS office2
    //         $office2units = $allunits->where('office2', $office2Name);

    //         /* ============================================================
    //        2. PROCESS group UNDER THIS office2
    //     ============================================================ */

    //         $group = $office2units->unique('group');

    //         foreach ($group as $groupRow) {

    //             $groupName = $groupRow->group ?? null;

    //             $groupData = [
    //                 'group' => $groupName,
    //                 'divisions' => [],
    //                 'sections_without_division' => [],
    //                 'units_without_division' => []
    //             ];

    //             // FILTER RECORDS FOR THIS GROUP
    //             $groupunits = $office2units->where('group', $groupName);

    //             /* ============================================================
    //            3. PROCESS divisionS UNDER THIS GROUP
    //         ============================================================ */
    //             $divisions = $groupunits->whereNotNull('division')->unique('division');

    //             foreach ($divisions as $division) {

    //                 $divisionData = [
    //                     'division' => $division->division,
    //                     'sections' => [],
    //                     'units_without_section' => []
    //                 ];

    //                 // sectionS UNDER THIS division
    //                 $sections = $groupunits
    //                     ->where('division', $division->division)
    //                     ->whereNotNull('section')
    //                     ->unique('section');

    //                 foreach ($sections as $section) {

    //                     $sectionData = [
    //                         'section' => $section->section,
    //                         'units' => $groupunits
    //                             ->where('division', $division->division)
    //                             ->where('section', $section->section)
    //                             ->whereNotNull('unit')
    //                             ->pluck('unit')
    //                             ->unique()
    //                             ->values()
    //                             ->toArray()
    //                     ];

    //                     $divisionData['sections'][] = $sectionData;
    //                 }

    //                 // unitS WITHOUT section
    //                 $divisionunits = $groupunits
    //                     ->where('division', $division->division)
    //                     ->whereNull('section')
    //                     ->whereNotNull('unit')
    //                     ->pluck('unit')
    //                     ->unique()
    //                     ->values()
    //                     ->toArray();

    //                 $divisionData['units_without_section'] = $divisionunits;

    //                 $groupData['divisions'][] = $divisionData;
    //             }

    //             /* ============================================================
    //            4. sectionS WITHOUT division UNDER THIS GROUP
    //         ============================================================ */

    //             $sectionsWithoutdivision = $groupunits
    //                 ->whereNull('division')
    //                 ->whereNotNull('section')
    //                 ->unique('section');

    //             foreach ($sectionsWithoutdivision as $section) {

    //                 $sectionData = [
    //                     'section' => $section->section,
    //                     'units' => $groupunits
    //                         ->whereNull('division')
    //                         ->where('section', $section->section)
    //                         ->whereNotNull('unit')
    //                         ->pluck('unit')
    //                         ->unique()
    //                         ->values()
    //                         ->toArray()
    //                 ];

    //                 $groupData['sections_without_division'][] = $sectionData;
    //             }

    //             // unitS WITHOUT division AND section
    //             $unitsWithoutdivision = $groupunits
    //                 ->whereNull('division')
    //                 ->whereNull('section')
    //                 ->whereNotNull('unit')
    //                 ->pluck('unit')
    //                 ->unique()
    //                 ->values()
    //                 ->toArray();

    //             $groupData['units_without_division'] = $unitsWithoutdivision;

    //             $office2Data['group'][] = $groupData;
    //         }

    //         $officeData['office2'][] = $office2Data;
    //     }

    //     return response()->json([$officeData]);
    // }


    // plantilla structure of office
    public function officePlantilla(SpmsService $structure, Request $request)
    {
        $plantilla = $structure->structure($request);

        return response()->json($plantilla);
    }


    public function fetchEmployees(Request $request) //employee with target peroid
    {

        // Get semester & year from request
        $semester = $request->input('semester');   // example: January-June / July-December
        $year = $request->input('year');           // example: 2025

        if (!$semester || !$year) {
            return response()->json([
                'message' => 'Please provide semester and year'
            ], 422);
        }

        $employees = Employee::where('office_id', $this->officeId)
            ->get()
            ->map(function ($emp) use ($semester, $year) {

                // Look for target period based on user request
                $existing = $emp->targetPeriods()
                    ->where('semester', $semester)
                    ->where('year', $year)
                    ->first();

                $emp->has_target_period = $existing ? true : false;
                $emp->existing_target_period = $existing;

                // Remove auto-loaded relation if exists
                unset($emp->target_periods);

                return $emp;
            });

        return response()->json($employees);
    }

    // fetch the employee  base on the office and  the target peroid of the employee
    public function getEmployees(Request $request, SpmsService $employee)
    {
        $employees = $employee->employees($request);

        return response()->json($employees);
    }


    // fetch the employee  base on the office and  the target peroid of the employee
    public function getEmployeeRequested(Request $request, SpmsService $employee)
    {

        $employees = $employee->employeesRequest($request);

        return response()->json($employees);
    }

    public function getTargetPeriodsSemesterYear() // geting the year and semester
    {
        $targetPeriods = TargetPeriod::select('semester', 'year')
            ->groupBy('semester', 'year')
            ->orderBy('year', 'desc')
            ->get();

        return response()->json($targetPeriods);
    }


    //     public function getEmployeeCountAndUnitworkplan(Request $request)
    //     {
    //         // $user = Auth::user();
    //         // $officeId = $user->office_id;

    //         if (!$this->officeId) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Office ID is required'
    //             ], 400);
    //         }

    //         // 1. Get office name
    //         $officeName = DB::table('offices')->where('id', $this->officeId)->value('name');
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
    //         $employees = Employee::where('office_id', $this->officeId)
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


    // get the employee base on the employee



    public function getStructureOffice()
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        if (!$officeId) return response()->json([]);

        $officeName = DB::table('offices')->where('id', $officeId)->value('name');
        if (!$officeName) return response()->json([]);

        $rows = DB::table('employees as e')
            ->where('e.office', $officeName)
            ->select(
                'e.ControlNo',
                'e.ItemNo',
                'e.office',
                'e.office2',
                'e.group',
                'e.division',
                'e.section',
                'e.unit',
                'e.name',
                'e.status',
                'e.position',

            )
            ->orderBy('e.office2')
            ->orderBy('e.group')
            ->orderBy('e.division')
            ->orderBy('e.section')
            ->orderBy('e.unit')
            ->orderBy('e.ItemNo')
            ->get();

        if ($rows->isEmpty()) return response()->json([]);

        $result = [];
        $officeGroups = $rows->groupBy('office');

        foreach ($officeGroups as $officeName => $officeRows) {
            $officeData = [
                'office'    => $officeName,
                'employees' => [],
                'office2'   => []
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

                    foreach ($remainingGroupRows->groupBy('division') as $divisionName => $divisionRows) {
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

                        foreach ($remainingDivisionRows->groupBy('section') as $sectionName => $sectionRows) {
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

                            foreach ($remainingSectionRows->groupBy('unit') as $unitName => $unitRows) {
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

        return response()->json($result);
    }

    private function mapEmployee($row)
    {
        return [
            'controlNo' => $row->ControlNo,
            'name'      => $row->name,
            'status' => $row->status,
            'position' => $row->position
        ];
    }

    public function getEmployeeUnderOfHead(Request $request)
    {
        $user = Auth::user();
        $controlNo = $user->control_no;

        if (!$controlNo) return response()->json([]);

        // Get the full office structure
        $structure = $this->getStructureOffice();
        $structureData = json_decode($structure->getContent(), true);

        // Search through the structure to find where this controlNo belongs
        $employees = $this->findEmployeesSameNode($structureData, $controlNo);

        return response()->json($employees);
    }

    private function findEmployeesSameNode(array $structure, string $controlNo): array
    {
        foreach ($structure as $officeData) {

            // Check office-level employees
            $found = $this->controlNoExistsIn($officeData['employees'], $controlNo);
            if ($found) {
                return $this->excludeSelf($officeData['employees'], $controlNo);
            }

            foreach ($officeData['office2'] as $office2Data) {

                // Check office2-level employees
                $found = $this->controlNoExistsIn($office2Data['employees'], $controlNo);
                if ($found) {
                    return $this->excludeSelf($office2Data['employees'], $controlNo);
                }

                foreach ($office2Data['groups'] as $groupData) {

                    // Check group-level employees
                    $found = $this->controlNoExistsIn($groupData['employees'], $controlNo);
                    if ($found) {
                        return $this->excludeSelf($groupData['employees'], $controlNo);
                    }

                    foreach ($groupData['divisions'] as $divisionData) {

                        // Check division-level employees
                        $found = $this->controlNoExistsIn($divisionData['employees'], $controlNo);
                        if ($found) {
                            return $this->excludeSelf($divisionData['employees'], $controlNo);
                        }

                        foreach ($divisionData['sections'] as $sectionData) {

                            // Check section-level employees
                            $found = $this->controlNoExistsIn($sectionData['employees'], $controlNo);
                            if ($found) {
                                return $this->excludeSelf($sectionData['employees'], $controlNo);
                            }

                            foreach ($sectionData['units'] as $unitData) {

                                // Check unit-level employees
                                $found = $this->controlNoExistsIn($unitData['employees'], $controlNo);
                                if ($found) {
                                    return $this->excludeSelf($unitData['employees'], $controlNo);
                                }
                            }
                        }
                    }
                }
            }
        }

        return [];
    }

    private function controlNoExistsIn(array $employees, string $controlNo): bool
    {
        return collect($employees)->contains('controlNo', $controlNo);
    }

    private function excludeSelf(array $employees, string $controlNo): array
    {
        return collect($employees)
            ->reject(fn($e) => $e['controlNo'] === $controlNo)
            ->filter(fn($e) => in_array(strtoupper($e['status']), ['CASUAL', 'HONORARIUM', 'CONTRACTUAL', 'JOB ORDER']))
            ->values()
            ->all();
    }
}
