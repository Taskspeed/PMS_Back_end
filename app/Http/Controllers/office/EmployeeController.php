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

    // private function SgLeveL(){ // for  casual status only
    //     // this is the convert to sg example is the employee are have C1 on his grade  is means he got sg 10
    //     $C1 =  10;
    //     $C2 =  2;
    //     $C3 =  3;
    //     $C4 =  4;
    //     $C5 =  5;
    //     $C6 =  6;
    //     $C7 =  7;
    //     $C8 =  8;
    //     $C9 =  9;

    //     $D1 = 11;
    //     $D2 = 12;
    //     $D3 = 13;
    //     $D4 = 14;
    //     $D5 = 15;
    //     $D6 = 16;
    //     $D7 = 17;
    //     $D8 = 18;
    //     $D9 = 19;

    //     $E1 = 21;
    //     $E2 = 22;
    //     $E3 = 23;
    //     $E4 = 24;
    //     $E5 = 25;
    //     $E6 = 26;
    //     $E7 = 27;
    //     $E8 = 28;
    //     $E9 = 29;

    //     // to get the level to all sg 1-10 = 1 and sg 11-30  = 2




    // }


    // // Search employees by name or designation
    // public function searchEmployees(Request $request)
    // {
    //     $searchTerm = $request->query('search');
    //     $unassignedOnly = $request->query('unassigned_only', false);

    //     if (empty($searchTerm)) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Search term is required'
    //         ], 400);
    //     }

    //     try {
    //         $query = vwActive::where(function ($q) use ($searchTerm) {
    //             $q->where('Name4', 'LIKE', "%{$searchTerm}%")
    //                 ->orWhere('Designation', 'LIKE', "%{$searchTerm}%");
    //         });

    //         if ($unassignedOnly) {
    //             $query->whereNotExists(function ($query) {
    //                 $query->select(DB::raw(1))
    //                     ->from('employees')
    //                     ->whereRaw('vwActive.Name4 = employees.name');
    //             });
    //         }

    //         $employees = $query->get(['ControlNo',' Office as office', 'Name4 as name', 'Designation as position','ID','PositionID','ItemNo','PageNo' ]);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $employees
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Search failed: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function fetchEmployees(Request $request)
    // {
    //     $query = Employee::with('position'); // Eager load the position relationship

    //     // Filter by office_id if provided
    //     if ($request->has('office_id')) {
    //         $query->where('office_id', $request->office_id);
    //     }

    //     // Check for the most specific organizational unit first
    //     if ($request->has('unit')) {
    //         $query->where('unit', $request->unit);
    //     } elseif ($request->has('section')) {
    //         $query->where('section', $request->section)
    //             ->whereNull('unit');
    //     } elseif ($request->has('division')) {
    //         $query->where('division', $request->division)
    //             ->whereNull('section')
    //             ->whereNull('unit');
    //     } elseif ($request->has('office_id')) {
    //         $query->whereNull('division')
    //             ->whereNull('section')
    //             ->whereNull('unit');
    //     }

    //     $employees = $query->get()->map(function ($employee) {
    //         return [
    //             'id' => $employee->id,
    //             'name' => $employee->name,
    //             'position_id' => $employee->position_id,
    //             'position' => $employee->position ? $employee->position->name : null, // Include position name
    //             'office_id' => $employee->office_id,
    //             'office' => $employee->office,
    //             'division' => $employee->division,
    //             'section' => $employee->section,
    //             'unit' => $employee->unit,
    //             'rank' => $employee->rank
    //         ];
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $employees
    //     ]);
    // }


    // public function fetchEmployees(Request $request)
    // {
    //     $query = Employee::with('position'); // Eager load the position relationship

    //     // Filter by office_id first (required for all levels)
    //     if ($request->has('office_id')) {
    //         $query->where('office_id', $request->office_id);
    //     }

    //     // Check for the most specific organizational unit first
    //     if ($request->has('unit')) {
    //         // UNIT LEVEL
    //         $query->where('unit', $request->unit);
    //     } elseif ($request->has('section')) {
    //         // SECTION LEVEL
    //         $query->where('section', $request->section)
    //             ->whereNull('unit');
    //     } elseif ($request->has('division')) {
    //         // DIVISION LEVEL
    //         $query->where('division', $request->division)
    //             ->whereNull('section')
    //             ->whereNull('unit');
    //     } elseif ($request->has('group')) {
    //         // GROUP LEVEL
    //         $query->where('group', $request->group)
    //             ->whereNull('division')
    //             ->whereNull('section')
    //             ->whereNull('unit');
    //     } elseif ($request->has('office2')) {
    //         // OFFICE2 LEVEL
    //         $query->where('office2', $request->office2)
    //             ->whereNull('group')
    //             ->whereNull('division')
    //             ->whereNull('section')
    //             ->whereNull('unit');
    //     } elseif ($request->has('office_id')) {
    //         // OFFICE LEVEL ONLY (no division/section/unit/group/office2)
    //         $query->whereNull('office2')
    //             ->whereNull('group')
    //             ->whereNull('division')
    //             ->whereNull('section')
    //             ->whereNull('unit');
    //     }

    //     $employees = $query->get()->map(function ($employee) {
    //         return [
    //             'id' => $employee->id,
    //             'name' => $employee->name,
    //             'position_id' => $employee->position_id,
    //             'position' => $employee->position ? $employee->position->name : null,
    //             'office_id' => $employee->office_id,
    //             'office' => $employee->office,
    //             'office2' => $employee->office2,  // ← ADDED
    //             'group' => $employee->group,      // ← ADDED
    //             'division' => $employee->division,
    //             'section' => $employee->section,
    //             'unit' => $employee->unit,
    //             'rank' => $employee->rank
    //         ];
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $employees
    //     ]);
    // }


    // // Add a new endpoint for getting counts
    // public function getEmployeeCounts(Request $request)
    // {

    //     $user = Auth::user();

    //     $officeId = $user->office_id;

    //     if (!$officeId) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Office ID is required'
    //         ], 400);
    //     }

    //     // Count office-level employees (no division, section, or unit)
    //     $officeCount = Employee::where('office_id', $officeId)
    //         ->whereNull('office2')
    //         ->whereNull('group')
    //         ->whereNull('division')
    //         ->whereNull('section')
    //         ->whereNull('unit')
    //         ->count();

    //     // Count division-level employees (no section or unit)
    //     $divisionCounts = Employee::where('office_id', $officeId)
    //         ->whereNotNull('division')
    //         ->whereNull('section')
    //         ->whereNull('unit')
    //         ->select('division', DB::raw('count(*) as count'))
    //         ->groupBy('division')
    //         ->get()
    //         ->keyBy('division');

    //     // Count section-level employees (no unit)
    //     $sectionCounts = Employee::where('office_id', $officeId)
    //         ->whereNotNull('section')
    //         ->whereNull('unit')
    //         ->select('section', DB::raw('count(*) as count'))
    //         ->groupBy('section')
    //         ->get()
    //         ->keyBy('section');

    //     // Count unit-level employees
    //     $unitCounts = Employee::where('office_id', $officeId)
    //         ->whereNotNull('unit')
    //         ->select('unit', DB::raw('count(*) as count'))
    //         ->groupBy('unit')
    //         ->get()
    //         ->keyBy('unit');

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'office' => $officeCount,
    //             'divisions' => $divisionCounts,
    //             'sections' => $sectionCounts,
    //             'units' => $unitCounts
    //         ]
    //     ]);
    // }


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

    // // Fetch only active (non-deleted) mfo
    // public function index()
    // {

    //     $employee = Employee::whereNull('deleted_at')->get();

    //     return response()->json($employee);
    // }

    // // fetch_mfo_SoftDeleted
    // public function getSoftDeleted()
    // {

    //     $employee = Employee::onlyTrashed()->get();

    //     return response()->json($employee);
    // }

    // public function softDelete($id)
    // {
    //     $employee = Employee::findOrFail($id);
    //     $employee->delete();

    //     // Get updated counts
    //     $officeId = $employee->office_id;
    //     $officeCount = Employee::where('office_id', $officeId)->count();

    //     $divisionCount = $employee->division ? Employee::where('office_id', $officeId)
    //         ->where('division', $employee->division)
    //         ->count() : null;

    //     $sectionCount = $employee->section ? Employee::where('office_id', $officeId)
    //         ->where('section', $employee->section)
    //         ->count() : null;

    //     $unitCount = $employee->unit ? Employee::where('office_id', $officeId)
    //         ->where('unit', $employee->unit)
    //         ->count() : null;

    //     activity()
    //         ->performedOn($employee)
    //         ->causedBy(Auth::user())
    //         ->log('Employee soft deleted');

    //     return response()->json([
    //         'message' => 'Employee soft deleted successfully',
    //         'counts' => [
    //             'office' => $officeCount,
    //             'divisions' => $employee->division ? [$employee->division => $divisionCount] : [],
    //             'sections' => $employee->section ? [$employee->section => $sectionCount] : [],
    //             'units' => $employee->unit ? [$employee->unit => $unitCount] : []
    //         ]
    //     ]);
    // }

    // // restore soft-deleted data
    // public function restore($id)
    // {

    //     $employee = Employee::onlyTrashed()->findOrFail($id);
    //     $employee->restore();

    //     activity()
    //         ->performedOn($employee)
    //         ->causedBy(Auth::user())
    //         ->withProperties([
    //             'name' => $employee->name,
    //             'position' => $employee->position,
    //             'rank' => $employee->rank,
    //             'office' => $employee->office,
    //             'division' => $employee->division,
    //             'section' => $employee->section,
    //             'unit' => $employee->unit,
    //             'office_id' => $employee->office_id
    //         ])
    //         ->log('Employee restored');

    //     return response()->json(['message' => 'Employee restored successfully']);
    // }


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
