<?php

namespace App\Http\Controllers;

use App\Models\office;
use App\Models\Employee;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employees' => 'required|array',
            'employees.*.name' => 'required|string|max:255',
            'employees.*.position' => 'required|string|max:255',
            'employees.*.office_id' => 'required|exists:offices,id',
            'employees.*.office' => 'nullable|string|max:255',
            'employees.*.division' => 'nullable|string|max:255',
            'employees.*.section' => 'nullable|string|max:255',
            'employees.*.unit' => 'nullable|string|max:255',
        ]);

        $createdEmployees = [];

        foreach ($validated['employees'] as $employeeData) {
            $employee = Employee::create($employeeData);

            // Enhanced activity logging
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
                ->log('Employee Created');

            $createdEmployees[] = $employee;
        }

        return response()->json([
            'success' => true,
            'message' => 'Employees created successfully',
            'employees' => $createdEmployees
        ]);
    }


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

    public function fetchEmployees(Request $request)
    {
        $query = Employee::query();

        // Filter by office_id if provided
        if ($request->has('office_id')) {
            $query->where('office_id', $request->office_id);
        }

        // Filter by division if provided
        if ($request->has('division')) {
            $query->where('division', $request->division);
        }

        // Filter by section if provided
        if ($request->has('section')) {
            $query->where('section', $request->section);
        }

        // Filter by unit if provided
        if ($request->has('unit')) {
            $query->where('unit', $request->unit);
        }

        $employees = $query->get();

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

        // Get counts for the entire office
        $officeCount = Employee::where('office_id', $officeId)->count();

        // Get counts grouped by division
        $divisionCounts = Employee::where('office_id', $officeId)
            ->whereNotNull('division')
            ->select('division', DB::raw('count(*) as count'))
            ->groupBy('division')
            ->get()
            ->keyBy('division');

        // Get counts grouped by section
        $sectionCounts = Employee::where('office_id', $officeId)
            ->whereNotNull('section')
            ->select('section', DB::raw('count(*) as count'))
            ->groupBy('section')
            ->get()
            ->keyBy('section');

        // Get counts grouped by unit
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

    // softDelete for MFO
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
    //         ->log('Employee soft deleted');

    //     return response()->json([
    //         'message' => 'Employee soft deleted successfully',
    //         'counts' => [
    //             'office' => $officeCount,
    //             'division' => $divisionCount,
    //             'section' => $sectionCount,
    //             'unit' => $unitCount
    //         ]
    //     ]);
    // }
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
