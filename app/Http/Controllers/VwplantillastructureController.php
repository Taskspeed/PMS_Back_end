<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                'divisions' => []
            ];

            // Get divisions for this office
            $divisions = vwplantillastructure::select('Division')
                ->where('office', $office->office)
                ->whereNotNull('Division')
                ->distinct()
                ->orderBy('Division')
                ->get();

            foreach ($divisions as $division) {
                $divisionData = [
                    'division' => $division->Division,
                    'sections' => []
                ];

                // Get sections for this division
                $sections = vwplantillastructure::select('Section')
                    ->where('office', $office->office)
                    ->where('Division', $division->Division)
                    ->whereNotNull('Section')
                    ->distinct()
                    ->orderBy('Section')
                    ->get();

                foreach ($sections as $section) {
                    $sectionData = [
                        'section' => $section->Section,
                        'units' => []
                    ];

                    // Get units for this section
                    $units = vwplantillastructure::select('Unit')
                        ->where('office', $office->office)
                        ->where('Division', $division->Division)
                        ->where('Section', $section->Section)
                        ->whereNotNull('Unit')
                        ->distinct()
                        ->orderBy('Unit')
                        ->get();

                    foreach ($units as $unit) {
                        $sectionData['units'][] = $unit->Unit;
                    }

                    $divisionData['sections'][] = $sectionData;
                }

                // Get units directly under this division (no section)
                $unitsWithoutSection = vwplantillastructure::select('Unit')
                    ->where('office', $office->office)
                    ->where('Division', $division->Division)
                    ->whereNull('Section')
                    ->whereNotNull('Unit')
                    ->distinct()
                    ->orderBy('Unit')
                    ->get();

                // Add units directly to division if they exist
                if ($unitsWithoutSection->count() > 0) {
                    $divisionData['units'] = [];
                    foreach ($unitsWithoutSection as $unit) {
                        $divisionData['units'][] = $unit->Unit;
                    }
                }

                $officeData['divisions'][] = $divisionData;
            }

            // Get sections without division
            $sectionsWithoutDivision = vwplantillastructure::select('Section')
                ->where('office', $office->office)
                ->whereNull('Division')
                ->whereNotNull('Section')
                ->distinct()
                ->orderBy('Section')
                ->get();

            if ($sectionsWithoutDivision->count() > 0) {
                $officeData['sections_without_division'] = [];
                foreach ($sectionsWithoutDivision as $section) {
                    $officeData['sections_without_division'][] = $section->Section;
                }
            }

            $result[] = $officeData;
        }

        return response()->json($result);
    }

}
