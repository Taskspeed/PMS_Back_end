<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TargetPeriod;
use App\Models\TargetPeriodRecord;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupervisorController extends Controller
{
    //

    use ApiResponseTrait;

    // get the list of ipcr of my advisory
    public function getAdvisoryEmployeeIpcr(Request $request)
    {
        $supervisor = Auth::user();

        $year     = $request->input('year');
        $semester = $request->input('semester');

        // check if the supervisor is an Office Head
        $isOfficeHead = Employee::where('office_id', $supervisor->office_id)
            ->where('ControlNo', $supervisor->control_no)
            ->where('job_title', 'Office Head')
            ->exists();

        $query = TargetPeriod::select('id', 'control_no', 'year', 'semester', 'office_id', 'supervisory_control_no')
            ->where('office_id', $supervisor->office_id)
            ->where('year', $year)
            ->where('semester', $semester);

        if ($isOfficeHead) {
            // ✅ Office Head sees ALL employees in the office with Draft and Reviewed status
            $query->where('control_no', '!=', $supervisor->control_no) // ✅ exclude office head themselves
                ->whereHas('ipcrLastestRecord', function ($q) {
                    $q->whereIn(DB::raw('LOWER(status)'), ['Draft', 'Reviewed']); // ✅ lowercase
                });
        } else {
            // ✅ Supervisor only sees their advisory employees
            $query->where('supervisory_control_no', $supervisor->control_no)
                ->whereHas('ipcrLastestRecord', function ($q) {
                    $q->whereRaw("LOWER(status) = 'Draft'");
                });
        }

        $ipcr = $query->with(['employee:ControlNo,name', 'ipcrLastestRecord'])
            ->get()
            ->map(function ($item) {
                $item->name = $item->employee->name ?? null;
                unset($item->employee);

                return [
                    'ipcr_id'                => (int) $item->id,
                    'control_no'             => $item->control_no,
                    'year'                   => $item->year,
                    'semester'               => $item->semester,
                    'office_id'              => $item->office_id,
                    'supervisory_control_no' => $item->supervisory_control_no,
                    'name'                   => $item->name,
                    'ipcr_status'            => $item->ipcrLastestRecord->status ?? null,
                ];
            });

        if ($ipcr->isEmpty()) {
            return $this->infoMessage('No record found', 200);
        }

        return $this->successMessage($ipcr, 'Successfully fetch', 200);
    }
    // updating  ipcr status to reviewed
    public function updateIpcr(Request $request)
    {
        $supervisor = Auth::user();

        // ✅ check if the current user is an Office Head
        $isOfficeHead = Employee::where('office_id', $supervisor->office_id)
            ->where('ControlNo', $supervisor->control_no)
            ->where('job_title', 'Office Head')
            ->exists();

        $validated = $request->validate([
            'ipcr_id'   => 'required|array',
            'ipcr_id.*' => 'required|exists:target_periods,id',
            'status'    => 'required|in:Reviewed,Approved',
            'remarks'   => 'nullable|string',
        ], [
            'status.in' => "Status must be 'Reviewed' or 'Approved'.",
        ]);

        // ✅ block non-Office Head from using Approved status
        if ($validated['status'] === 'Approved' && ! $isOfficeHead) {
            return $this->errorMessage('Only the Office Head can Approve IPCR.', 403);
        }

        $records = [];

        foreach ($validated['ipcr_id'] as $ipcrId) {
            $records[] = TargetPeriodRecord::create([
                'target_period_id'  => $ipcrId,
                'date'              => Carbon::now()->format('Y-m-d'),
                'status'            => $validated['status'],
                'remarks'           => $validated['remarks'] ?? null,
                'processed_by'      => $supervisor->id,
                'processed_by_name' => $supervisor->name,
            ]);
        }

        return $this->successMessage($records, 'Successfully Updated', 200);
    }




    // public function getListOfEmployeeBaseOnSupervisor(Request $request)
    // {
    //     $user = Auth::user();

    //     if ($user->role_id != 4) {
    //         return response()->json([
    //             'message' => 'Unauthorized. Access restricted to authorized person only.'
    //         ], 403);
    //     }

    //     $year = $request->input('year');
    //     $semester = $request->input('semester');

    //     $controlNo = $user->control_no;

    //     if (!$controlNo) return response()->json([]);

    //     // Get the full office structure
    //     $structure = $this->getStructureOffice();
    //     $structureData = json_decode($structure->getContent(), true);

    //     // Search through the structure to find where this controlNo belongs
    //     $employees = $this->findEmployeesSameNode($structureData, $controlNo);

    //     // ✅ NEW: Get hierarchy + signatories for the current supervisor
    //     $hierarchyInfo  = $this->findEmployeeHierarchy($structureData, $controlNo);
    //     $signatories    = $this->resolveSignatories($structureData, $hierarchyInfo, $user->office_id);

    //     $department_office = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'office', 'status', 'job_title')
    //         ->where('office_id', $user->office_id)
    //         ->where('job_title', 'Office Head')
    //         ->first();

    //     // Safely extract control numbers
    //     $employeeControlNos = collect($employees)->map(function ($employee) {
    //         if (is_array($employee)) {
    //             return $employee['ControlNo']
    //                 ?? $employee['control_no']
    //                 ?? $employee['controlNo']
    //                 ?? null;
    //         }
    //         return $employee->ControlNo ?? $employee->control_no ?? null;
    //     })->filter()->values();

    //     // Fetch all employees with their target periods
    //     $ipcrEmployee = Employee::with(['targetPeriods' => function ($query) use ($year, $semester) {
    //         $query->select('id', 'control_no', 'semester', 'year', 'supervisory_control_no')
    //             ->where('year', $year)
    //             ->where('semester', $semester)
    //             ->with('ipcrLastestRecord');
    //     }])
    //         ->select('id', 'name', 'ControlNo', 'status', 'position', 'job_title', 'office')
    //         ->whereIn('ControlNo', $employeeControlNos)
    //         ->get()
    //         ->keyBy('ControlNo');

    //     // Map and flatten employee data
    //     $employeesWithIpcr = collect($employees)->map(function ($employee) use ($ipcrEmployee) {
    //         if (is_array($employee)) {
    //             $empControlNo = $employee['ControlNo']
    //                 ?? $employee['control_no']
    //                 ?? $employee['controlNo']
    //                 ?? null;
    //         } else {
    //             $empControlNo = $employee->ControlNo ?? $employee->control_no ?? null;
    //         }

    //         $dbEmployee = $ipcrEmployee->get($empControlNo);
    //         $existing   = $dbEmployee?->targetPeriods->first();

    //         $hasTargetPeriod    = $existing ? true : false;
    //         $existingTargetPeriod = null;

    //         if ($existing) {
    //             $latestRecord = $existing->ipcrLastestRecord;

    //             $existingTargetPeriod = [
    //                 'id'                     => $existing->id,
    //                 'control_no'             => $existing->control_no,
    //                 'year'                   => $existing->year,
    //                 'semester'               => $existing->semester,
    //                 'supervisory_control_no' => $existing->supervisory_control_no,
    //                 'status'                 => $latestRecord?->status ?? null,
    //                 'processed_by_name'      => $latestRecord?->processed_by_name ?? null,
    //                 'date'                   => $latestRecord?->date ?? null,
    //             ];
    //         }

    //         return [
    //             'controlNo'              => $empControlNo,
    //             'name'                   => $dbEmployee?->name,
    //             'status'                 => $dbEmployee?->status,
    //             'position'               => $dbEmployee?->position,
    //             'job_title'              => $dbEmployee?->job_title,
    //             'office'                 => $dbEmployee?->office,
    //             'office2'                => $dbEmployee?->office2,
    //             'group'                  => $dbEmployee?->group,
    //             'division'               => $dbEmployee?->division,
    //             'section'                => $dbEmployee?->section,
    //             'unit'                   => $dbEmployee?->unit,
    //             'has_target_period'      => $hasTargetPeriod,
    //             'existing_target_period' => $existingTargetPeriod,
    //         ];
    //     });

    //     return response()->json([
    //         'hierarchy'            => $hierarchyInfo['hierarchy'] ?? null,   // ✅ NEW
    //         'supervisorySignatory' => $signatories['supervisorySignatory'],  // ✅ NEW
    //         'managerialSignatory'  => $signatories['managerialSignatory'],   // ✅ NEW
    //         'employee'             => $employeesWithIpcr,
    //     ]);
    // }

    public function getListOfEmployeeBaseOnSupervisor(Request $request)
    {
        $user = Auth::user();

        if ($user->role_id != 4) {
            return response()->json([
                'message' => 'Unauthorized. Access restricted to authorized person only.'
            ], 403);
        }

        $year      = $request->input('year');
        $semester  = $request->input('semester');
        $controlNo = $user->control_no;

        if (!$controlNo) return response()->json([]);

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

        if (!$selfEmployee) return response()->json([]);

        // ── Build hierarchy from the employee's own DB columns ───────────────────
        $hierarchy = $this->buildHierarchyFromEmployee($selfEmployee);

        // ── Resolve signatories ───────────────────────────────────────────────────
        $signatories = $this->resolveSignatories($selfEmployee, $user->office_id);

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
        $employees = $this->findEmployeesSameNode($structureData, $controlNo);

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

        return response()->json([
            'hierarchy' => $hierarchy,
            'employee'  => $employeeData,          // ✅ single object (the supervisor)
            'subordinates' => $employeesWithIpcr,  // ✅ array of their subordinates
            'timestamp' => now()->toISOString(),
        ]);
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
    /**
     * Walk the structure tree and return the hierarchy labels
     * for the node where the given controlNo lives.
     */
    private function findEmployeeHierarchy(array $structure, string $controlNo): array
    {
        foreach ($structure as $officeData) {

            // Office-level
            if ($this->controlNoExistsIn($officeData['employees'], $controlNo)) {
                return $this->buildHierarchyResult($officeData['office'], null, null, null, null, null);
            }

            foreach ($officeData['office2'] as $office2Data) {

                // Office2-level
                if ($this->controlNoExistsIn($office2Data['employees'], $controlNo)) {
                    return $this->buildHierarchyResult($officeData['office'], $office2Data['office2'], null, null, null, null);
                }

                foreach ($office2Data['groups'] as $groupData) {

                    // Group-level
                    if ($this->controlNoExistsIn($groupData['employees'], $controlNo)) {
                        return $this->buildHierarchyResult($officeData['office'], $office2Data['office2'], $groupData['group'], null, null, null);
                    }

                    foreach ($groupData['divisions'] as $divisionData) {

                        // Division-level
                        if ($this->controlNoExistsIn($divisionData['employees'], $controlNo)) {
                            return $this->buildHierarchyResult($officeData['office'], $office2Data['office2'], $groupData['group'], $divisionData['division'], null, null);
                        }

                        foreach ($divisionData['sections'] as $sectionData) {

                            // Section-level
                            if ($this->controlNoExistsIn($sectionData['employees'], $controlNo)) {
                                return $this->buildHierarchyResult($officeData['office'], $office2Data['office2'], $groupData['group'], $divisionData['division'], $sectionData['section'], null);
                            }

                            foreach ($sectionData['units'] as $unitData) {

                                // Unit-level
                                if ($this->controlNoExistsIn($unitData['employees'], $controlNo)) {
                                    return $this->buildHierarchyResult($officeData['office'], $office2Data['office2'], $groupData['group'], $divisionData['division'], $sectionData['section'], $unitData['unit']);
                                }
                            }
                        }
                    }
                }
            }
        }

        return ['hierarchy' => null];
    }

    /**
     * Build the hierarchy array shape matching your expected response.
     */
    private function buildHierarchyResult(?string $office, ?string $office2, ?string $group, ?string $division, ?string $section, ?string $unit): array
    {
        return [
            'hierarchy' => [
                'office'   => $office   ? ['label' => $office,   'type' => 'office']   : null,
                'office2'  => $office2  ? ['label' => $office2,  'type' => 'office2']  : null,
                'group'    => $group    ? ['label' => $group,    'type' => 'group']    : null,
                'division' => $division ? ['label' => $division, 'type' => 'division'] : null,
                'section'  => $section  ? ['label' => $section,  'type' => 'section']  : null,
                'unit'     => $unit     ? ['label' => $unit,     'type' => 'unit']     : null,
            ],
            // carry these for signatory resolution
            '_office'   => $office,
            '_office2'  => $office2,
            '_group'    => $group,
            '_division' => $division,
            '_section'  => $section,
            '_unit'     => $unit,
        ];
    }

    /**
     * Build hierarchy labels directly from the employee's own DB columns.
     * This is accurate regardless of where they appear in the structure tree.
     */
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

    private function resolveSignatories(Employee $employee, int $officeId): array
    {
        // ── Always get the Office Head first ──────────────────────────────────────
        $officeHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
            ->where('office_id', $officeId)
            ->where('job_title', 'Office Head')
            ->where('ControlNo', '!=', $employee->ControlNo)
            ->first();

        $officeHeadData = $officeHead ? [
            'controlNo' => $officeHead->ControlNo,
            'name'      => $officeHead->name,
            'position'  => $officeHead->position,
            'rank'      => $officeHead->rank,
            'jobTitle'  => $officeHead->job_title,
        ] : null;

        // managerialSignatory is always the Office Head
        $managerialSignatory = $officeHeadData;

        // ── supervisorySignatory: Section Head → Division Head → Office2 Head → Office Head ──
        $supervisorySignatory = null;

        // 1️⃣ If employee is in a section, look for Section Head in that section
        if ($employee->section && $employee->job_title !== 'Section Head') {
            $sectionHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->where('office_id', $officeId)
                ->where('section', $employee->section)
                ->where('job_title', 'Section Head')
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->first();

            if ($sectionHead) {
                $supervisorySignatory = [
                    'controlNo' => $sectionHead->ControlNo,
                    'name'      => $sectionHead->name,
                    'position'  => $sectionHead->position,
                    'rank'      => $sectionHead->rank,
                    'jobTitle'  => $sectionHead->job_title,
                ];
            }
        }

        // 2️⃣ Fallback: Look for Division Head in the same division
        if (!$supervisorySignatory && $employee->division) {
            $divisionHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->where('office_id', $officeId)
                ->where('division', $employee->division)
                ->where('job_title', 'Division Head')
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->whereNull('section')
                ->whereNull('unit')
                ->first();

            if ($divisionHead) {
                $supervisorySignatory = [
                    'controlNo' => $divisionHead->ControlNo,
                    'name'      => $divisionHead->name,
                    'position'  => $divisionHead->position,
                    'rank'      => $divisionHead->rank,
                    'jobTitle'  => $divisionHead->job_title,
                ];
            }
        }

        // 3️⃣ Fallback: Look for a Sub-Office Head / Office2 Head in the same office2
        if (!$supervisorySignatory && $employee->office2) {
            $office2Head = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->where('office_id', $officeId)
                ->where('office2', $employee->office2)
                ->where('job_title', 'Sub-Office Head') // adjust to your actual job_title value
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->whereNull('division')
                ->whereNull('section')
                ->whereNull('unit')
                ->first();

            if ($office2Head) {
                $supervisorySignatory = [
                    'controlNo' => $office2Head->ControlNo,
                    'name'      => $office2Head->name,
                    'position'  => $office2Head->position,
                    'rank'      => $office2Head->rank,
                    'jobTitle'  => $office2Head->job_title,
                ];
            }
        }

        // 3️⃣ Fallback: Look for a Sub-Office Head / Office2 Head in the same office2
        if (!$supervisorySignatory && $employee->group) {
            $groupHead = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'status', 'job_title')
                ->where('office_id', $officeId)
                ->where('group', $employee->group)
                ->where('job_title', 'Group Head') // adjust to your actual job_title value
                ->where('ControlNo', '!=', $employee->ControlNo)
                ->whereNull('division')
                ->whereNull('section')
                ->whereNull('unit')
                ->first();

            if ($groupHead) {
                $supervisorySignatory = [
                    'controlNo' => $groupHead->ControlNo,
                    'name'      => $groupHead->name,
                    'position'  => $groupHead->position,
                    'rank'      => $groupHead->rank,
                    'jobTitle'  => $groupHead->job_title,
                ];
            }
        }

        // 4️⃣ Final fallback: Office Head
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
