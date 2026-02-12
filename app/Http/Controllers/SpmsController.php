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

    public function getTargetPeriodsSemesterYear() // geting the year and semester
    {
        $targetPeriods = TargetPeriod::select('semester', 'year')
            ->groupBy('semester', 'year')
            ->orderBy('year', 'desc')
            ->get();

        return response()->json($targetPeriods);
    }


    public function getEmployeeCountAndUnitworkplan(Request $request)
    {
        // $user = Auth::user();
        // $officeId = $user->office_id;

        if (!$this->officeId) {
            return response()->json([
                'success' => false,
                'message' => 'Office ID is required'
            ], 400);
        }

        // 1. Get office name
        $officeName = DB::table('offices')->where('id', $this->officeId)->value('name');
        if (!$officeName) {
            return response()->json([
                'success' => false,
                'message' => 'Office not found'
            ], 404);
        }

        // 2. Fetch plantilla structure records
        $plantilla = DB::table('vwplantillastructure')
            ->where('office', $officeName)
            ->orderBy('office2')
            ->orderBy('group')
            ->orderBy('division')
            ->orderBy('section')
            ->orderBy('unit')
            ->get();

        // 3. Helper count function
        $countWorkplan = function ($query) {
            $total = $query->count();
            $with = $query->filter(function ($emp) {
                return TargetPeriod::where('control_no', $emp->ControlNo)->exists();
            })->count();

            return "$with/$total";
        };

        // 4. Preload all employees of this office (1 query only)
        $employees = Employee::where('office_id', $this->officeId)
            ->select('ControlNo', 'office2', 'group', 'division', 'section', 'unit')
            ->get();

        // 5. Start final output - FIX: Use filter instead of where for null checks
        $result = [
            "office" => [
                "name" => $officeName,
                "unitWorkPlan" => $countWorkplan($employees->filter(function ($e) {
                    return is_null($e->division) && is_null($e->section) && is_null($e->unit);
                }))
            ],
            "office2" => []
        ];

        /* ============================================================
   LOOP OFFICE2
============================================================ */
        foreach ($plantilla->unique('office2') as $o2) {

            $office2Name = $o2->office2;
            $office2Employees = $employees->where('office2', $office2Name);

            $office2Block = [
                "office2" => $office2Name,
                "unitWorkPlan" => $countWorkplan($office2Employees),
                "group" => []
            ];

            /* ============================================================
       LOOP GROUP
    ============================================================ */
            foreach ($plantilla->where('office2', $office2Name)->unique('group') as $grp) {

                $groupName = $grp->group;
                $groupEmployees = $office2Employees->where('group', $groupName);

                $groupBlock = [
                    "group" => $groupName,
                    "unitWorkPlan" => $countWorkplan($groupEmployees),
                    "divisions" => [],
                    "sections_without_division" => [],
                    "units_without_division" => []
                ];

                /* ============================================================
           DIVISIONS
        ============================================================ */
                $divisions = $plantilla
                    ->where('office2', $office2Name)
                    ->where('group', $groupName)
                    ->whereNotNull('division')
                    ->unique('division');

                foreach ($divisions as $div) {
                    $divisionName = $div->division;
                    $divisionEmployees = $groupEmployees->where('division', $divisionName);

                    $divisionBlock = [
                        "division" => $divisionName,
                        "unitWorkPlan" => $countWorkplan($divisionEmployees),
                        "sections" => [],
                        "units_without_section" => []
                    ];

                    // Sections under this division
                    $sections = $plantilla
                        ->where('division', $divisionName)
                        ->whereNotNull('section')
                        ->unique('section');

                    foreach ($sections as $sec) {
                        $sectionName = $sec->section;
                        $sectionEmployees = $divisionEmployees->where('section', $sectionName);

                        $sectionBlock = [
                            "section" => $sectionName,
                            "unitWorkPlan" => $countWorkplan($sectionEmployees),
                            "units" => []
                        ];

                        // Units under this section
                        $units = $plantilla
                            ->where('division', $divisionName)
                            ->where('section', $sectionName)
                            ->pluck('unit')
                            ->unique()
                            ->filter()
                            ->values();

                        foreach ($units as $unitName) {
                            $unitEmployees = $sectionEmployees->where('unit', $unitName);

                            $sectionBlock["units"][] = [
                                "unit" => $unitName,
                                "unitWorkPlan" => $countWorkplan($unitEmployees)
                            ];
                        }

                        $divisionBlock["sections"][] = $sectionBlock;
                    }

                    // Units without section - FIX: Use filter for null checks
                    $unitsWithoutSection = $plantilla
                        ->where('division', $divisionName)
                        ->filter(function ($item) {
                            return is_null($item->section);
                        })
                        ->pluck('unit')
                        ->unique()
                        ->filter()
                        ->values();

                    foreach ($unitsWithoutSection as $u) {
                        $unitEmployees = $divisionEmployees->filter(function ($emp) use ($u) {
                            return is_null($emp->section) && $emp->unit == $u;
                        });
                        $divisionBlock["units_without_section"][] = [
                            "unit" => $u,
                            "unitWorkPlan" => $countWorkplan($unitEmployees)
                        ];
                    }

                    $groupBlock["divisions"][] = $divisionBlock;
                }

                /* ============================================================
           SECTIONS WITHOUT DIVISION
        ============================================================ */
                $sectionsWithoutDivision = $plantilla
                    ->filter(function ($item) {
                        return is_null($item->division);
                    })
                    ->whereNotNull('section')
                    ->where('group', $groupName)
                    ->where('office2', $office2Name)
                    ->unique('section');

                foreach ($sectionsWithoutDivision as $sec) {

                    $sectionName = $sec->section;
                    // FIX: Use filter for null checks on collections
                    $sectionEmployees = $groupEmployees->filter(function ($emp) use ($sectionName) {
                        return is_null($emp->division) && $emp->section == $sectionName;
                    });

                    $sectionBlock = [
                        "section" => $sectionName,
                        "unitWorkPlan" => $countWorkplan($sectionEmployees),
                        "units" => []
                    ];

                    $units = $plantilla
                        ->where('section', $sectionName)
                        ->filter(function ($item) {
                            return is_null($item->division);
                        })
                        ->pluck('unit')
                        ->unique()
                        ->filter()
                        ->values();

                    foreach ($units as $u) {
                        $unitEmployees = $sectionEmployees->where('unit', $u);
                        $sectionBlock["units"][] = [
                            "unit" => $u,
                            "unitWorkPlan" => $countWorkplan($unitEmployees)
                        ];
                    }

                    $groupBlock["sections_without_division"][] = $sectionBlock;
                }

                /* ============================================================
           UNITS WITHOUT DIVISION & SECTION
        ============================================================ */
                $unitsWithoutDiv = $plantilla
                    ->filter(function ($item) {
                        return is_null($item->division) && is_null($item->section);
                    })
                    ->where('group', $groupName)
                    ->pluck('unit')
                    ->unique()
                    ->filter()
                    ->values();

                foreach ($unitsWithoutDiv as $u) {
                    $unitEmployees = $groupEmployees->filter(function ($emp) use ($u) {
                        return is_null($emp->division) && is_null($emp->section) && $emp->unit == $u;
                    });
                    $groupBlock["units_without_division"][] = [
                        "unit" => $u,
                        "unitWorkPlan" => $countWorkplan($unitEmployees)
                    ];
                }

                $office2Block["group"][] = $groupBlock;
            }

            $result["office2"][] = $office2Block;
        }

        return response()->json($result);
    }

}
