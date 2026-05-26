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
    //     $year = $request->input('year');
    //     $semester = $request->input('semester');
    //     $user = Auth::user();
    //     $controlNo = $user->control_no;

    //     if (!$controlNo) return response()->json([]);

    //     // Get the full office structure
    //     $structure = $this->getStructureOffice();
    //     $structureData = json_decode($structure->getContent(), true);

    //     // Search through the structure to find where this controlNo belongs
    //     $employees = $this->findEmployeesSameNode($structureData, $controlNo);

    //     $department_office = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'office', 'status', 'job_title')
    //         ->where('office_id', $user->office_id)
    //         ->where('job_title', 'Office Head')
    //         ->first();

    //     // Safely extract control numbers — handle both array key formats
    //     $employeeControlNos = collect($employees)->map(function ($employee) {
    //         // Try all possible key formats
    //         if (is_array($employee)) {
    //             return $employee['ControlNo']
    //                 ?? $employee['control_no']
    //                 ?? $employee['controlNo']
    //                 ?? null;
    //         }
    //         // If it's an object
    //         return $employee->ControlNo
    //             ?? $employee->control_no
    //             ?? null;
    //     })->filter()->values();

    //     // Fetch all QPEFs for those employees based on the year
    //     $ipcrEmployee = Employee::with(['targetPeriods' => function($query) use ($year,$semester){
    //                 $query->select('id','control_no','semester','year')->where('year', $year)->where('semester', $semester)->with('ipcrLastestRecord');
    //     }])->select('id','name', 'ControlNo','status')



    //         ->whereIn('ControlNo', $employeeControlNos)
    //         ->get()
    //         ->groupBy('ControlNo');

    //           $emp->has_target_period = $existing ? true : false;

    //             if ($existing) {
    //                 $latestRecord = $existing->ipcrLastestRecord;

    //                 // ✅ Flatten only the fields you need
    //                 $existing->status           = $latestRecord?->status ?? null;
    //                 $existing->processed_by_name = $latestRecord?->processed_by_name ?? null;
    //                 $existing->date             = $latestRecord?->date ?? null;

    //                // ✅ Use makeHidden() to remove the relation from serialization
    //                   $existing->makeHidden('ipcrLastestRecord');
    //             }


    //     // Map QPEF data into each employee
    //     $employeesWithIpcr = collect($employees)->map(function ($employee) use ($ipcrEmployee) {
    //         // Safely get the control number from the employee
    //         if (is_array($employee)) {
    //             $empControlNo = $employee['ControlNo']
    //                 ?? $employee['control_no']
    //                 ?? $employee['controlNo']
    //                 ?? null;
    //         } else {
    //             $empControlNo = $employee->ControlNo
    //                 ?? $employee->control_no
    //                 ?? null;
    //         }

    //         // Attach QPEFs to the employee
    //         if (is_array($employee)) {
    //             $employee['ipcrEmployee'] = $ipcrEmployee->get($empControlNo, collect())->values();
    //         } else {
    //             $employee->ipcrEmployee = $ipcrEmployee->get($empControlNo, collect())->values();
    //         }

    //         return $employee;
    //     });

    //     return response()->json([
    //         'employee'             => $employeesWithIpcr,
    //         'immediate_supervisor' => [
    //             'name'     => $user->name,
    //             'position' => $user->designation,
    //         ],
    //         'department_office'    => $department_office,
    //     ]);
    // }

    public function getListOfEmployeeBaseOnSupervisor(Request $request)
    {


        $user = Auth::user();

        // ✅ Actually enforce the role check
        if ($user->role_id != 4) {
            return response()->json([
                'message' => 'Unauthorized. Access restricted to authorized person only.'
            ], 403);
        }

        $year = $request->input('year');
        $semester = $request->input('semester');

        $controlNo = $user->control_no;

        if (!$controlNo) return response()->json([]);

        // Get the full office structure
        $structure = $this->getStructureOffice();
        $structureData = json_decode($structure->getContent(), true);

        // Search through the structure to find where this controlNo belongs
        $employees = $this->findEmployeesSameNode($structureData, $controlNo);

        $department_office = Employee::select('id', 'name', 'rank', 'ControlNo', 'position', 'office', 'status', 'job_title')
            ->where('office_id', $user->office_id)
            ->where('job_title', 'Office Head')
            ->first();

        // Safely extract control numbers — handle both array key formats
        $employeeControlNos = collect($employees)->map(function ($employee) {
            if (is_array($employee)) {
                return $employee['ControlNo']
                    ?? $employee['control_no']
                    ?? $employee['controlNo']
                    ?? null;
            }
            return $employee->ControlNo ?? $employee->control_no ?? null;
        })->filter()->values();

        // Fetch all employees with their target periods
        $ipcrEmployee = Employee::with(['targetPeriods' => function ($query) use ($year, $semester) {
            $query->select('id', 'control_no', 'semester', 'year', 'supervisory_control_no')
                ->where('year', $year)
                ->where('semester', $semester)
                ->with('ipcrLastestRecord');
        }])
            ->select('id', 'name', 'ControlNo', 'status', 'position', 'job_title', 'office')
            ->whereIn('ControlNo', $employeeControlNos)
            ->get()
            ->keyBy('ControlNo');

        // Map and flatten employee data
        $employeesWithIpcr = collect($employees)->map(function ($employee) use ($ipcrEmployee) {
            // Resolve control number
            if (is_array($employee)) {
                $empControlNo = $employee['ControlNo']
                    ?? $employee['control_no']
                    ?? $employee['controlNo']
                    ?? null;
            } else {
                $empControlNo = $employee->ControlNo ?? $employee->control_no ?? null;
            }

            // Get the matched DB employee record
            $dbEmployee = $ipcrEmployee->get($empControlNo);

            // Get the first matching target period
            $existing = $dbEmployee?->targetPeriods->first();

            $hasTargetPeriod = $existing ? true : false;
            $existingTargetPeriod = null;

            if ($existing) {
                $latestRecord = $existing->ipcrLastestRecord;

                $existingTargetPeriod = [
                    'id'                  => $existing->id,
                    'control_no'          => $existing->control_no,
                    'year'                => $existing->year,
                    'semester'            => $existing->semester,
                    'supervisory_control_no' => $existing->supervisory_control_no,
                    'status'              => $latestRecord?->status ?? null,
                    'processed_by_name'   => $latestRecord?->processed_by_name ?? null,
                    'date'                => $latestRecord?->date ?? null,
                ];
            }

            // Build the flattened response
            return [
                'controlNo'              => $empControlNo,
                'name'                   => $dbEmployee?->name,
                'status'                 => $dbEmployee?->status,
                'position'               => $dbEmployee?->position,
                'job_title'              => $dbEmployee?->job_title,
                'office'                 => $dbEmployee?->office,
                'office2' => $dbEmployee?->office2,
            'group' =>$dbEmployee?->group,
            'division' => $dbEmployee?->division,
            'section' => $dbEmployee?->section,
            'unit' => $dbEmployee?->unit,
                'has_target_period'      => $hasTargetPeriod,
                'existing_target_period' => $existingTargetPeriod,
            ];
        });

        return response()->json([
            'employee'             => $employeesWithIpcr,
            // 'immediate_supervisor' => [
            //     'name'     => $user->name,
            //     'position' => $user->designation,
            // ],
            // 'department_office'    => $department_office,
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
