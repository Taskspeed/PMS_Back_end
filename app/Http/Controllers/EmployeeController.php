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

            activity()
                ->performedOn($employee)
                ->causedBy(Auth::user())
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
}
