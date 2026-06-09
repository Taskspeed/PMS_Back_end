<?php

namespace App\Services\Erms;

use App\Models\Employee;
use App\Models\TargetPeriod;

class ErmsUnitWorkPlanService
{
   
    public function supervisoryDeductionOfSuccessIndicator(int $year, string $semester, string $mfo, int $officeId )
    {

        // Get the managerial (office head) of this office
        $managerial = Employee::where('job_title', 'Office Head')
            ->where('office_id', $officeId)
            ->first();

        if (!$managerial) {
            return response()->json([
                'success' => false,
                'message' => 'No managerial employee found.'
            ], 404);
        }

        // Get the managerial's target period
        $targetPeriod = TargetPeriod::with('performanceStandards')
            ->where('control_no', $managerial->ControlNo)
            ->where('year', $year)
            ->where('semester', $semester)
            ->first();

        if (!$targetPeriod) {
            return response()->json([
                'success' => false,
                'message' => 'No target period found for this managerial.'
            ], 404);
        }

        // Get ALL target periods in this office for this year/semester (excluding office head)
        $allOtherTargetPeriods = TargetPeriod::with('performanceStandards')
            ->where('office_id', $officeId)
            ->where('year', $year)
            ->where('semester', $semester)
            ->where('control_no', '!=', $managerial->ControlNo)
            ->get();

        // Get all employees in this office for name/rank/job_title lookup
        $allEmployees = Employee::where('office_id',$officeId)->get()->keyBy('ControlNo');

        /**
         * Sum the total targets of all performance standards that:
         * - belong to a direct report of $controlNo (matched via standard->supervisory_control_no)
         * - match the given $mfoKey
         */
        $getTotalClaimed = function (string $controlNo, string $mfoKey) use (
            $allOtherTargetPeriods
        ) {
            $claimed = 0;

            foreach ($allOtherTargetPeriods as $reportPeriod) {
                // Filter standards where:
                // 1. mfo matches
                // 2. supervisory_control_no on the standard points to $controlNo
                $matchedStandards = $reportPeriod->performanceStandards->filter(
                    fn($s) => $s->mfo === $mfoKey
                        && $s->supervisory_control_no === $controlNo
                );

                foreach ($matchedStandards as $standard) {
                    $claimed += $this->extractNumber($standard->success_indicator);
                }
            }

            return $claimed;
        };

        // Build managerial MFOs — claimed = sum of subordinates' standards pointing to this managerial
        $standards = $mfo
            ? $targetPeriod->performanceStandards->where('mfo', $mfo)
            : $targetPeriod->performanceStandards;

        $result = $standards->map(function ($standard) use ($getTotalClaimed, $managerial) {
            $totalTarget = $this->extractNumber($standard->success_indicator);
            $claimed     = $getTotalClaimed($managerial->ControlNo, $standard->mfo);
            $available   = $totalTarget - $claimed;

            return [
                'category'              => $standard->category,
                'mfo'                   => $standard->mfo,
                'output'                => $standard->output,
                'output_name'           => $standard->output_name,
                'performance_indicator' => $standard->performance_indicator,
                'success_indicator'     => $standard->success_indicator,
                'total_target'          => $totalTarget,
                'claimed'               => $claimed,
                'available'             => max(0, $available),
            ];
        });

        // Build subordinates list
        $subordinatesData = $allOtherTargetPeriods->map(function ($tp) use (
            $allEmployees,
            $getTotalClaimed,
            $mfo
        ) {
            $employee = $allEmployees->get($tp->control_no);

            $standards = $mfo
                ? $tp->performanceStandards->where('mfo', $mfo)
                : $tp->performanceStandards;

            if ($standards->isEmpty()) {
                return [
                    'controlNo'  => $tp->control_no,
                    'name'       => $employee?->name,
                    'rank'       => $employee?->rank,
                    'job_title'  => $employee?->job_title,
                    'mfos'       => null,
                ];
            }

            $mfos = $standards->map(function ($standard) use ($tp, $getTotalClaimed) {
                $totalTarget = $this->extractNumber($standard->success_indicator);

                // Claimed = standards from others that point to THIS person's control_no
                $claimed   = $getTotalClaimed($tp->control_no, $standard->mfo);
                $available = $totalTarget - $claimed;

                return [
                    'category'               => $standard->category,
                    'mfo'                    => $standard->mfo,
                    'output'                 => $standard->output,
                    'output_name'            => $standard->output_name,
                    'performance_indicator'  => $standard->performance_indicator,
                    'success_indicator'      => $standard->success_indicator,
                    'supervisory_control_no' => $standard->supervisory_control_no,
                    'total_target'           => $totalTarget,
                    'claimed'                => $claimed,
                    'available'              => max(0, $available),
                ];
            });

            return [
                'controlNo'  => $tp->control_no,
                'name'       => $employee?->name,
                'rank'       => $employee?->rank,
                'job_title'  => $employee?->job_title,
                'mfos'       => $mfos->values(),
            ];
        });

        return response()->json([
            'controlNo'     => $managerial->ControlNo,
            'name'          => $managerial->name,
            'rank'          => $managerial->rank,
            'job_title'     => $managerial->job_title,
            'office'        => $managerial->office,
            'year'          => $year,
            'semester'      => $semester,
            'mfos'          => $result->values(),
            'supervisories' => $subordinatesData->filter(function ($subordinate) use ($allEmployees) {
                $emp = $allEmployees->get($subordinate['controlNo']);
                return $emp && $emp->job_title !== 'Employee';
            })->values(),
        ], 200);
    }

    private function extractNumber(string $string): int
    {
        preg_match('/^\d+/', trim($string), $matches);
        return isset($matches[0]) ? (int) $matches[0] : 0;
    }
}
