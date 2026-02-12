<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpmsService
{


    // structure of office plantilla
    public function structure($request)
    {
        $officeId = $request->input('office_id');

        if (!$officeId) return response()->json([]);

        $officeName = DB::table('offices')->where('id', $officeId)->value('name');
        if (!$officeName) return response()->json([]);

        // BASE RESULT STRUCTURE
        $officeData = [
            'officeId' => $officeId,
            'office' => $officeName,
            'office2' => []
        ];

        // GET ALL RECORDS FOR THE OFFICE
        $allunits = DB::table('vwplantillastructure')->where('office', $officeName)
            ->orderBy('office2')
            ->orderBy('group')
            ->orderBy('division')
            ->orderBy('section')
            ->orderBy('unit')
            ->get();

        /* ============================================================
       1. PROCESS OFFICE2
    ============================================================ */

        $office2List = $allunits->unique('office2');

        foreach ($office2List as $office2Row) {

            $office2Name = $office2Row->office2 ?? null;

            $office2Data = [
                'office2' => $office2Name,
                'group' => []
            ];

            // FILTER ALL RECORDS UNDER THIS office2
            $office2units = $allunits->where('office2', $office2Name);

            /* ============================================================
           2. PROCESS group UNDER THIS office2
        ============================================================ */

            $group = $office2units->unique('group');

            foreach ($group as $groupRow) {

                $groupName = $groupRow->group ?? null;

                $groupData = [
                    'group' => $groupName,
                    'divisions' => [],
                    'sections_without_division' => [],
                    'units_without_division' => []
                ];

                // FILTER RECORDS FOR THIS GROUP
                $groupunits = $office2units->where('group', $groupName);

                /* ============================================================
               3. PROCESS divisionS UNDER THIS GROUP
            ============================================================ */
                $divisions = $groupunits->whereNotNull('division')->unique('division');

                foreach ($divisions as $division) {

                    $divisionData = [
                        'division' => $division->division,
                        'sections' => [],
                        'units_without_section' => []
                    ];

                    // sectionS UNDER THIS division
                    $sections = $groupunits
                        ->where('division', $division->division)
                        ->whereNotNull('section')
                        ->unique('section');

                    foreach ($sections as $section) {

                        $sectionData = [
                            'section' => $section->section,
                            'units' => $groupunits
                                ->where('division', $division->division)
                                ->where('section', $section->section)
                                ->whereNotNull('unit')
                                ->pluck('unit')
                                ->unique()
                                ->values()
                                ->toArray()
                        ];

                        $divisionData['sections'][] = $sectionData;
                    }

                    // unitS WITHOUT section
                    $divisionunits = $groupunits
                        ->where('division', $division->division)
                        ->whereNull('section')
                        ->whereNotNull('unit')
                        ->pluck('unit')
                        ->unique()
                        ->values()
                        ->toArray();

                    $divisionData['units_without_section'] = $divisionunits;

                    $groupData['divisions'][] = $divisionData;
                }

                /* ============================================================
               4. sectionS WITHOUT division UNDER THIS GROUP
            ============================================================ */

                $sectionsWithoutdivision = $groupunits
                    ->whereNull('division')
                    ->whereNotNull('section')
                    ->unique('section');

                foreach ($sectionsWithoutdivision as $section) {

                    $sectionData = [
                        'section' => $section->section,
                        'units' => $groupunits
                            ->whereNull('division')
                            ->where('section', $section->section)
                            ->whereNotNull('unit')
                            ->pluck('unit')
                            ->unique()
                            ->values()
                            ->toArray()
                    ];

                    $groupData['sections_without_division'][] = $sectionData;
                }

                // unitS WITHOUT division AND section
                $unitsWithoutdivision = $groupunits
                    ->whereNull('division')
                    ->whereNull('section')
                    ->whereNotNull('unit')
                    ->pluck('unit')
                    ->unique()
                    ->values()
                    ->toArray();

                $groupData['units_without_division'] = $unitsWithoutdivision;

                $office2Data['group'][] = $groupData;
            }

            $officeData['office2'][] = $office2Data;
        }

        return [$officeData];
    }
}
