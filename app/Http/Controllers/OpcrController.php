<?php

namespace App\Http\Controllers;

use App\Models\opcr;
use App\Models\Employee;
use App\Models\Unit_work_plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OpcrController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        // Get unique divisions with work plans for this office
        $divisions = Unit_work_plan::where('office_id', $user->office_id)
            ->select('division', 'target_period', 'year', 'status', DB::raw('MAX(created_at) as created_at'))
            ->groupBy('division', 'target_period', 'year', 'status')
            ->orderBy('division')
            ->orderBy('year')
            ->orderBy('target_period')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'division' => $item->division,
                    'targetPeriod' => $item->target_period . ' ' . $item->year,
                    'dateCreated' => $item->created_at->format('F j, Y'),
                    'status' => $item->status ?? 'Draft'
                ];
            });

        return response()->json($divisions);
    }

    public function getOfficeHeadFunctions($officeId)
    {
        // Get office head employee with position relationship
        $officeHead = Employee::with('position')
            ->where('office_id', $officeId)
            ->where(function ($query) {
                $query->where('rank', 'like', '%office-head%')
                    ->orWhereHas('position', function ($q) {
                        $q->where('name', 'like', '%head%');
                    });
            })
            ->first();

        if (!$officeHead) {
            return response()->json(['message' => 'Office head not found'], 404);
        }

        // Get the office head's work plans grouped by category
        $functions = Unit_work_plan::where('employee_id', $officeHead->id)
            ->select(
                'category',
                'mfo',
                'output',
                'success_indicator',
                'required_output',
                'core',
                'technical',
                'leadership',
                'standard_outcomes'
            )
            ->get()
            ->groupBy('category')
            ->map(function ($items, $category) {
                // For Strategic and Core functions, only include MFOs
                if (
                    str_starts_with($category, 'A. STRATEGIC FUNCTION') ||
                    str_starts_with($category, 'B. CORE FUNCTION')
                ) {
                    return [
                        'category' => $category,
                        'items' => $items->unique('mfo')->map(function ($item) {
                            return [
                                'mfo' => $item->mfo,
                                'core_competency' => json_decode($item->core, true),
                                'technical_competency' => json_decode($item->technical, true),
                                'leadership_competency' => json_decode($item->leadership, true)
                            ];
                        })->values()
                    ];
                }
                // For Support functions, only include Outputs
                else if (str_starts_with($category, 'C. SUPPORT FUNCTION')) {
                    return [
                        'category' => $category,
                        'items' => $items->unique('output')->map(function ($item) {
                            return [
                                'output' => $item->output,
                                'core_competency' => json_decode($item->core, true),
                                'technical_competency' => json_decode($item->technical, true),
                                'leadership_competency' => json_decode($item->leadership, true)
                            ];
                        })->values()
                    ];
                }
            });

        return response()->json([
            'strategic_function' => $functions->get('A. STRATEGIC FUNCTION', []),
            'core_function' => $functions->filter(function ($item, $key) {
                return str_starts_with($key, 'B. CORE FUNCTION');
            })->values(),
            'support_function' => $functions->get('C. SUPPORT FUNCTION', [])
        ]);
    }

    public function saveOpcr(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->office_id) {
            return response()->json(['message' => 'Unauthorized or no office assigned'], 403);
        }

        // Get office head employee
        $officeHead = Employee::where('office_id', $user->office_id)
            ->where(function ($query) {
                $query->where('rank', 'like', '%office-head%')
                    ->orWhereHas('position', function ($q) {
                        $q->where('name', 'like', '%head%');
                    });
            })
            ->first();

        if (!$officeHead) {
            return response()->json(['message' => 'Office head not found'], 404);
        }

        // Get the latest unit work plan for this office head
        $unitWorkPlan = Unit_work_plan::where('employee_id', $officeHead->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$unitWorkPlan) {
            return response()->json(['message' => 'No unit work plan found for office head'], 404);
        }

        $validated = $request->validate([
            'strategic_function' => 'required|array',
            'core_function' => 'required|array',
            'support_function' => 'required|array'
        ]);

        $opcr = Opcr::create([
            'office_id' => $user->office_id,
            'employee_id' => $officeHead->id,
            'target_period' => $unitWorkPlan->target_period,
            'year' => $unitWorkPlan->year,
            'strategic function' => json_encode($validated['strategic_function']),
            'core function' => json_encode($validated['core_function']),
            'support function' => json_encode($validated['support_function']),

            'status' => 'draft'
        ]);

        return response()->json([
            'message' => 'OPCR saved successfully',
            'data' => $opcr
        ]);
    }



}
