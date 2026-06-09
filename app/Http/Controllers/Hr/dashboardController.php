<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\vwActive;
use App\Models\vwplantillastructure;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;

    }

    // get the data  dashboard
    public function dashboardSummaryData(Request $request)
    {
          $year     = $request->input('year');
        $semester = $request->input('semester');   
        $employee = $this->dashboardService->dashboard($year, $semester);

        return $employee;
    }

    // get the current target period of spms
    public function currentTargetPeriod(Request $request)
    {
       //  FIX — specify the key for each parameter
        $year     = $request->input('year');
        $semester = $request->input('semester');     
        $employee = $this->dashboardService->currentTargetPeriod($year, $semester);

        return $employee;
    }

    // get the list of IPCR target period of spms
    public function listOfIpcr(Request $request)
    {
        $year  = $request->input('year');
        $semester = $request->input('semester');       
        // $office = $request->input('office');   
        $employee = $this->dashboardService->listOfIpcr($year, $semester);

        return $employee;
    }

    // get the list of unit work plans
    public function listOfUnitWorkPlan(Request $request)
    {
        $year  = $request->input('year');
        $semester = $request->input('semester');       
        $office = $request->input('office');    

        $employee = $this->dashboardService->listOfUnitWorkPlan($year, $semester,$office);

        return $employee;
    }


    // get the list of IPCR target period of spms
    public function listOfOpcr(Request $request)
    {
        $year  = $request->input('year');
        $semester = $request->input('semester');       
        // $office = $request->input('office');    
        $employee = $this->dashboardService->listOfOpcr($year, $semester);

        return $employee;
    }


    public function plantillaEmployee()
    {
        // DB::connection('second_db')
        //   ->table('vwplantillaStructure as p')
        $rows = vwplantillastructure::from('vwplantillaStructure as p')
            ->leftJoin('vwofficearrangement as o', 'o.Office', '=', 'p.office')
            ->select(
                'p.*',
                'p.level',
                'p.ControlNo',
                'p.Name4',
                'o.office_sort'
            )
            ->orderBy('o.office_sort')
            ->orderBy('p.office2')
            ->orderBy('p.group')
            ->orderBy('p.division')
            ->orderBy('p.section')
            ->orderBy('p.unit')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $result        = [];
        $officeGroups  = $rows->groupBy('office');
        $totalOffices  = $officeGroups->count();

        foreach ($officeGroups as $officeName => $officeRows) {
            $officeSort  = $officeRows->first()->office_sort;
            $officeLevel = $officeRows->first()->level;

            $officeData = [
                'office'      => $officeName,
                'level'       => $officeLevel,
                'office_sort' => $officeSort,
                'employees'   => [],
                'office2'     => []
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

                    foreach ($remainingGroupRows->sortBy('divordr')->groupBy('division') as $divisionName => $divisionRows) {
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

                        foreach ($remainingDivisionRows->sortBy('secordr')->groupBy('section') as $sectionName => $sectionRows) {
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

                            foreach ($remainingSectionRows->sortBy('unitordr')->groupBy('unit') as $unitName => $unitRows) {
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

        $result = collect($result)->sortBy('office_sort')->values()->all();

        return $result;
    }

    private function mapEmployee(vwplantillastructure $row): array
    {
        return [
            'controlNo' => $row->ControlNo, // ✅ fixed
            'name'      => $row->Name4,     // ✅ fixed
        ];
    }
}
