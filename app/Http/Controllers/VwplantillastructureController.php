<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class VwplantillastructureController extends Controller
{
    // office structure for employee
    // public function plantillaStructureEmployee()
    // {
    //     // Get all distinct offices
    //     $offices = vwplantillastructure::select('office')
    //         ->distinct()
    //         ->orderBy('office')
    //         ->get();

    //     $result = [];

    //     foreach ($offices as $office) {
    //         $officeData = [
    //             'office' => $office->office,
    //             'office2' => []
    //         ];

    //         // Get all units for this office
    //         $allUnits = vwplantillastructure::where('office', $office->office)
    //             ->orderBy('office2')
    //             ->orderBy('group')
    //             ->orderBy('Division')
    //             ->orderBy('Section')
    //             ->orderBy('Unit')
    //             ->get();

    //         // Process office2
    //         $office2List = $allUnits->unique('office2');
    //         foreach ($office2List as $office2Row) {
    //             $office2Name = $office2Row->office2 ?? null;

    //             $office2Data = [
    //                 'office2' => $office2Name,
    //                 'group' => []
    //             ];

    //             // Filter units for this office2
    //             $office2Units = $allUnits->where('office2', $office2Name);

    //             // Process group under this office2
    //             $group = $office2Units->unique('group');
    //             foreach ($group as $groupRow) {
    //                 $groupName = $groupRow->group ?? null;

    //                 $groupData = [
    //                     'group' => $groupName,
    //                     'divisions' => [],
    //                     'sections_without_division' => [],
    //                     'units_without_division' => []
    //                 ];

    //                 // Filter units for this group
    //                 $groupUnits = $office2Units->where('group', $groupName);

    //                 // Process divisions under group
    //                 $divisions = $groupUnits->whereNotNull('Division')->unique('Division');
    //                 foreach ($divisions as $division) {
    //                     $divisionData = [
    //                         'division' => $division->Division,
    //                         'sections' => [],
    //                         'units_without_section' => []
    //                     ];

    //                     // Sections under this division
    //                     $sections = $groupUnits
    //                         ->where('Division', $division->Division)
    //                         ->whereNotNull('Section')
    //                         ->unique('Section');

    //                     foreach ($sections as $section) {
    //                         $sectionData = [
    //                             'section' => $section->Section,
    //                             'units' => $groupUnits
    //                                 ->where('Division', $division->Division)
    //                                 ->where('Section', $section->Section)
    //                                 ->whereNotNull('Unit')
    //                                 ->pluck('Unit')
    //                                 ->unique()
    //                                 ->values()
    //                                 ->toArray()
    //                         ];

    //                         $divisionData['sections'][] = $sectionData;
    //                     }

    //                     // Units directly under division (no section)
    //                     $divisionUnits = $groupUnits
    //                         ->where('Division', $division->Division)
    //                         ->whereNull('Section')
    //                         ->whereNotNull('Unit')
    //                         ->pluck('Unit')
    //                         ->unique()
    //                         ->values()
    //                         ->toArray();

    //                     $divisionData['units_without_section'] = $divisionUnits;

    //                     $groupData['divisions'][] = $divisionData;
    //                 }

    //                 // Sections without division
    //                 $sectionsWithoutDivision = $groupUnits
    //                     ->whereNull('Division')
    //                     ->whereNotNull('Section')
    //                     ->unique('Section');

    //                 foreach ($sectionsWithoutDivision as $section) {
    //                     $sectionData = [
    //                         'section' => $section->Section,
    //                         'units' => $groupUnits
    //                             ->whereNull('Division')
    //                             ->where('Section', $section->Section)
    //                             ->whereNotNull('Unit')
    //                             ->pluck('Unit')
    //                             ->unique()
    //                             ->values()
    //                             ->toArray()
    //                     ];
    //                     $groupData['sections_without_division'][] = $sectionData;
    //                 }

    //                 // Units without division and section
    //                 $unitsWithoutDivision = $groupUnits
    //                     ->whereNull('Division')
    //                     ->whereNull('Section')
    //                     ->whereNotNull('Unit')
    //                     ->pluck('Unit')
    //                     ->unique()
    //                     ->values()
    //                     ->toArray();

    //                 $groupData['units_without_division'] = $unitsWithoutDivision;

    //                 $office2Data['group'][] = $groupData;
    //             }

    //             $officeData['office2'][] = $office2Data;
    //         }

    //         $result[] = $officeData;
    //     }

    //     return response()->json($result);
    // }



public function plantillaStructureEmployeeWithCount(Request $request)
    {
        $user = Auth::user();

        if (!$user?->office_id) {
            return response()->json([]);
        }

        $officeName = DB::table('offices')
            ->where('id', $user->office_id)
            ->value('name');

        if (!$officeName) {
            return response()->json([]);
        }

        // BASE STRUCTURE
        $officeData = [
            'office' => $officeName,
            'employee_count' => 0,
            'office2' => []
        ];

        // FETCH ALL RECORDS FOR THIS OFFICE
        $allUnits = DB::table('vwplantillastructure')
            ->where('office', $officeName)
            ->orderBy('office2')
            ->orderBy('group')
            ->orderBy('division')
            ->orderBy('section')
            ->orderBy('unit')
            ->get();

        foreach ($allUnits->unique('office2') as $office2) {
            $office2Name = $office2->office2;
            $office2Units = $allUnits->where('office2', $office2Name);

            $office2Data = [
                'office2' => $office2Name,
                'employee_count' => $office2Units->count(),
                'group' => []
            ];

            foreach ($office2Units->unique('group') as $grp) {
                $groupName = $grp->group;
                $groupUnits = $office2Units->where('group', $groupName);

                $groupData = [
                    'group' => $groupName,
                    'employee_count' => $groupUnits->count(),
                    'divisions' => [],
                    'sections_without_division' => [],
                    'units_without_division' => []
                ];

                // Divisions
                foreach ($groupUnits->whereNotNull('division')->unique('division') as $division) {
                    $divisionName = $division->division;
                    $divisionUnits = $groupUnits->where('division', $divisionName);

                    $divisionData = [
                        'division' => $divisionName,
                        'employee_count' => $divisionUnits->count(),
                        'sections' => [],
                        'units_without_section' => []
                    ];

                    // Sections under this division
                    foreach ($divisionUnits->whereNotNull('section')->unique('section') as $sec) {
                        $secName = $sec->section;
                        $sectionUnits = $divisionUnits
                            ->where('section', $secName)
                            ->whereNotNull('unit');

                        $divisionData['sections'][] = [
                            'section' => $secName,
                            'employee_count' => $sectionUnits->count(),
                            'units' => $sectionUnits->pluck('unit')->unique()->values()->toArray()
                        ];
                    }

                    // Units without section
                    $divisionData['units_without_section'] = $divisionUnits
                        ->whereNull('section')
                        ->whereNotNull('unit')
                        ->pluck('unit')
                        ->unique()
                        ->values()
                        ->toArray();

                    $groupData['divisions'][] = $divisionData;
                }

                // Sections without division
                foreach ($groupUnits->whereNull('division')->whereNotNull('section')->unique('section') as $section) {
                    $secName = $section->section;
                    $sectionUnits = $groupUnits
                        ->whereNull('division')
                        ->where('section', $secName)
                        ->whereNotNull('unit');

                    $groupData['sections_without_division'][] = [
                        'section' => $secName,
                        'employee_count' => $sectionUnits->count(),
                        'units' => $sectionUnits->pluck('unit')->unique()->values()->toArray()
                    ];
                }

                // Units without division & section
                $groupData['units_without_division'] = $groupUnits
                    ->whereNull('division')
                    ->whereNull('section')
                    ->whereNotNull('unit')
                    ->pluck('unit')
                    ->unique()
                    ->values()
                    ->toArray();

                $office2Data['group'][] = $groupData;
            }

            $officeData['office2'][] = $office2Data;
        }

        // Calculate total employee count for the office
        $officeData['employee_count'] = $allUnits->count();

        return response()->json([$officeData]);
    }




    public function fetchEmployees(Request $request)
    {
        $query = Employee::with('position');

        // Filter by office_id if provided
        if ($request->has('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        // Filter by organizational unit if provided
        if ($request->has('division')) {
            $query->where('division', $request->division);
        }

        if ($request->has('section')) {
            $query->where('section', $request->section);
        }

        if ($request->has('unit')) {
            $query->where('unit', $request->unit);
        }

        $employees = $query->get()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'position_id' => $employee->position_id,
                'position' => $employee->position ? $employee->position->name : null,
                'office_id' => $employee->office_id,
                'office' => $employee->office,
                'division' => $employee->division,
                'section' => $employee->section,
                'unit' => $employee->unit,
                'rank' => $employee->rank
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }
}
