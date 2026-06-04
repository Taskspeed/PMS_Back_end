<?php

namespace App\Http\Controllers\office;


use App\Http\Controllers\Controller;

use App\Http\Requests\addEmployeeRequest;
use App\Models\Employee;
use App\Models\JobTitle;
use App\Models\User;
use App\Services\EmployeeService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EmployeeController extends Controller
{
    use ApiResponseTrait;

    // arg  data,message

     protected EmployeeService $employeeService;

     public function __construct(EmployeeService $employeeService)
     {
        $this->employeeService = $employeeService;
     }


    // add an employee on the plantilla structure
    public function addEmployee(addEmployeeRequest $request,)
    {
        $validated = $request->validated();

        $employee = $this->employeeService->storeEmployees($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employees created successfully',
            'employees' => $employee

        ]);
    }

        // add an employee on the plantilla structure
    public function v1addEmployee(addEmployeeRequest $request)
    {
        $validated = $request->validated();

        $employee = $this->employeeService->v1storeEmployees($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employees created successfully',
            'employees' => $employee

        ]);
    }

    //rank update of employee
    public function updateRank(Request $request, int $id) // need to check  this code for review

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


    //fetch of list of jobtitle
    public function fetchJobTitle()
    {
        $job = \App\Models\JobTitle::all();

        return $job;
    }

    //Jobtitle update of employee
    public function updateJobTitle(Request $request, int $employeeId) // need to check  this code for review

    {
        $validated = $request->validate([
            'job_title' => 'required|string'
        ]);

        $job = $this->employeeService->jobTitle($employeeId, $validated);

        return $job;
    }

    //remove employee on the plantilla
    public function deleteEmployee(int $employeeId)
    {

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
    public function getEmployee()
    {
        $user = Auth::user();

        $officeId = $user->office_id;

        $employees = Employee::where('office_id', $officeId)
            ->get();
        return response()->json($employees);
    }


    // fetch the employee base on the user office
    public function listOfEmployee(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->name) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or no office assigned.'
            ], 403);
        }

        try {

            $result = $this->employeeService->employee($request, $user);

            return response()->json([
                'success' => true,
                'data' => $result['employees'],
                'user_office' => $result['office_name']
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // search employee on the list of employee
    public function searchEmployee(Request $request)
    {

        $employee = $this->employeeService->onSearchEmployee($request);

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }


    // list of the employee supervisory on the office
    public function listOfHead()
    {
        $user = Auth::user();

        // Filter out null values to prevent SQL NOT IN null issue
        $existingControlNos = User::where('role_id', 4)
            ->whereNotNull('control_no')
            ->pluck('control_no')
            ->toArray();

        $employee = Employee::select('name', 'position', 'ControlNo', 'office', 'job_title', 'status')
            ->where('office_id', $user->office_id)
            ->whereNotIn('job_title', ['Employee'])
            ->whereNotIn('ControlNo', $existingControlNos)
            ->get();

        if ($employee->isEmpty()) {
            return $this->errorMessage('No head employees found for this office.', 200);
        }

        return $this->successMessage($employee, 'Fetch employee successful');
    }
}
