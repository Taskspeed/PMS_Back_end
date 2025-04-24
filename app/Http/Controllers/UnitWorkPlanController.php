<?php

namespace App\Http\Controllers;

use App\Models\Mfo;
use App\Models\Core;
use App\Models\Employee;
use App\Models\F_outpot;
use App\Models\Position;
use App\Models\Technical;
use App\Models\F_category;
use App\Models\Leadership;
use Illuminate\Http\Request;
use App\Models\Unit_work_plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class UnitWorkPlanController extends Controller
{
    public function index()
    {
        $user = Employee::all();

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $user
        ]);
    }

    public function unit_work_plan()
    {
        $user = Unit_work_plan::all();

        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $user
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        $validated = $request->validate([
            'office_id' => 'required|exists:offices,id',
            'division' => 'required|string',
            'target_period' => 'required|string',
            'year' => 'required|integer',
            'employee_work_plans' => 'required|array',
            'employee_work_plans.*.employee_id' => 'required|exists:employees,id',
            'employee_work_plans.*.rank' => 'required|string',
            'employee_work_plans.*.position' => 'required|string',
            'employee_work_plans.*.performance_standards' => 'required|array',
            'employee_work_plans.*.performance_standards.*.category' => 'nullable|string',
            'employee_work_plans.*.performance_standards.*.mfo' => 'nullable|string',
            'employee_work_plans.*.performance_standards.*.output' => 'nullable|string',
            'employee_work_plans.*.performance_standards.*.success_indicator' => 'required|string',
            'employee_work_plans.*.performance_standards.*.required_output' => 'required|string',
            'employee_work_plans.*.performance_standards.*.standard_outcomes' => 'required|array',
            'employee_work_plans.*.performance_standards.*.standard_outcomes.*.rating' => 'required|string',
            'employee_work_plans.*.performance_standards.*.standard_outcomes.*.quantity' => 'required|string',
            'employee_work_plans.*.performance_standards.*.standard_outcomes.*.effectiveness' => 'required|string',
            'employee_work_plans.*.performance_standards.*.standard_outcomes.*.timeliness' => 'required|string',
            'employee_work_plans.*.performance_standards.*.core_competency' => 'nullable|array',
            'employee_work_plans.*.performance_standards.*.technical_competency' => 'nullable|array',
            'employee_work_plans.*.performance_standards.*.leadership_competency' => 'nullable|array',
        ]);


        // Log the validated data
        Log::info('Validated Data:', $validated);

        $savedPlans = [];

        foreach ($validated['employee_work_plans'] as $workPlan) {
            foreach ($workPlan['performance_standards'] as $standard) {
                // Debug each standard
                Log::info('Processing Standard:', [
                    'category' => $standard['category'] ?? null,
                    'core_competency' => $standard['core_competency'] ?? null,
                    'technical_competency' => $standard['technical_competency'] ?? null,
                    'leadership_competency' => $standard['leadership_competency'] ?? null
                ]);

                $planData = [
                    'office_id' => $validated['office_id'],
                    'division' => $validated['division'],
                    'target_period' => $validated['target_period'],
                    'year' => $validated['year'],
                    'employee_id' => $workPlan['employee_id'],
                    'rank' => $workPlan['rank'],
                    'position' => $workPlan['position'],
                    'category' => $standard['category'] ?? null,
                    'mfo' => $standard['mfo'] ?? null,
                    'output' => $standard['output'] ?? null,
                    'success_indicator' => $standard['success_indicator'],
                    'required_output' => $standard['required_output'],
                    'standard_outcomes' => json_encode($standard['standard_outcomes']),
                    'core' => isset($standard['core_competency']) ? json_encode($standard['core_competency']) : null,
                    'technical' => isset($standard['technical_competency']) ? json_encode($standard['technical_competency']) : null,
                    'leadership' => isset($standard['leadership_competency']) ? json_encode($standard['leadership_competency']) : null,
                ];

                Log::info('Plan Data Before Create:', $planData);

                $plan = Unit_work_plan::create($planData);
                $savedPlans[] = $plan;
            }
        }

        return response()->json([
            'message' => 'Unit work plans created successfully',
            'data' => $savedPlans,
            'debug' => [
                'received_core' => $validated['employee_work_plans'][0]['performance_standards'][0]['core_competency'] ?? null,
                'received_technical' => $validated['employee_work_plans'][0]['performance_standards'][0]['technical_competency'] ?? null,
                'received_leadership' => $validated['employee_work_plans'][0]['performance_standards'][0]['leadership_competency'] ?? null,
            ]
        ], 201);
    }
    // Helper method to format competency data
    protected function formatCompetencyData($competencyData)
    {
        if (is_array($competencyData)) {
            return $competencyData;
        }

        // If it's an object, convert to array
        if (is_object($competencyData)) {
            return (array)$competencyData;
        }

        // If it's a JSON string, decode it
        if (is_string($competencyData)) {
            return json_decode($competencyData, true) ?? [];
        }

        return [];
    }

    public function getDivisionsByOffice(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        $divisions = Employee::where('office_id', $user->office_id)
            ->whereNotNull('division')
            ->select('division')
            ->distinct()
            ->pluck('division');

        return response()->json($divisions);
    }


    public function getEmployeesByDivision(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        $request->validate([
            'division' => 'required|string'
        ]);

        // Debug logging
        Log::info('Fetching employees for office: ' . $user->office_id . ' and division: ' . $request->division);

        // $employees = Employee::where('office_id', $user->office_id)
        //     ->where('division', $request->division)
        //     ->select('id', 'name', 'position', 'rank')
        //     ->get();
        $employees = Employee::select([
            'employees.id',
            'employees.name',
            'positions.name as position', // Get position name from positions table
            'employees.rank'
        ])
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->where('office_id', $user->office_id)
            ->where('division', $request->division)
            ->whereNull('deleted_at')
            ->get();

        Log::info('Found employees: ' . $employees->count());

        return response()->json($employees);
    }


    public function getMfosByCategory(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        $request->validate([
            'category_id' => 'required|numeric|exists:f_categories,id'
        ]);

        $category = F_category::find($request->category_id);

        // For support functions, we'll skip MFOs and just return outputs
        if ($category->name === 'C. SUPPORT FUNCTION') {
            $supportOutputs = F_outpot::where('f_category_id', $request->category_id)
                ->where('office_id', $user->office_id)
                ->whereNull('mfo_id')
                ->whereNull('deleted_at')
                ->get(['id', 'name']);

            return response()->json([
                'mfos' => [], // Empty array for MFOs
                'support_outputs' => $supportOutputs,
                'skip_mfo' => true  // Flag to skip MFO selection
            ]);
        }

        // For other categories, return MFOs with their outputs
        $mfos = Mfo::with(['outpots' => function ($query) {
            $query->whereNull('deleted_at');
        }])
            ->where('office_id', $user->office_id)
            ->where('f_category_id', $request->category_id)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($mfo) {
                return [
                    'id' => $mfo->id,
                    'name' => $mfo->name,
                    'outputs' => $mfo->outpots->map(function ($output) {
                        return [
                            'id' => $output->id,
                            'name' => $output->name
                        ];
                    })->toArray()
                ];
            });

        return response()->json([
            'mfos' => $mfos,
            'support_outputs' => [],
            'skip_mfo' => false
        ]);
    }

public function getSupportOutputs(Request $request)
{
    $user = Auth::user();
    if (!$user || !$user->office_id) {
        return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
    }

    $request->validate([
        'category_id' => 'required|exists:f_categories,id'
    ]);

    // Get support outputs for this category and office
    $outputs = F_outpot::where('f_category_id', $request->category_id)
        ->where('office_id', $user->office_id)
        ->whereNull('mfo_id')
        ->whereNull('deleted_at')
        ->get(['id', 'name']);

    return response()->json($outputs);
}
    public function getOutputsByMfo(Request $request)
    {
        $request->validate([
            'mfo_id' => 'required|exists:mfos,id'
        ]);

        $outputs = F_outpot::where('mfo_id', $request->mfo_id)
            ->whereNull('deleted_at')
            ->get(['id', 'name']);

        return response()->json($outputs);
    }

    public function category()   {
        $data = F_category::all();
        return response()->json($data);
    }




    public function SupportOutputs(Request $request)
    {
        // Get the office_id from the request
        $office_id = $request->input('office_id');

        // Fetch outputs that are support functions (category C) for the specific office
        $outputs = F_outpot::whereHas('category', function ($query) {
            $query->where('name', 'like', '%SUPPORT%')
                ->orWhere('name', 'like', 'C.%');
        })
            ->when($office_id, function ($query) use ($office_id) {
                $query->where('office_id', $office_id);
            })
            ->get();

        return response()->json($outputs);
    }


    //Competencies

    // New method to get position with competencies
    // Legend mapping
    private $legend = [
        0 => 'Not Applicable',
        1 => 'Basic',
        2 => 'Intermediate',
        3 => 'Advanced',
        4 => 'Superior'
    ];



    public function getEmployeeCompetencies($employeeId)
    {
        try {
            // Get the employee with position relationship
            $employee = Employee::with('position.core', 'position.technical', 'position.leadership')
                ->findOrFail($employeeId);

            if (!$employee->position) {
                return response()->json([
                    'status' => 404,
                    'message' => 'No position assigned to this employee'
                ], 404);
            }

            // Legend mapping
            $legend = [
                0 => 'Not Applicable',
                1 => 'Basic',
                2 => 'Intermediate',
                3 => 'Advanced',
                4 => 'Superior'
            ];

            // Format core competencies
            $coreCompetencies = [];
            if ($employee->position->core) {
                $coreAttributes = $employee->position->core->getAttributes();
                foreach ($coreAttributes as $key => $value) {
                    if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                        $coreCompetencies[$key] = [
                            'value' => (string)$value,
                            'legend' => $legend[$value] ?? 'Not Applicable'
                        ];
                    }
                }
            }

            // Format technical competencies
            $technicalCompetencies = [];
            if ($employee->position->technical) {
                $techAttributes = $employee->position->technical->getAttributes();
                foreach ($techAttributes as $key => $value) {
                    if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                        $technicalCompetencies[$key] = [
                            'value' => (string)$value,
                            'legend' => $legend[$value] ?? 'Not Applicable'
                        ];
                    }
                }
            }

            // Format leadership competencies
            $leadershipCompetencies = [];
            if ($employee->position->leadership) {
                $leaderAttributes = $employee->position->leadership->getAttributes();
                foreach ($leaderAttributes as $key => $value) {
                    if (!in_array($key, ['id', 'created_at', 'updated_at'])) {
                        $leadershipCompetencies[$key] = [
                            'value' => (string)$value,
                            'legend' => $legend[$value] ?? 'Not Applicable'
                        ];
                    }
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => [
                    'position' => $employee->position->name,
                    'core' => $coreCompetencies,
                    'technical' => $technicalCompetencies,
                    'leadership' => $leadershipCompetencies
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching competencies: ' . $e->getMessage()
            ], 500);
        }
    }

    // Division-target Period-Date Created-Status
    public function get_division_status(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        // Get all unique division/target period/year combinations
        $data = Unit_work_plan::where('office_id', $user->office_id)
            ->select(
                'division',
                'target_period',
                'year',
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(status) as status') // Or use whatever logic makes sense for status
            )
            ->groupBy('division', 'target_period', 'year')
            ->orderBy('division')
            ->orderBy('year')
            ->orderBy('target_period')
            ->get()
            ->map(function ($item) {
                return [
                    'division' => $item->division,
                    'target_period' => $item->target_period . ' ' . $item->year,
                    'created_at' => $item->created_at,
                    'status' => $item->status
                ];
            });

        return response()->json($data);
    }

    public function get_employee_performance(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        $request->validate([
            'division' => 'required|string',
            'target_period' => 'required|string' // Format: "January - June 2025"
        ]);

        // Split the target period into period and year more reliably
        $parts = preg_split('/\s+/', $request->target_period);

        // The last part is the year
        $year = end($parts);

        // The period is everything except the last part
        array_pop($parts);
        $period = implode(' ', $parts);

        // Validate the year is numeric
        if (!is_numeric($year)) {
            return response()->json(['message' => 'Invalid target period format'], 400);
        }

        $data = Unit_work_plan::with('employee')
            ->where('office_id', $user->office_id)
            ->where('division', $request->division)
            ->where('target_period', $period)
            ->where('year', $year)
            ->get();

        $result = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'employee_name' => $item->employee->name ?? null,
                'position' => $item->position,
                'rank' => $item->rank,
                'status' => $item->status,
                'category'=>$item->category,
                'mfo'=>$item->mfo,
                'output'=>$item->output,
                'performance_standards' => [
                    'success_indicator' => $item->success_indicator,
                    'required_output' => $item->required_output,
                    'standard_outcomes' => json_decode($item->standard_outcomes, true),
                    'core_competency' => json_decode($item->core, true),
                    'technical_competency' => json_decode($item->technical, true),
                    'leadership_competency' => json_decode($item->leadership, true)
                ]
            ];
        });

        return response()->json($result);
    }

    public function updateEmployee(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'rank' => 'required|string',
            'position' => 'required|string',
            'performance_standards' => 'required|array',
            'performance_standards.*.category' => 'nullable|string',
            'performance_standards.*.mfo' => 'nullable|string',
            'performance_standards.*.output' => 'nullable|string',
            'performance_standards.*.success_indicator' => 'required|string',
            'performance_standards.*.required_output' => 'required|string',
            'performance_standards.*.standard_outcomes' => 'required|array',
            'performance_standards.*.core_competency' => 'nullable|array',
            'performance_standards.*.technical_competency' => 'nullable|array',
            'performance_standards.*.leadership_competency' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Update employee basic info if needed
            $employee = Employee::find($id);
            if ($employee) {
                $employee->update([
                    'rank' => $validated['rank'],
                    'position' => $validated['position']
                ]);
            }

            // Update or create work plan records
            foreach ($validated['performance_standards'] as $standard) {
                Unit_work_plan::updateOrCreate(
                    [
                        'employee_id' => $id,
                        'category' => $standard['category'],
                        'mfo' => $standard['mfo'],
                        'output' => $standard['output']
                    ],
                    [
                        'success_indicator' => $standard['success_indicator'],
                        'required_output' => $standard['required_output'],
                        'standard_outcomes' => json_encode($standard['standard_outcomes']),
                        'core' => isset($standard['core_competency']) ? json_encode($standard['core_competency']) : null,
                        'technical' => isset($standard['technical_competency']) ? json_encode($standard['technical_competency']) : null,
                        'leadership' => isset($standard['leadership_competency']) ? json_encode($standard['leadership_competency']) : null
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Employee MFO details updated successfully',
                'employee' => $employee
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update employee MFO details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
