<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfficeService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    public function structure()
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
            'office2' => []
        ];

        // FETCH ALL RECORDS FOR THIS OFFICE
        $allUnits = DB::table('vwplantillaStructure')
            ->where('office', $officeName)
            ->orderBy('office2')
            ->orderBy('group')
            ->orderBy('division')
            ->orderBy('section')
            ->orderBy('unit')
            ->get();

        // ============================
        // OFFICE2 LEVEL
        // ============================
        foreach ($allUnits->unique('office2') as $office2) {

            $office2Name = $office2->office2;

            $office2Units = $allUnits->where('office2', $office2Name);

            $office2Data = [
                'office2' => $office2Name,
                'group' => []
            ];

            // ============================
            // GROUP LEVEL
            // ============================
            foreach ($office2Units->unique('group') as $grp) {

                $groupName = $grp->group;

                $groupUnits = $office2Units->where('group', $groupName);

                $groupData = [
                    'group' => $groupName,
                    'divisions' => [],
                    'sections_without_division' => [],
                    'units_without_division' => []
                ];

                // ============================
                // DIVISIONS
                // ============================
                foreach ($groupUnits->whereNotNull('division')->unique('division') as $division) {

                    $divisionName = $division->division;

                    $divisionUnits = $groupUnits->where('division', $divisionName);

                    $divisionData = [
                        'division' => $divisionName,
                        'sections' => [],
                        'units_without_section' => []
                    ];

                    // ----- SECTIONS UNDER THIS DIVISION -----
                    foreach ($divisionUnits->whereNotNull('section')->unique('section') as $sec) {

                        $secName = $sec->section;

                        $sectionUnits = $divisionUnits
                            ->where('section', $secName)
                            ->whereNotNull('unit')
                            ->pluck('unit')
                            ->unique()
                            ->values()
                            ->toArray();

                        $divisionData['sections'][] = [
                            'section' => $secName,
                            'units'   => $sectionUnits
                        ];
                    }

                    // ----- UNITS WITHOUT SECTION -----
                    $divisionData['units_without_section'] = $divisionUnits
                        ->whereNull('section')
                        ->whereNotNull('unit')
                        ->pluck('unit')
                        ->unique()
                        ->values()
                        ->toArray();

                    $groupData['divisions'][] = $divisionData;
                }

                // ============================
                // SECTIONS WITHOUT DIVISION
                // ============================
                foreach ($groupUnits->whereNull('division')->whereNotNull('section')->unique('section') as $section) {

                    $secName = $section->section;

                    $sectionUnits = $groupUnits
                        ->whereNull('division')
                        ->where('section', $secName)
                        ->whereNotNull('unit')
                        ->pluck('unit')
                        ->unique()
                        ->values()
                        ->toArray();

                    $groupData['sections_without_division'][] = [
                        'section' => $secName,
                        'units'   => $sectionUnits
                    ];
                }

                // ============================
                // UNITS WITHOUT DIVISION & SECTION
                // ============================
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


        return $officeData;
    }
}
