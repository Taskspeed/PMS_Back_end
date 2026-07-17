<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupervisorService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }
    public function getListOfEmployeeBaseOnSupervisor($year, $semester, $controlNo, $user)
    {

        if (!$controlNo) return null;  // or throw an exception

        // ── Fetch the logged-in supervisor's own employee record ──────────────────
        $selfEmployee = Employee::select(
            'id',
            'name',
            'rank',
            'ControlNo',
            'position',
            'office',
            'office2',
            'group',
            'division',
            'section',
            'unit',
            'status',
            'job_title',
            'sg',
            'level'
        )
            ->where('ControlNo', $controlNo)
            ->first();

        $userStruture = Employee::select('ControlNo', 'job_title', 'office', 'office2', 'group', 'division', 'section', 'unit')
            ->where('ControlNo', $controlNo)
            ->first();

        // if (!$selfEmployee) return response()->json([]);
        if (!$selfEmployee) return null;

        // ── Build hierarchy from the employee's own DB columns ───────────────────
        $hierarchy = $this->buildHierarchyFromEmployee($selfEmployee);

        // ── Resolve signatories ───────────────────────────────────────────────────
        $signatories = $this->resolveSignatories($selfEmployee, $user->office_id, $year, $semester);

        // ── Fetch existing target period for the logged-in supervisor ────────────
        $selfTargetPeriod = $selfEmployee->targetPeriods()
            ->select('id', 'control_no', 'semester', 'year', 'supervisory_control_no')
            ->where('year', $year)
            ->where('semester', $semester)
            ->with('ipcrLastestRecord')
            ->first();

        $hasTargetPeriod      = $selfTargetPeriod ? true : false;
        $existingTargetPeriod = null;

        if ($selfTargetPeriod) {
            $latestRecord = $selfTargetPeriod->ipcrLastestRecord;
            $existingTargetPeriod = [
                'id'                     => $selfTargetPeriod->id,
                'control_no'             => $selfTargetPeriod->control_no,
                'year'                   => $selfTargetPeriod->year,
                'semester'               => $selfTargetPeriod->semester,
                'supervisory_control_no' => $selfTargetPeriod->supervisory_control_no,
                'status'                 => $latestRecord?->status ?? null,
                'processed_by_name'      => $latestRecord?->processed_by_name ?? null,
                'date'                   => $latestRecord?->date ?? null,
            ];
        }

        // ── Build the single employee object (the logged-in supervisor) ───────────
        $employeeData = [
            'id'                     => (string) $selfEmployee->id,
            'controlNo'              => $selfEmployee->ControlNo,
            'name'                   => $selfEmployee->name,
            'label'                  => $selfEmployee->name,
            'position'               => $selfEmployee->position,
            'rank'                   => $selfEmployee->rank,
            'jobTitle'               => $selfEmployee->job_title,
            'sg'                     => $selfEmployee->sg ?? null,
            'level'                  => $selfEmployee->level ?? null,
            'status'                 => $selfEmployee->status,
            'office'                 => $selfEmployee->office,
            'office2'                => $selfEmployee->office2,
            'group'                  => $selfEmployee->group,
            'division'               => $selfEmployee->division,
            'section'                => $selfEmployee->section,
            'unit'                   => $selfEmployee->unit,
            'has_target_period'      => $hasTargetPeriod,
            'existing_target_period' => $existingTargetPeriod,
            'supervisorySignatory'   => $signatories['supervisorySignatory'],
            'managerialSignatory'    => $signatories['managerialSignatory'],
        ];

        // ── Get the full office structure for finding subordinates ────────────────
        $structure     = $this->getStructureOffice();
        $structureData = json_decode($structure->getContent(), true);

        // Get subordinates list (same node employees)
        // $employees = $this->findEmployeesSameNode($structureData, $controlNo);
        $employees = $this->fetchEmployeesUnderStructure($userStruture);

        // Safely extract control numbers
        $employeeControlNos = collect($employees)->map(function ($employee) {
            if (is_array($employee)) {
                return $employee['ControlNo']
                    ?? $employee['control_no']
                    ?? $employee['controlNo']
                    ?? null;
            }
            return $employee->ControlNo ?? $employee->control_no ?? null;
        })->filter()->values();

        // Fetch subordinates with their target periods
        $ipcrEmployee = Employee::with(['targetPeriods' => function ($query) use ($year, $semester) {
            $query->select('id', 'control_no', 'semester', 'year', 'supervisory_control_no')
                ->where('year', $year)
                ->where('semester', $semester)
                ->with('ipcrLastestRecord');
        }])
            ->select(
                'id',
                'name',
                'ControlNo',
                'status',
                'position',
                'job_title',
                'office',
                'office2',
                'group',
                'division',
                'section',
                'unit'
            )
            ->whereIn('ControlNo', $employeeControlNos)
            ->get()
            ->keyBy('ControlNo');

        // Map subordinates
        $employeesWithIpcr = collect($employees)->map(function ($employee) use ($ipcrEmployee) {
            if (is_array($employee)) {
                $empControlNo = $employee['ControlNo']
                    ?? $employee['control_no']
                    ?? $employee['controlNo']
                    ?? null;
            } else {
                $empControlNo = $employee->ControlNo ?? $employee->control_no ?? null;
            }

            $dbEmployee = $ipcrEmployee->get($empControlNo);
            $existing   = $dbEmployee?->targetPeriods->first();

            $hasTargetPeriod      = $existing ? true : false;
            $existingTargetPeriod = null;

            if ($existing) {
                $latestRecord = $existing->ipcrLastestRecord;
                $existingTargetPeriod = [
                    'id'                     => $existing->id,
                    'control_no'             => $existing->control_no,
                    'year'                   => $existing->year,
                    'semester'               => $existing->semester,
                    'supervisory_control_no' => $existing->supervisory_control_no,
                    'status'                 => $latestRecord?->status ?? null,
                    'processed_by_name'      => $latestRecord?->processed_by_name ?? null,
                    'date'                   => $latestRecord?->date ?? null,
                ];
            }

            return [
                'controlNo'              => $empControlNo,
                'name'                   => $dbEmployee?->name,
                'status'                 => $dbEmployee?->status,
                'position'               => $dbEmployee?->position,
                'job_title'              => $dbEmployee?->job_title,
                'office'                 => $dbEmployee?->office,
                'office2'                => $dbEmployee?->office2,
                'group'                  => $dbEmployee?->group,
                'division'               => $dbEmployee?->division,
                'section'                => $dbEmployee?->section,
                'unit'                   => $dbEmployee?->unit,
                'has_target_period'      => $hasTargetPeriod,
                'existing_target_period' => $existingTargetPeriod,
            ];
        });

        // return response()->json([
        //     'hierarchy' => $hierarchy,
        //     'employee'  => $employeeData,          // ✅ single object (the supervisor)
        //     'subordinates' => $employeesWithIpcr,  // ✅ array of their subordinates
        //     // 'timestamp' => now()->toISOString(),
        // ]);
        return [
            'hierarchy'    => $hierarchy,
            'employee'     => $employeeData,
            'subordinates' => $employeesWithIpcr,
        ];
    }
    private function findEmployeesSameNode(array $structure, string $controlNo): array
    {
        foreach ($structure as $officeData) {

            // Check office-level employees
            $found = $this->controlNoExistsIn($officeData['employees'], $controlNo);
            if ($found) {
                return $this->excludeSelf($officeData['employees'], $controlNo);
            }

            foreach ($officeData['office2'] as $office2Data) {

                // Check office2-level employees
                $found = $this->controlNoExistsIn($office2Data['employees'], $controlNo);
                if ($found) {
                    return $this->excludeSelf($office2Data['employees'], $controlNo);
                }

                foreach ($office2Data['groups'] as $groupData) {

                    // Check group-level employees
                    $found = $this->controlNoExistsIn($groupData['employees'], $controlNo);
                    if ($found) {
                        return $this->excludeSelf($groupData['employees'], $controlNo);
                    }

                    foreach ($groupData['divisions'] as $divisionData) {

                        // Check division-level employees
                        $found = $this->controlNoExistsIn($divisionData['employees'], $controlNo);
                        if ($found) {
                            return $this->excludeSelf($divisionData['employees'], $controlNo);
                        }

                        foreach ($divisionData['sections'] as $sectionData) {

                            // Check section-level employees
                            $found = $this->controlNoExistsIn($sectionData['employees'], $controlNo);
                            if ($found) {
                                return $this->excludeSelf($sectionData['employees'], $controlNo);
                            }

                            foreach ($sectionData['units'] as $unitData) {

                                // Check unit-level employees
                                $found = $this->controlNoExistsIn($unitData['employees'], $controlNo);
                                if ($found) {
                                    return $this->excludeSelf($unitData['employees'], $controlNo);
                                }
                            }
                        }
                    }
                }
            }
        }

        return [];
    }

    private function controlNoExistsIn(array $employees, string $controlNo): bool
    {
        return collect($employees)->contains('controlNo', $controlNo);
    }

    private function excludeSelf(array $employees, string $controlNo): array
    {
        return collect($employees)
            // ->reject(fn($e) => $e['controlNo'] === $controlNo)
            ->filter(fn($e) => in_array(strtoupper($e['status']), ['CASUAL', 'REGULAR']))
            ->values()
            ->all();
    }


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
            ->filter(fn($e) => in_array(strtoupper($e->status ?? ''), [ 'REGULAR', 'CASUAL','CO-TERMINOUS']))
            ->values();
    }

    private function buildHierarchyFromEmployee(Employee $employee): array
    {
        return [
            'office'  => $employee->office
                ? ['label' => $employee->office,  'type' => 'office']
                : null,
            'office2' => $employee->office2
                ? ['label' => $employee->office2, 'type' => 'office2']
                : null,
            'group'   => $employee->group
                ? ['label' => $employee->group,   'type' => 'group']
                : null,
            'division' => $employee->division
                ? ['label' => $employee->division, 'type' => 'division']
                : null,
            'section' => $employee->section
                ? ['label' => $employee->section,  'type' => 'section']
                : null,
            'unit'    => $employee->unit
                ? ['label' => $employee->unit,     'type' => 'unit']
                : null,
        ];
    }

    private function resolveSignatories(Employee $employee, int $officeId, $year, $semester): array
    {
        // ── Helper closure to build signatory data with target period ─────────────
        $buildSignatory = function (Employee $emp) use ($year, $semester) {
            $existing = $emp->targetPeriods->first();
            return [
                'controlNo'              => $emp->ControlNo,
                'name'                   => $emp->name,
                'position'               => $emp->position,
                'rank'                   => $emp->rank,
                'jobTitle'               => $emp->job_title,
                'has_target_period'      => $existing ? true : false,
                'existing_target_period' => $existing ? [
                    'control_no' => $existing->control_no,
                    'year'       => $existing->year,
                    'semester'   => $existing->semester,
                ] : null,
            ];
        };

        $withTargetPeriod = fn($query) => $query
            ->select('control_no', 'semester', 'year')
            ->where('year', $year)
            ->where('semester', $semester);

        // ── Always get the Department Head first ──────────────────────────────────────
        $officeHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
            ->with(['targetPeriods' => $withTargetPeriod])
            ->where('office_id', $officeId)
            ->where('job_title', 'Department Head')
            ->where('ControlNo', '!=', $employee->ControlNo)
            ->first();

        $officeHeadData      = $officeHead ? $buildSignatory($officeHead) : null;
        $managerialSignatory = $officeHeadData;
        $supervisorySignatory = null;

        // 1️⃣ Section Head
        if ($employee->section && $employee->job_title !== 'Section Head') {
            $sectionHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->with(['targetPeriods' => $withTargetPeriod])
                ->where('office_id', $officeId)
                ->where('section', $employee->section)
                ->where('job_title', 'Section Head')
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->first();

            if ($sectionHead) {
                $supervisorySignatory = $buildSignatory($sectionHead);
            }
        }

        // 2️⃣ Division Head
        if (!$supervisorySignatory && $employee->division) {
            $divisionHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->with(['targetPeriods' => $withTargetPeriod])
                ->where('office_id', $officeId)
                ->where('division', $employee->division)
                ->where('job_title', 'Division Head')
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->whereNull('section')
                ->whereNull('unit')
                ->first();

            if ($divisionHead) {
                $supervisorySignatory = $buildSignatory($divisionHead);
            }
        }

        // 3️⃣ Office Head
        if (!$supervisorySignatory && $employee->office2) {
            $office2Head = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->with(['targetPeriods' => $withTargetPeriod])
                ->where('office_id', $officeId)
                ->where('office2', $employee->office2)
                ->where('job_title', 'Office Head')
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->whereNull('division')
                ->whereNull('section')
                ->whereNull('unit')
                ->first();

            if ($office2Head) {
                $supervisorySignatory = $buildSignatory($office2Head);
            }
        }

        // 4️⃣ Group Head
        if (!$supervisorySignatory && $employee->group) {
            $groupHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->with(['targetPeriods' => $withTargetPeriod])
                ->where('office_id', $officeId)
                ->where('group', $employee->group)
                ->where('job_title', 'Group Head')
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->whereNull('division')
                ->whereNull('section')
                ->whereNull('unit')
                ->first();

            if ($groupHead) {
                $supervisorySignatory = $buildSignatory($groupHead);
            }
        }

        // 5️⃣ Final fallback: Department Head
        if (!$supervisorySignatory) {
            $supervisorySignatory = $officeHeadData;
        }

        return [
            'supervisorySignatory' => $supervisorySignatory,
            'managerialSignatory'  => $managerialSignatory,
        ];
    }


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
            'office' => $row->office,

        ];
    }
}
