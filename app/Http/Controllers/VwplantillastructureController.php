<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\vwplantillastructure;

class VwplantillastructureController extends Controller
{

    public function index()
    {
        // Get all distinct offices
        $offices = vwplantillastructure::select('office')
            ->distinct()
            ->orderBy('office')
            ->get();

        // Build hierarchical structure
        $result = [];
        foreach ($offices as $office) {
            $officeData = [
                'office' => $office->office,
                'divisions' => [],
                'sections_without_division' => [],
                'units_without_division' => [],
                'units_without_section' => []
            ];

            // Get all organizational units for this office
            $allUnits = vwplantillastructure::where('office', $office->office)
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

            $result[] = $officeData;
        }

        return response()->json($result);
    }
}
