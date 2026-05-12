<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StructureService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    
// =====================
// ✅ FIXED: accepts $office_name string directly, no Request
// =====================
    public function structure(string $office_name): array
{
    if (!$office_name) return [];

    $officeName = DB::table('offices')->where('name', $office_name)->first();
    if (!$officeName) return [];

    $officeData = [
        'office'  => $officeName,
        'office2' => []
    ];

    $allunits = DB::table('vwplantillastructure')
        ->where('office', $office_name) // ✅ use the string, not the object
        ->orderBy('office2')
        ->orderBy('group')
        ->orderBy('division')
        ->orderBy('section')
        ->orderBy('unit')
        ->get();

    $office2List = $allunits->unique('office2');

    foreach ($office2List as $office2Row) {
        $office2Name = $office2Row->office2 ?? null;

        $office2Data = [
            'office2' => $office2Name,
            'group'   => []
        ];

        $office2units = $allunits->where('office2', $office2Name);
        $group        = $office2units->unique('group');

        foreach ($group as $groupRow) {
            $groupName = $groupRow->group ?? null;

            $groupData = [
                'group'                      => $groupName,
                'divisions'                  => [],
                'sections_without_division'  => [],
                'units_without_division'     => []
            ];

            $groupunits = $office2units->where('group', $groupName);
            $divisions  = $groupunits->whereNotNull('division')->unique('division');

            foreach ($divisions as $division) {
                $divisionData = [
                    'division'             => $division->division,
                    'sections'             => [],
                    'units_without_section'=> []
                ];

                $sections = $groupunits
                    ->where('division', $division->division)
                    ->whereNotNull('section')
                    ->unique('section');

                foreach ($sections as $section) {
                    $divisionData['sections'][] = [
                        'section' => $section->section,
                        'units'   => $groupunits
                            ->where('division', $division->division)
                            ->where('section', $section->section)
                            ->whereNotNull('unit')
                            ->pluck('unit')
                            ->unique()
                            ->values()
                            ->toArray()
                    ];
                }

                $divisionData['units_without_section'] = $groupunits
                    ->where('division', $division->division)
                    ->whereNull('section')
                    ->whereNotNull('unit')
                    ->pluck('unit')
                    ->unique()
                    ->values()
                    ->toArray();

                $groupData['divisions'][] = $divisionData;
            }

            $sectionsWithoutDivision = $groupunits
                ->whereNull('division')
                ->whereNotNull('section')
                ->unique('section');

            foreach ($sectionsWithoutDivision as $section) {
                $groupData['sections_without_division'][] = [
                    'section' => $section->section,
                    'units'   => $groupunits
                        ->whereNull('division')
                        ->where('section', $section->section)
                        ->whereNotNull('unit')
                        ->pluck('unit')
                        ->unique()
                        ->values()
                        ->toArray()
                ];
            }

            $groupData['units_without_division'] = $groupunits
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

    return $officeData; // ✅ return array, not wrapped in extra array
}
}
