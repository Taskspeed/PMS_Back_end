<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IpcrController extends Controller
{
    //


    // In IpcrController

    public function getEmployeesWithUnitWorkPlans()
    {

        return Employee::select(
            'employees.id',
            'employees.name',
            'positions.name as position',
            'unit_work_plans.status'
        )
            ->join('unit_work_plans', 'employees.id', '=', 'unit_work_plans.employee_id')
            ->join('positions', 'employees.position_id', '=', 'positions.id')
            ->with(['unitWorkPlans']) // Eager load the relationship if needed
            ->groupBy('employees.id', 'employees.name', 'positions.name', 'unit_work_plans.status')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                    'status' => $employee->status,
                    'unit_work_plans' => [
                        [
                            'id' => $employee->unit_work_plans->id ?? null,
                            'status' => $employee->status
                        ]
                    ]
                ];
            });
    }

}
