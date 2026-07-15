<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Qpef;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\vwplantillastructure;
use App\Services\SpmsService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class SpmsController extends BaseController
{


    protected ?Authenticatable $user = null;
    protected ?int $officeId = null;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->officeId = $this->user->office_id;

            return $next($request);
        });
    }

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


    // fetch the employee  base on the office and  the target peroid of the employee
    public function getEmployeeRequested(Request $request, SpmsService $employee)
    {

        $employees = $employee->employeesRequest($request);

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




    // get the employee base on the employee
    public function getStructureOffice()
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        if (!$officeId) return response()->json([]);

        $officeName = DB::table('offices')->where('id', $officeId)->value('name');
        if (!$officeName) return response()->json([]);

        $rows = DB::table('employees as e')
            ->where('e.office', $officeName)
            ->select(
                'e.ControlNo',
                'e.ItemNo',
                'e.office',
                'e.office2',
                'e.group',
                'e.division',
                'e.section',
                'e.unit',
                'e.name',
                'e.status',
                'e.position',
                'e.job_title',

            )
            ->orderBy('e.office2')
            ->orderBy('e.group')
            ->orderBy('e.division')
            ->orderBy('e.section')
            ->orderBy('e.unit')
            ->orderBy('e.ItemNo')
            ->get();

        if ($rows->isEmpty()) return response()->json([]);

        $result = [];
        $officeGroups = $rows->groupBy('office');

        foreach ($officeGroups as $officeName => $officeRows) {
            $officeData = [
                'office'    => $officeName,
                'employees' => [],
                'office2'   => []
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

                    foreach ($remainingGroupRows->groupBy('division') as $divisionName => $divisionRows) {
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

                        foreach ($remainingDivisionRows->groupBy('section') as $sectionName => $sectionRows) {
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

                            foreach ($remainingSectionRows->groupBy('unit') as $unitName => $unitRows) {
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

        return response()->json($result);
    }

    private function mapEmployee($row)
    {
        return [
            'controlNo' => $row->ControlNo,
            'name'      => $row->name,
            'status' => $row->status,
            'position' => $row->position,
            'job_title' => $row->job_title,
            'office' => $row->office
        ];
    }

    public function getEmployeeUnderOfHead(Request $request)
    {
        $year = $request->input('year');

        $user = Auth::user();
        $controlNo = $user->control_no;

        $userStruture = Employee::select('ControlNo', 'job_title', 'office', 'office2', 'group', 'division', 'section', 'unit')
            ->where('ControlNo', $controlNo)
            ->first();

        if (!$userStruture) {
            return response()->json([
                'message' => 'No employee record found for the current user.',
            ], 404);
        }

        $employees = $this->fetchEmployeesUnderStructure($userStruture);

        $department_office = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'office', 'status', 'job_title')
            ->where('office_id', $user->office_id)
            ->where('job_title', 'Department Head')
            ->first();

        $employeeControlNos = $employees->pluck('ControlNo')->filter()->values();

        $qpefs = Qpef::select('id', 'control_no', 'year', 'quarterly', 'rated_by', 'status')
            ->where('year', $year)
            ->whereIn('control_no', $employeeControlNos)
            ->get()
            ->groupBy('control_no');

        $employeesWithQpef = $employees->map(function ($e) use ($qpefs) {
            return [
                'controlNo' => $e->ControlNo,
                'name'      => $e->name,
                'status'    => $e->status,
                'position'  => $e->position,
                'job_title' => $e->job_title,
                'office'    => $e->office,
                'qpef'      => $qpefs->get($e->ControlNo, collect())->values(),
            ];
        });

        return response()->json([
            'employee'             => $employeesWithQpef->values(),
            'immediate_supervisor' => [
                'name'     => $user->name,
                'position' => $user->designation,
                'office'   => $userStruture->office,
                'office2'  => $userStruture->office2,
                'group'    => $userStruture->group,
                'division' => $userStruture->division,
                'section'  => $userStruture->section,
                'unit'     => $userStruture->unit,
            ],
            'department_office'    => $department_office,
        ]);
    }
    // private function findEmployeesSameNode(array $structure, string $controlNo): array
    // {
    //     foreach ($structure as $officeData) {

    //         // Check office-level employees
    //         $found = $this->controlNoExistsIn($officeData['employees'], $controlNo);
    //         if ($found) {
    //             return $this->excludeSelf($officeData['employees'], $controlNo);
    //         }

    //         foreach ($officeData['office2'] as $office2Data) {

    //             // Check office2-level employees
    //             $found = $this->controlNoExistsIn($office2Data['employees'], $controlNo);
    //             if ($found) {
    //                 return $this->excludeSelf($office2Data['employees'], $controlNo);
    //             }

    //             foreach ($office2Data['groups'] as $groupData) {

    //                 // Check group-level employees
    //                 $found = $this->controlNoExistsIn($groupData['employees'], $controlNo);
    //                 if ($found) {
    //                     return $this->excludeSelf($groupData['employees'], $controlNo);
    //                 }

    //                 foreach ($groupData['divisions'] as $divisionData) {

    //                     // Check division-level employees
    //                     $found = $this->controlNoExistsIn($divisionData['employees'], $controlNo);
    //                     if ($found) {
    //                         return $this->excludeSelf($divisionData['employees'], $controlNo);
    //                     }

    //                     foreach ($divisionData['sections'] as $sectionData) {

    //                         // Check section-level employees
    //                         $found = $this->controlNoExistsIn($sectionData['employees'], $controlNo);
    //                         if ($found) {
    //                             return $this->excludeSelf($sectionData['employees'], $controlNo);
    //                         }

    //                         foreach ($sectionData['units'] as $unitData) {

    //                             // Check unit-level employees
    //                             $found = $this->controlNoExistsIn($unitData['employees'], $controlNo);
    //                             if ($found) {
    //                                 return $this->excludeSelf($unitData['employees'], $controlNo);
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return [];
    // }

    // private function controlNoExistsIn(array $employees, string $controlNo): bool
    // {
    //     return collect($employees)->contains('controlNo', $controlNo);
    // }

    // private function excludeSelf(array $employees, string $controlNo): array
    // {
    //     return collect($employees)
    //         ->reject(fn($e) => $e['controlNo'] === $controlNo)
    //         ->filter(fn($e) => in_array(strtoupper($e['status']), ['CASUAL', 'HONORARIUM', 'CONTRACTUAL', 'JOB ORDER']))
    //         ->values()
    //         ->all();
    // }

    private function fetchEmployeesUnderStructure($userStruture)
    {
        $query = Employee::query()->where('office', $userStruture->office);

        foreach (['office2', 'group', 'division', 'section', 'unit'] as $level) {
            if (!is_null($userStruture->$level)) {
                $query->where($level, $userStruture->$level);
            }
        }

        return $query->get()
            ->reject(fn($e) => $e->ControlNo === $userStruture->ControlNo)
            ->filter(fn($e) => in_array(strtoupper($e->status ?? ''), ['CASUAL', 'HONORARIUM', 'CONTRACTUAL', 'JOB ORDER',]))
            ->values();
    }
}
