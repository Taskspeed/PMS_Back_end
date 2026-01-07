<?php

namespace App\Http\Controllers\office;


use App\Models\Employee;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controller as BaseController;

class IpcrController extends BaseController
{
    protected $user;
    protected $officeId;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->officeId = $this->user->office_id;

            return $next($request);
        });
    }


    // getting the ipcr of the employee based on controlno and year
    public function getIpcr($controlNo, $year, $semester)
    {
        $employee = Employee::where('ControlNo', $controlNo)
            ->where('office_id', $this->officeId) // ✅ OFFICE RESTRICTION
            ->with([
                'targetPeriods' => function ($q) use ($year, $semester) {
                    $q->where('year', $year)
                        ->where('semester', $semester)
                        ->with(['performanceStandards', 'standardOutcomes']);
                }
            ])
            ->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found or access denied'
            ], 404);
        }

        return response()->json($employee);
    }


    // approving the ipcr of the employee
    public function approveIpcrEmployee($controlNo,  $semester, $year, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approve,reject,review',
        ]);

        // Get employee with office restriction
        $employee = Employee::where('ControlNo', $controlNo)
            ->where('office_id', $this->officeId)
            ->first();

        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found or access denied'
            ], 404);
        }

        // Get the target period
        $targetPeriod = $employee->targetPeriods()
            ->where('year', $year)
            ->where('semester', $semester)
            ->first();

        if (!$targetPeriod) {
            return response()->json([
                'message' => 'Target period not found'
            ], 404);
        }

        // Update only the target period
        $targetPeriod->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'IPCR status updated successfully.',
            'data' => $targetPeriod,
        ]);
    }
}
