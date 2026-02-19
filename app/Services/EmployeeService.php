<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\vwActive;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    public function storeEmployees($validated){


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

            return  $createdEmployees;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    // list of employee in the office
    public function employee($request, $user)
    {


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
                        ->whereRaw('vwActive.ControlNo = employees.ControlNo');
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



            return [
                'employees' => $employees,
                'office_name' => $officeName
            ];

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employees: ' . $e->getMessage()
            ], 500);
        }
    }



    //search employees by name or designation
    public function onSearchEmployee($request)
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
                'vwActive.Status',
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
                        ->whereRaw('vwActive.ControlNo = employees.ControlNo');
                });
            }

            $employees = $query->get();

            return $employees;
   
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
