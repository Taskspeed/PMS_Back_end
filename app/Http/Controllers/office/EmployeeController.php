<?php

namespace App\Http\Controllers\office;

use App\Models\office;
use App\Models\Employee;
use App\Models\Position;
use App\Models\vwActive;
use Illuminate\Http\Request;
use App\Services\EmployeeService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\addEmployeeRequest;

class EmployeeController extends Controller
{

    //add employee on the plantilla structure


    public function store(addEmployeeRequest $request)
    {
        $validated = $request->validated();

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

    // add an employee on the plantilla structure
    public function addEmployee(addEmployeeRequest $request, EmployeeService $employeeStore)
    {
        $validated = $request->validated();

        $employee = $employeeStore->storeEmployees($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employees created successfully',
            'employees' => $employee

           ]);
    }



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

    // fetch the employee base on the user office
    public function listOfEmployee(Request $request, EmployeeService $employeeList)
    {
        $user = Auth::user();

        if (!$user || !$user->name) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or no office assigned.'
            ], 403);
        }

        try {

            $result = $employeeList->employee($request, $user);

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
    public function searchEmployee(Request $request ,EmployeeService $employeeService)
    {

     $employee = $employeeService->onSearchEmployee($request);



        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }

}
