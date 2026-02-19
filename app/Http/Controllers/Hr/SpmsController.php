<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SpmsController extends Controller
{
    //

    //
    // public function getOfficePlantilla(Request $request)
    // {

    //     $request->validate([
    //         'officeId' => 'required|integer|exists:offices,id',
    //         // 'office_name' => 'required',
    //         // 'organization' => 'required',
    //         'year' => 'required',
    //         'semester' => 'required'
    //     ]);


    //     $rows = DB::table('employees as e')
    //         ->leftJoin('vwActive as a', 'a.ControlNo', '=', 'e.ControlNo')
    //         ->leftJoin('offices as o', 'o.Office', '=', 'e.office') // or actual related column

    //         // ->leftJoin('offices as o', 'o.name', '=', 'e.office')

    //         ->select(
    //             'e.*',
    //             'a.Birthdate as birthdate',
    //             'a.Surname as lastname',
    //             'a.Firstname as firstname',
    //             'a.MIddlename as middlename',
    //             'e.rank',
    //             'e.ControlNo as controlNo',
    //             'o.id as office_id'   // ✅ FETCH OFFICE ID


    //         )
    //         ->where('o.id', $request->officeId) // Filter here
    //         ->orderBy('e.office2')
    //         ->orderBy('e.group')
    //         ->orderBy('e.division')
    //         ->orderBy('e.section')
    //         ->orderBy('e.unit')
    //         // ->orderBy('p.itemNo')
    //         ->get();


    //     if ($rows->isEmpty()) {
    //         return response()->json([]);
    //     }


    //     $result = [];

    //     foreach ($rows->groupBy('office') as $officeName => $officeRows) {

    //         // $officeLevel = $officeRows->first()->level;
    //         $officeId = $officeRows->first()->office_id; // ✅ HERE
    //         $officeData = [
    //             'office_id' => (int) $officeId,   // ✅ Correct integer cast
    //             // 'office_name'      => $officeName,
    //             // 'level'       => $officeLevel,
    //             'employees'   => [],
    //             'office2'     => []
    //         ];

    //         $officeEmployees = $officeRows->filter(
    //             fn($r) =>
    //             is_null($r->office2) &&
    //                 is_null($r->group) &&
    //                 is_null($r->division) &&
    //                 is_null($r->section) &&
    //                 is_null($r->unit)
    //         );
    //         $officeData['employees'] = $officeEmployees
    //             ->sortBy('ItemNo')
    //             // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //             ->map(fn($r) => $this->mapEmployee($r))

    //             ->values();

    //         $remainingOfficeRows = $officeRows->reject(
    //             fn($r) =>
    //             is_null($r->office2) &&
    //                 is_null($r->group) &&
    //                 is_null($r->division) &&
    //                 is_null($r->section) &&
    //                 is_null($r->unit)
    //         );

    //         foreach ($remainingOfficeRows->groupBy('office2') as $office2Name => $office2Rows) {
    //             $office2Data = [
    //                 'office2'   => $office2Name,
    //                 'employees' => [],
    //                 'groups'    => []
    //             ];

    //             $office2Employees = $office2Rows->filter(
    //                 fn($r) =>
    //                 is_null($r->group) &&
    //                     is_null($r->division) &&
    //                     is_null($r->section) &&
    //                     is_null($r->unit)
    //             );
    //             $office2Data['employees'] = $office2Employees
    //                 ->sortBy('ItemNo')
    //                 // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                 ->map(fn($r) => $this->mapEmployee($r))

    //                 ->values();

    //             $remainingOffice2Rows = $office2Rows->reject(
    //                 fn($r) =>
    //                 is_null($r->group) &&
    //                     is_null($r->division) &&
    //                     is_null($r->section) &&
    //                     is_null($r->unit)
    //             );

    //             foreach ($remainingOffice2Rows->groupBy('group') as $groupName => $groupRows) {
    //                 $groupData = [
    //                     'group'     => $groupName,
    //                     'employees' => [],
    //                     'divisions' => []
    //                 ];

    //                 $groupEmployees = $groupRows->filter(
    //                     fn($r) =>
    //                     is_null($r->division) &&
    //                         is_null($r->section) &&
    //                         is_null($r->unit)
    //                 );
    //                 $groupData['employees'] = $groupEmployees
    //                     ->sortBy('ItemNo')
    //                     // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                     ->map(fn($r) => $this->mapEmployee($r))

    //                     ->values();

    //                 $remainingGroupRows = $groupRows->reject(
    //                     fn($r) =>
    //                     is_null($r->division) &&
    //                         is_null($r->section) &&
    //                         is_null($r->unit)
    //                 );

    //                 // ----- SORT HERE by divordr -----
    //                 foreach ($remainingGroupRows->sortBy('divordr')->groupBy('division') as $divisionName => $divisionRows) {
    //                     $divisionData = [
    //                         'division'  => $divisionName,
    //                         'employees' => [],
    //                         'sections'  => []
    //                     ];

    //                     $divisionEmployees = $divisionRows->filter(
    //                         fn($r) =>
    //                         is_null($r->section) &&
    //                             is_null($r->unit)
    //                     );
    //                     $divisionData['employees'] = $divisionEmployees
    //                         ->sortBy('ItemNo')
    //                         // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                         ->map(fn($r) => $this->mapEmployee($r))

    //                         ->values();

    //                     $remainingDivisionRows = $divisionRows->reject(
    //                         fn($r) =>
    //                         is_null($r->section) &&
    //                             is_null($r->unit)
    //                     );

    //                     // ----- SORT HERE by secordr -----
    //                     foreach ($remainingDivisionRows->sortBy('secordr')->groupBy('section') as $sectionName => $sectionRows) {
    //                         $sectionData = [
    //                             'section'   => $sectionName,
    //                             'employees' => [],
    //                             'units'     => []
    //                         ];

    //                         $sectionEmployees = $sectionRows->filter(
    //                             fn($r) =>
    //                             is_null($r->unit)
    //                         );
    //                         $sectionData['employees'] = $sectionEmployees
    //                             ->sortBy('ItemNo')
    //                             // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                             ->map(fn($r) => $this->mapEmployee($r))

    //                             ->values();

    //                         $remainingSectionRows = $sectionRows->reject(
    //                             fn($r) =>
    //                             is_null($r->unit)
    //                         );

    //                         // ----- SORT HERE by unitordr -----
    //                         foreach ($remainingSectionRows->sortBy('unitordr')->groupBy('unit') as $unitName => $unitRows) {
    //                             $sectionData['units'][] = [
    //                                 'unit'      => $unitName,
    //                                 'employees' => $unitRows
    //                                     ->sortBy('ItemNo')
    //                                     // ->map(fn($r) => $this->mapEmployee($r, $xServiceByControl))
    //                                     ->map(fn($r) => $this->mapEmployee($r))

    //                                     ->values()
    //                             ];
    //                         }

    //                         $divisionData['sections'][] = $sectionData;
    //                     }

    //                     $groupData['divisions'][] = $divisionData;
    //                 }

    //                 $office2Data['groups'][] = $groupData;
    //             }

    //             $officeData['office2'][] = $office2Data;
    //         }

    //         $result[] = $officeData;
    //     }



    //     return response()->json($result);
    // }


    // this structure
    public function getOfficePlantilla(Request $request)
    {

        $request->validate([
            'officeId' => 'required|integer|exists:offices,id',
            // 'office_name' => 'required',
            // 'organization' => 'required',
            'year' => 'required',
            'semester' => 'required'
        ]);

        $officeName = DB::table('offices')->where('id', $request->officeId)->value('name');
        if (!$officeName) return response()->json([]);

        // BASE RESULT STRUCTURE
        $officeData = [
            'officeId' => $request->officeId,
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

        return response()->json([$officeData]);
    }

    private function mapEmployee($row)
    {
        return [
            'controlNo'   => $row->controlNo, // ✅ FIX
            'lastname'    => $row->lastname,
            'firstname'   => $row->firstname,
            'middlename'  => $row->middlename,
            'rank'   => $row->rank, // ✅ FIX

        ];
    }

}
