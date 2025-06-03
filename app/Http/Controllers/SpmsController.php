<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\vwplantillastructure;

class SpmsController extends Controller
{
    //


    public function Spms_index(Request $request)
    {
        // Get office_id from request
        $officeId = $request->input('office_id');

        // If no office_id provided, return empty array or all offices based on your requirements
        if (!$officeId) {
            return response()->json([]);
        }

        // First get the office name corresponding to the office_id
        $officeName = DB::table('offices')->where('id', $officeId)->value('name');

        if (!$officeName) {
            return response()->json([]);
        }

        // Now query the view for just this office
        $officeData = [
            'office' => $officeName,
            'divisions' => [],
            'sections_without_division' => [],
            'units_without_division' => [],
            'units_without_section' => []
        ];

        // Get all organizational units for this office
        $allUnits = vwplantillastructure::where('office', $officeName)
            ->orderBy('Division')
            ->orderBy('Section')
            ->orderBy('Unit')
            ->get();

        // Process divisions
        $divisions = $allUnits->whereNotNull('Division')->unique('Division');
        foreach ($divisions as $division) {
            $divisionData = [
                'division' => $division->Division,
                'sections' => [],
                'units_without_section' => []
            ];

            // Process sections within this division
            $sections = $allUnits->where('Division', $division->Division)
                ->whereNotNull('Section')
                ->unique('Section');

            foreach ($sections as $section) {
                $sectionData = [
                    'section' => $section->Section,
                    'units' => $allUnits->where('Division', $division->Division)
                        ->where('Section', $section->Section)
                        ->whereNotNull('Unit')
                        ->pluck('Unit')
                        ->unique()
                        ->values()
                        ->toArray()
                ];
                $divisionData['sections'][] = $sectionData;
            }

            // Process units directly under division (no section)
            $divisionUnits = $allUnits->where('Division', $division->Division)
                ->whereNull('Section')
                ->whereNotNull('Unit')
                ->pluck('Unit')
                ->unique()
                ->values()
                ->toArray();

            if (!empty($divisionUnits)) {
                $divisionData['units_without_section'] = $divisionUnits;
            }

            $officeData['divisions'][] = $divisionData;
        }

        // Process sections without division
        $sectionsWithoutDivision = $allUnits->whereNull('Division')
            ->whereNotNull('Section')
            ->unique('Section');

        foreach ($sectionsWithoutDivision as $section) {
            $sectionData = [
                'section' => $section->Section,
                'units' => $allUnits->whereNull('Division')
                    ->where('Section', $section->Section)
                    ->whereNotNull('Unit')
                    ->pluck('Unit')
                    ->unique()
                    ->values()
                    ->toArray()
            ];
            $officeData['sections_without_division'][] = $sectionData;
        }

        // Process units without division or section
        $unitsWithoutDivision = $allUnits->whereNull('Division')
            ->whereNull('Section')
            ->whereNotNull('Unit')
            ->pluck('Unit')
            ->unique()
            ->values()
            ->toArray();

        if (!empty($unitsWithoutDivision)) {
            $officeData['units_without_division'] = $unitsWithoutDivision;
        }

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
