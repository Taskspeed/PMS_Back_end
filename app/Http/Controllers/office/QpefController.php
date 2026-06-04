<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Http\Requests\qpefRequest;
use App\Http\Resources\QpefResource;
use App\Services\QpefService;
use Illuminate\Http\Request;

class QpefController extends Controller
{

    protected QpefService $qpefService;

    public function __construct(QpefService $qpefService)
    {
        $this->qpefService = $qpefService;
    }

    // storing qpef of employee
    public function qpefStore(qpefRequest $request)
    {

        $validated = $request->validated();

        try {
            $qpef = $this->qpefService->createQpef($validated);

            return response()->json([
                'message' => 'QPEF created successfully',
                'data' => $qpef->load([
                    'jobPerformances',
                    'competenciesAttitudes',
                    'physicalMentals',
                    'recommendationDevelopment'
                ])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating QPEF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // updating qpef of employee
    public function qpefUpdate(int $qpefId, qpefRequest $request)
    {

        $validated = $request->validated();


        try {
            $qpef =  $this->qpefService->updateQpef($qpefId, $validated);

            return response()->json([
                'message' => 'QPEF update successfully',
                'data' => $qpef->load([
                    'jobPerformances',
                    'competenciesAttitudes',
                    'physicalMentals',
                    'recommendationDevelopment'
                ])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating QPEF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // employee qpef
    public function employeeQpef(string $control_no, string $quarterly, int $year)
    {
        $qpef =  $this->qpefService->getEmployeeQpef($control_no, $quarterly, $year);

        if (!$qpef) {
            return response()->json([
                'message' => 'Employee does not have QPEF yet.'
            ], 404);
        }

        return new QpefResource($qpef);
    }

    //
    public function employeeQpefAllQuarter(string $control_no, int $year,)
    {
        $qpef = $this->qpefService->fetchAllEmployeeQpef($control_no, $year);

        if ($qpef->isEmpty()) {
            return response()->json([
                'message' => 'Employee does not have QPEF yet.'
            ], 404);
        }


        return  response()->json($qpef);
    }

    // get multiple qpef of employee
    public function getAllEmployeeQpefQuater(Request $request, QpefService $QpefService)
    {
        $validated = $request->validate([
            'controlNo'   => 'required|array',
            'controlNo.*' => 'required|string',
            'quarter'     => 'required|string|min:2',
            'year'        => 'required|date_format:Y',
        ]);

        // Check which controlNo don't have QPEF records
        $qpef = $QpefService->getAllEmployeeQpefQuarter($validated);

        $foundControlNos = $qpef->pluck('control_no')->toArray();

        $missingControlNos = array_diff($validated['controlNo'], $foundControlNos);

        if (!empty($missingControlNos)) {
            return response()->json([
                'message' => 'Some employees do not have QPEF yet.',
                'error' => array_values($missingControlNos), // ✅ shows which ones are missing
            ], 404);
        }

        return QpefResource::collection($qpef);
    }
}
