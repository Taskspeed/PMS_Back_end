<?php

namespace App\Http\Controllers\Planning;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Unit_work_plan;
use App\Models\Office;


class Planning_Unit_work_planController extends Controller
{
    public function office(Request $request)
    {
        $query = Office::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $office = $query->get();
        return response()->json($office);
    }

    public function unit_work_plan(Request $request)
    {
        $query = Unit_work_plan::query();

        if ($request->has('division')) {
            $query->where('division', $request->division);
        }

        if ($request->has('target_period')) {
            $period = explode(' ', $request->target_period);
            $query->where('target_period', $period[0])
                ->where('year', $period[1]);
        }

        $data = $query->with(['employee', 'office'])->get();
        return response()->json($data);
    }

    public function employee()
    {
        $data = Employee::all();
        return response()->json($data);
    }

    public function getDivisionsWithWorkPlans(Request $request)
    {
        $request->validate([
            'office_id' => 'required|integer',
            'target_period' => 'nullable|string'
        ]);

        $officeId = $request->office_id;
        $targetPeriod = $request->target_period;

        $query = Unit_work_plan::where('office_id', $officeId)
            ->select(
                'division',
                'target_period',
                'year',
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(status) as status')
            )
            ->groupBy('division', 'target_period', 'year');

        if ($targetPeriod) {
            $parts = explode(' ', $targetPeriod);
            $period = implode(' ', array_slice($parts, 0, -1));
            $year = end($parts);

            $query->where('target_period', $period)
                ->where('year', $year);
        }

        $data = $query->orderBy('division')
            ->orderBy('year')
            ->orderBy('target_period')
            ->get()
            ->map(function ($item) {
                return [
                    'division' => $item->division,
                    'target_period' => $item->target_period,
                    'year' => $item->year,
                    'full_period' => $item->target_period . ' ' . $item->year,
                    'created_at' => $item->created_at ? $item->created_at->format('M j, Y') : '',
                    'status' => $item->status
                ];
            });

        return response()->json([
            'data' => $data,
            'total_divisions' => $data->count()
        ]);
    }



    public function getEmployeesByDivision(Request $request)
    {
        $request->validate([
            'division' => 'required|string',
            'target_period' => 'required|string', // Now accepts "January - June 2025"

            'office_id' => 'required|integer'
        ]);

        try {
            $division = $request->division;
            $fullPeriod = $request->target_period; // e.g. "January - June 2025"
            $officeId = $request->office_id;

            // Parse the period and year
            $parts = explode(' ', $fullPeriod);
            $period = $parts[0] . ' - ' . $parts[2]; // "January - June"
            $year = $parts[3]; // "2025"

            // Get employees
            $employees = Employee::where('division', $division)
                ->where('office_id', $officeId)
                ->whereNull('deleted_at')
                ->with(['position'])
                ->get();

            if ($employees->isEmpty()) {
                return response()->json(['error' => 'No employees found for this division'], 404);
            }

            // Get work plans
            $workPlans = Unit_work_plan::whereIn('employee_id', $employees->pluck('id'))
                ->where('division', $division)
                ->where('target_period', $period)
                ->where('year', $year)
                ->get();

            // Format response
            $formattedEmployees = $employees->map(function ($employee) use ($workPlans) {
                $employeePlans = $workPlans->where('employee_id', $employee->id);

                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position ? $employee->position->name : 'N/A',
                    'rank' => $employee->rank,
                    'unit_work_plans' => $employeePlans->map(function ($plan) {
                        return [
                            'id' => $plan->id,
                            'category' => $plan->category,
                            'mfo' => $plan->mfo,
                            'output' => $plan->output,
                            'success_indicator' => $plan->success_indicator,
                            'required_output' => $plan->required_output,
                            'mode' => $plan->mode,
                            'core' => $plan->core ? json_decode($plan->core, true) : [],
                            'technical' => $plan->technical ? json_decode($plan->technical, true) : [],
                            'leadership' => $plan->leadership ? json_decode($plan->leadership, true) : [],
                            'standard_outcomes' => $plan->standard_outcomes ? json_decode($plan->standard_outcomes, true) : [],
                            'status' => $plan->status
                        ];
                    })->values()->toArray()
                ];
            });

            return response()->json($formattedEmployees);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
