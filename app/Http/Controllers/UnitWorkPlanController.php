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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Output\Output;

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

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        $validated = $request->validate([
            'division' => 'required|string',
            'employee_id' => 'required|exists:employees,id',
            'rank' => 'required|string',
            'position' => 'required|string',
            'target_period' => 'required|string',
            'year' => 'required|integer',
            'performance_standards' => 'required|array',
            'performance_standards.*.core' => 'required|array',
            'performance_standards.*.technical' => 'required|array',
            'performance_standards.*.leadership' => 'required|array',
            'performance_standards.*.success_indicator' => 'required|string',
            'performance_standards.*.required_output' => 'required|string'
        ]);

        $workPlan = Unit_work_plan::create([
            'office_id' => $user->office_id,
            'division' => $validated['division'],
            'target_period' => $validated['target_period'],
            'year' => $validated['year'],
            'core' => $validated['performance_standards'][0]['core'],
            'technical' => $validated['performance_standards'][0]['technical'],
            'leadership' => $validated['performance_standards'][0]['leadership'],
            'success_indicator' => $validated['performance_standards'][0]['success_indicator'],
            'required_output' => $validated['performance_standards'][0]['required_output'],
            'employee_id' => $validated['employee_id']
        ]);

        // Handle additional performance standards if any
        if (count($validated['performance_standards']) > 1) {
            for ($i = 1; $i < count($validated['performance_standards']); $i++) {
                Unit_work_plan::create([
                    'office_id' => $user->office_id,
                    'division' => $validated['division'],
                    'target_period' => $validated['target_period'],
                    'year' => $validated['year'],
                    'core' => $validated['performance_standards'][$i]['core'],
                    'technical' => $validated['performance_standards'][$i]['technical'],
                    'leadership' => $validated['performance_standards'][$i]['leadership'],
                    'success_indicator' => $validated['performance_standards'][$i]['success_indicator'],
                    'required_output' => $validated['performance_standards'][$i]['required_output'],
                    'employee_id' => $validated['employee_id']
                ]);
            }
        }

        return response()->json(['message' => 'Unit work plan created successfully', 'data' => $workPlan]);
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



    public function core()
    {
        $core = Core::all();
        return response()->json($core);
    }

    public function technical()
    {
        $Technical = Technical::all();
        return response()->json($Technical);
    }

    public function leadership()
    {
        $leadership = Leadership::all();
        return response()->json($leadership);
    }

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

}
