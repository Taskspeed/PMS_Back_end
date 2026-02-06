<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Http\Requests\addEmployeeRequest;
use App\Models\office;
use App\Models\Employee;
use App\Models\Position;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{

    //add employee on the plantilla structure


    public function store(addEmployeeRequest $request)
    {
        $validated = $request->validated(
            // 'employees' => 'required|array',
            // 'employees.*.ControlNo' => 'required|string',
            // 'employees.*.name' => 'required|string|max:255',
            // // 'employees.*.position_id' => 'required|exists:positions,id',
            // 'employees.*.office_id' => 'required|exists:offices,id',
            // 'employees.*.office' => 'nullable|string|max:255',
            // 'employees.*.position' => 'required|string|max:255', // changed from 'designation' to 'position'
            // 'employees.*.office2' => 'nullable|string|max:255',
            // 'employees.*.group' => 'nullable|string|max:255',
            // 'employees.*.division' => 'nullable|string|max:255',
            // 'employees.*.section' => 'nullable|string|max:255',
            // 'employees.*.unit' => 'nullable|string|max:255',
            // 'employees.*.rank' => 'nullable|in:Supervisor,Employee,Rank-in-File,Managerial,Section-Head,Office-Head,Division-Head',

            // 'employees.*.tblStructureID' => 'required|string|max:255',
            // 'employees.*.sg' => 'required|string|max:255',
            // 'employees.*.level' => 'required|string|max:255',
            // 'employees.*.positionID' => 'required|string|max:255',
            // 'employees.*.itemNo' => 'required|string|max:255',
            // 'employees.*.pageNo' => 'required|string|max:255',
        );

        $createdEmployees = [];

        // Use a transaction to ensure data integrity
        DB::beginTransaction();
        try {
            foreach ($validated['employees'] as $employeeData) {
                // Set default rank to Employee if not provided
                if (!isset($employeeData['rank'])) {
                    $employeeData['rank'] = 'Employee';
                }

                $employee = Employee::create($employeeData);

                // Enhanced activity logging
                activity()
                    ->performedOn($employee)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'name' => $employee->name,
                        // 'position_id' => $employee->position_id,
                        'rank' => $employee->rank,
                        'designation' => $employee->designation,
                        'office' => $employee->office,
                        'division' => $employee->division,
                        'section' => $employee->section,
                        'unit' => $employee->unit,
                        'office_id' => $employee->office_id
                    ])
                    ->log('Employee Created');

                $createdEmployees[] = $employee;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employees created successfully',
                'employees' => $createdEmployees
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'employees' => 'required|array',
    //         'employees.*.ControlNo' => 'required|string',
    //         'employees.*.name' => 'required|string|max:255',
    //         // 'employees.*.position_id' => 'required|exists:positions,id',
    //         'employees.*.office_id' => 'required|exists:offices,id',
    //         'employees.*.office' => 'nullable|string|max:255',
    //         'employees.*.position' => 'required|string|max:255', // changed from 'designation' to 'position'
    //         'employees.*.office2' => 'nullable|string|max:255',
    //         'employees.*.group' => 'nullable|string|max:255',
    //         'employees.*.division' => 'nullable|string|max:255',
    //         'employees.*.section' => 'nullable|string|max:255',
    //         'employees.*.unit' => 'nullable|string|max:255',
    //         'employees.*.rank' => 'nullable|in:Supervisor,Employee,Rank-in-File,Managerial,Section-Head,Office-Head,Division-Head',

    //         'employees.*.tblStructureID' => 'required|string|max:255',
    //         'employees.*.sg' => 'required|string|max:255',
    //         'employees.*.level' => 'required|string|max:255',
    //         'employees.*.positionID' => 'required|string|max:255',
    //         'employees.*.itemNo' => 'required|string|max:255',
    //         'employees.*.pageNo' => 'required|string|max:255',
    //     ]);

    //     $createdEmployees = [];

    //     // Use a transaction to ensure data integrity
    //     DB::beginTransaction();
    //     try {
    //         foreach ($validated['employees'] as $employeeData) {
    //             // Set default rank to Employee if not provided
    //             if (!isset($employeeData['rank'])) {
    //                 $employeeData['rank'] = 'Employee';
    //             }

    //             $employee = Employee::create($employeeData);

    //             // Enhanced activity logging
    //             activity()
    //                 ->performedOn($employee)
    //                 ->causedBy(Auth::user())
    //                 ->withProperties([
    //                     'name' => $employee->name,
    //                     // 'position_id' => $employee->position_id,
    //                     'rank' => $employee->rank,
    //                 'designation' => $employee->designation,
    //                     'office' => $employee->office,
    //                     'division' => $employee->division,
    //                     'section' => $employee->section,
    //                     'unit' => $employee->unit,
    //                     'office_id' => $employee->office_id
    //                 ])
    //                 ->log('Employee Created');

    //             $createdEmployees[] = $employee;
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Employees created successfully',
    //             'employees' => $createdEmployees
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to create employees',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function index_position()
    // {
    //     $positions = Position::all();
    //     return response()->json($positions);
    // }

    //rank update of employee
    public function updateRank(Request $request, $id) // need to check  this code for review

    {
        $validated = $request->validate([
            'rank' => 'required|string'
        ]);

        $employee = Employee::findOrFail($id);

        // Check if this is a Head promotion
        if ($validated['rank'] === 'Head') {
            $query = Employee::where('office_id', $employee->office_id)
                ->where('rank', 'Head')
                ->where('id', '!=', $employee->id);

            // Check based on organizational level
            if ($employee->unit) {
                $query->where('unit', $employee->unit);
            } elseif ($employee->section) {
                $query->where('section', $employee->section)
                    ->whereNull('unit');
            } elseif ($employee->division) {
                $query->where('division', $employee->division)
                    ->whereNull('section')
                    ->whereNull('unit');
            } else {
                $query->whereNull('division')
                    ->whereNull('section')
                    ->whereNull('unit');
            }

            $existingHead = $query->first();

            if ($existingHead) {
                return response()->json([
                    'success' => false,
                    'message' => 'There is already a Head in this organizational unit'
                ], 422);
            }
        }

        $employee->rank = $validated['rank'];
        $employee->save();

        activity()
            ->performedOn($employee)
            ->causedBy(Auth::user())
            ->withProperties(['new_rank' => $validated['rank']])
            ->log('Employee rank updated');

        return response()->json([
            'success' => true,
            'message' => 'Employee rank updated successfully'
        ]);
    }


    // // for fetching employees on the modal
    // public function show_employee(Request $request)
    // {
    //     $user = Auth::user();

    //     if (!$user || !$user->office_id) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized or no office assigned.'
    //         ], 403);
    //     }

    //     try {
    //         $office = Office::find($user->office_id);
    //         if (!$office) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Office not found'
    //             ], 404);
    //         }

    //         $showAll = $request->query('show_all', false);
    //         $unassignedOnly = $request->query('unassigned_only', false);

    //         // Build query with LEFT JOIN
    //         $query = vwActive::select(
    //             'vwActive.Office as office',
    //             'vwActive.Name4 as name',
    //             'vwActive.Designation as position',
    //             'vwActive.ControlNo',
    //             'vwplantillaStructure.ItemNo',
    //             'vwplantillaStructure.PageNo',
    //             'vwplantillaStructure.PositionID',
    //             'vwplantillaStructure.ID as tblStructureID',
    //             // 'vwplantillalevel.ID as tblStructureID',
    //             'vwplantillalevel.SG',
    //             'vwplantillalevel.SGLevel',
    //         )
    //             ->leftJoin('vwplantillaStructure', 'vwActive.ControlNo', '=', 'vwplantillaStructure.ControlNo')
    //             ->leftJoin('vwplantillalevel', 'vwplantillalevel.ID', '=', 'vwplantillaStructure.ID');

    //         // Only filter by office if show_all is false
    //         if (!$showAll) {
    //             $query->where('vwActive.Office', $office->name); // <- corrected
    //         }

    //         // Filter for unassigned employees only if requested
    //         if ($unassignedOnly) {
    //             $query->whereNotExists(function ($q) {
    //                 $q->select(DB::raw(1))
    //                     ->from('employees')
    //                     ->whereRaw('vwActive.Name4 = employees.name');
    //             });
    //         }

    //         $employees = $query->get();

    //         return response()->json([
    //             'success' => true,
    //             'data' => $employees,
    //             'user_office' => $office->name
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to fetch employees: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // //search employees by name or designation
    public function searchEmployees(Request $request)
    {
        $searchTerm = $request->query('search');
        $unassignedOnly = $request->query('unassigned_only', false);

        if (empty($searchTerm)) {
            return response()->json([
                'success' => false,
                'message' => 'Search term is required'
            ], 400);
        }

        try {
            $query = vwActive::select(
                'vwActive.Name4 as name',
                'vwActive.Office as office',

                'vwActive.Designation as position',
                'vwActive.ControlNo',
                'vwplantillaStructure.ItemNo',
                'vwplantillaStructure.PageNo',
                'vwplantillaStructure.PositionID',
                'vwplantillaStructure.ID as tblStructureID',
                // 'vwplantillalevel.ID as tblStructureID',
                'vwplantillalevel.SG',
                'vwplantillalevel.Level as SGLevel',
            )
                ->leftJoin('vwplantillaStructure', 'vwActive.ControlNo', '=', 'vwplantillaStructure.ControlNo')
                ->leftJoin('vwplantillalevel', 'vwplantillalevel.ID', '=', 'vwplantillaStructure.ID')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('vwActive.Name4', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('vwActive.Designation', 'LIKE', "%{$searchTerm}%");
                });

            if ($unassignedOnly) {
                $query->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('employees')
                        ->whereRaw('vwActive.Name4 = employees.name');
                });
            }

            $employees = $query->get();

            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show_employee(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->name) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or no office assigned.'
            ], 403);
        }

        try {
            // Get the user's office name
            $officeName = $user->Office->name;

            // Build query with LEFT JOIN
           $query = vwActive::select(
                'vwActive.Office as office',
                'vwActive.Name4 as name',
                'vwActive.Designation as position',
                'vwActive.ControlNo',
                'vwActive.Status',
                'vwActive.Grades',
                'vwplantillaStructure.ItemNo',
                'vwplantillaStructure.PageNo',
                'vwplantillaStructure.PositionID',
                'vwplantillaStructure.ID as tblStructureID',
                'vwplantillalevel.SG',
                // 'vwplantillalevel.SGLevel'
                'vwplantillalevel.Level as SGLevel'
            )
                ->leftJoin('vwplantillaStructure', 'vwActive.ControlNo', '=', 'vwplantillaStructure.ControlNo')
                ->leftJoin('vwplantillalevel', 'vwplantillalevel.ID', '=', 'vwplantillaStructure.ID');
            // if the employee is CASUAL use his grade to Convert  this sg and get his level



            // Only filter by office if show_all is false
            $showAll = $request->query('show_all', false);
            if (!$showAll) {
                $query->where('vwActive.Office', $officeName);
            }

            // Filter for unassigned employees only if requested
            $unassignedOnly = $request->query('unassigned_only', false);
            if ($unassignedOnly) {
                $query->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('employees')
                        ->whereRaw('vwActive.Name4 = employees.name');
                });
            }

            $employees = $query->get();
            $employees->transform(function ($emp) {

                // Only CASUAL employees
                if ($emp->Status === 'CASUAL' && !empty($emp->Grades)) {

                    // Grade → SG mapping
                    $map = [
                        'C1' => '10',
                        'C2' => '11',
                        'C3' => '12',
                        'C4' => '13',
                        'C5' => '14',
                        'C6' => '15',
                        'C7' => '16',
                        'C8' => '17',
                        'C9' => '18',
                        'D1' => '11',
                        'D2' => '12',
                        'D3' => '13',
                        'D4' => '14',
                        'D5' => '15',
                        'D6' => '16',
                        'D7' => '17',
                        'D8' => '18',
                        'D9' => '19',
                        'E1' => '21',
                        'E2' => '22',
                        'E3' => '23',
                        'E4' => '24',
                        'E5' => '25',
                        'E6' => '26',
                        'E7' => '27',
                        'E8' => '28',
                        'E9' => '29',
                    ];

                    $grade = strtoupper(trim($emp->Grades));

                    if (isset($map[$grade])) {

                        // Set SG from grade
                        $emp->SG = $map[$grade];

                        // Compute level
                        // SG 1–10 = Level 1
                        // SG 11–30 = Level 2
                        $emp->SGLevel = ($emp->SG <= 10) ? '1' : '2';
                    }
                }

                return $emp;
            });


            return response()->json([
                'success' => true,
                'data' => $employees,
                'user_office' => $officeName
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employees: ' . $e->getMessage()
            ], 500);
        }
    }



    // Add a new endpoint for getting counts
    public function getEmployeeCounts(Request $request)
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        if (!$officeId) {
            return response()->json([
                'success' => false,
                'message' => 'Office ID is required'
            ], 400);
        }

        // Count office-level employees (no division, section, or unit)
        $officeCount = Employee::where('office_id', $officeId)
            ->whereNull('office2')
            ->whereNull('group')
            ->whereNull('division')
            ->whereNull('section')
            ->whereNull('unit')
            ->count();

        // Count division-level employees
        $divisionCounts = Employee::where('office_id', $officeId)
            ->whereNotNull('division')
            ->whereNull('section')
            ->whereNull('unit')
            ->select('division', DB::raw('count(*) as count'))
            ->groupBy('division')
            ->get()
            ->keyBy('division');

        // Count section-level employees
        $sectionCounts = Employee::where('office_id', $officeId)
            ->whereNotNull('section')
            ->whereNull('unit')
            ->select('section', DB::raw('count(*) as count'))
            ->groupBy('section')
            ->get()
            ->keyBy('section');

        // Count unit-level employees
        $unitCounts = Employee::where('office_id', $officeId)
            ->whereNotNull('unit')
            ->select('unit', DB::raw('count(*) as count'))
            ->groupBy('unit')
            ->get()
            ->keyBy('unit');

        // ⭐ Count group-level employees
        $groupCounts = Employee::where('office_id', $officeId)
            ->whereNotNull('group')
            ->select('group', DB::raw('count(*) as count'))
            ->groupBy('group')
            ->get()
            ->keyBy('group');

        // ⭐ Count office2-level employees
        $office2Counts = Employee::where('office_id', $officeId)
            ->whereNotNull('office2')
            ->select('office2', DB::raw('count(*) as count'))
            ->groupBy('office2')
            ->get()
            ->keyBy('office2');

        return response()->json([
                'office'   => $officeCount,
                'office2'  => $office2Counts,
                'groups'   => $groupCounts,
                'divisions' => $divisionCounts,
                'sections' => $sectionCounts,
                'units'    => $unitCounts,
            ]
        );
    }



    // In EmployeeController.php
    public function getOfficeStructureCounts(Request $request)
    {
        $officeId = $request->office_id;

        if (!$officeId) {
            return response()->json([
                'success' => false,
                'message' => 'Office ID is required'
            ], 400);
        }

        $divisionCount = Employee::where('office_id', $officeId)
            ->whereNotNull('division')
            ->distinct('division')
            ->count('division');

        $sectionCount = Employee::where('office_id', $officeId)
            ->whereNotNull('section')
            ->distinct('section')
            ->count('section');

        return response()->json([
            'success' => true,
            'data' => [
                'divisions' => $divisionCount,
                'sections' => $sectionCount,
                'total' => $divisionCount + $sectionCount
            ]
        ]);
    }

    //remove employee on the plantilla
    public function deleteEmployee($employeeId){

        $employee = Employee::findOrFail($employeeId);

        $employee->delete();

        return response()->json(
            [
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]
        );
    }


    // fetch employee
    public function getEmployee(Request $request)
    {
        $user = Auth::user();

        $officeId = $user->office_id;

        $employees = Employee::where('office_id', $officeId)
            ->get();
        return response()->json($employees);
    }
}
