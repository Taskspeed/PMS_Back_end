<?php

namespace App\Http\Controllers;

use App\Models\office;
use App\Models\Employee;
use App\Models\Position;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    //add employee to division,section,unit

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employees' => 'required|array',
            'employees.*.name' => 'required|string|max:255',
            'employees.*.position_id' => 'required|exists:positions,id',
            'employees.*.office_id' => 'required|exists:offices,id',
            'employees.*.office' => 'nullable|string|max:255',
            'employees.*.division' => 'nullable|string|max:255',
            'employees.*.section' => 'nullable|string|max:255',
            'employees.*.unit' => 'nullable|string|max:255',
            'employees.*.rank' => 'nullable|in:Head,Supervisor,Employee,Rank-in-File,Managerial'
        ]);

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
                        'position_id' => $employee->position_id,
                        'rank' => $employee->rank,
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

    public function index_position()
    {
        $positions = Position::all();
        return response()->json($positions);
    }

        //rank update
    public function updateRank(Request $request, $id)

    {
        $validated = $request->validate([
            'rank' => 'required|in:Head,Supervisor,Employee,Managerial,Rank-in-File'
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


    // for fetching employees on the modal
    public function show_employee(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->office_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or no office assigned.'
            ], 403);
        }

        try {
            $office = Office::find($user->office_id);
            if (!$office) {
                return response()->json([
                    'success' => false,
                    'message' => 'Office not found'
                ], 404);
            }

            // Get parameters from the request
            $showAll = $request->query('show_all', false);
            $unassignedOnly = $request->query('unassigned_only', false);

            $query = vwActive::select('Office as office', 'Name4 as name', 'Designation as position');

            // Only filter by office if show_all is false
            if (!$showAll) {
                $query->where('office_id', $user->office_id);
            }

            // Filter for unassigned employees only if requested
            if ($unassignedOnly) {
                $query->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('employees')
                        ->whereRaw('vwActive.Name4 = employees.name');
                });
            }

            $employees = $query->get();

            return response()->json([
                'success' => true,
                'data' => $employees,
                'user_office' => $office->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employees: ' . $e->getMessage()
            ], 500);
        }
    }

    // Search employees by name or designation
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
            $query = vwActive::where(function ($q) use ($searchTerm) {
                $q->where('Name4', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('Designation', 'LIKE', "%{$searchTerm}%");
            });

            if ($unassignedOnly) {
                $query->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('employees')
                        ->whereRaw('vwActive.Name4 = employees.name');
                });
            }

            $employees = $query->get(['Office as office', 'Name4 as name', 'Designation as position']);

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

    public function fetchEmployees(Request $request)
    {
        $query = Employee::with('position'); // Eager load the position relationship

        // Filter by office_id if provided
        if ($request->has('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        // Check for the most specific organizational unit first
        if ($request->has('unit')) {
            $query->where('unit', $request->unit);
        } elseif ($request->has('section')) {
            $query->where('section', $request->section)
                ->whereNull('unit');
        } elseif ($request->has('division')) {
            $query->where('division', $request->division)
                ->whereNull('section')
                ->whereNull('unit');
        } elseif ($request->has('office_id')) {
            $query->whereNull('division')
                ->whereNull('section')
                ->whereNull('unit');
        }

        $employees = $query->get()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'position_id' => $employee->position_id,
                'position' => $employee->position ? $employee->position->name : null, // Include position name
                'office_id' => $employee->office_id,
                'office' => $employee->office,
                'division' => $employee->division,
                'section' => $employee->section,
                'unit' => $employee->unit,
                'rank' => $employee->rank
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $employees
        ]);
    }

    // Add a new endpoint for getting counts
    public function getEmployeeCounts(Request $request)
    {
        $officeId = $request->office_id;

        if (!$officeId) {
            return response()->json([
                'success' => false,
                'message' => 'Office ID is required'
            ], 400);
        }

        // Count office-level employees (no division, section, or unit)
        $officeCount = Employee::where('office_id', $officeId)
            ->whereNull('division')
            ->whereNull('section')
            ->whereNull('unit')
            ->count();

        // Count division-level employees (no section or unit)
        $divisionCounts = Employee::where('office_id', $officeId)
            ->whereNotNull('division')
            ->whereNull('section')
            ->whereNull('unit')
            ->select('division', DB::raw('count(*) as count'))
            ->groupBy('division')
            ->get()
            ->keyBy('division');

        // Count section-level employees (no unit)
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

        return response()->json([
            'success' => true,
            'data' => [
                'office' => $officeCount,
                'divisions' => $divisionCounts,
                'sections' => $sectionCounts,
                'units' => $unitCounts
            ]
        ]);
    }

    // Fetch only active (non-deleted) mfo
    public function index()
    {

        $employee = Employee::whereNull('deleted_at')->get();

        return response()->json($employee);
    }

    // fetch_mfo_SoftDeleted
    public function getSoftDeleted()
    {

        $employee = Employee::onlyTrashed()->get();

        return response()->json($employee);
    }

    public function softDelete($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        // Get updated counts
        $officeId = $employee->office_id;
        $officeCount = Employee::where('office_id', $officeId)->count();

        $divisionCount = $employee->division ? Employee::where('office_id', $officeId)
            ->where('division', $employee->division)
            ->count() : null;

        $sectionCount = $employee->section ? Employee::where('office_id', $officeId)
            ->where('section', $employee->section)
            ->count() : null;

        $unitCount = $employee->unit ? Employee::where('office_id', $officeId)
            ->where('unit', $employee->unit)
            ->count() : null;

        activity()
            ->performedOn($employee)
            ->causedBy(Auth::user())
            ->log('Employee soft deleted');

        return response()->json([
            'message' => 'Employee soft deleted successfully',
            'counts' => [
                'office' => $officeCount,
                'divisions' => $employee->division ? [$employee->division => $divisionCount] : [],
                'sections' => $employee->section ? [$employee->section => $sectionCount] : [],
                'units' => $employee->unit ? [$employee->unit => $unitCount] : []
            ]
        ]);
    }
    // restore soft-deleted data
    public function restore($id)
    {

        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->restore();

        activity()
            ->performedOn($employee)
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $employee->name,
                'position' => $employee->position,
                'rank' => $employee->rank,
                'office' => $employee->office,
                'division' => $employee->division,
                'section' => $employee->section,
                'unit' => $employee->unit,
                'office_id' => $employee->office_id
            ])
            ->log('Employee restored');

        return response()->json(['message' => 'Employee restored successfully']);
    }
}
