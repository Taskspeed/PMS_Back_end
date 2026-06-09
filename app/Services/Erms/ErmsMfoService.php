<?php

namespace App\Services\Erms;

use App\Models\Employee;
use App\Models\TargetPeriod;
use Illuminate\Http\Request;

class ErmsMfoService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    
//------------------------------------------------------------ ERM MFO ----------------------------------------------------\\

        public function getErmsMfo( int $officeId, Request $request)
    {
        // $user = Auth::user();
        $office = \App\Models\Office::find($officeId);

        if (!$office) {
            return ['error' => 'Office not found'];
        }

        // Get all categories having either MFOs or outputs for this office
        $categoryIds = $office->mfos()->pluck('f_category_id')
            ->merge($office->f_outpots()->pluck('f_category_id'))
            ->unique()
            ->toArray();

        $categories = \App\Models\F_category::whereIn('id', $categoryIds)->get();

        $result = [
            'categories' => []
        ];

        foreach ($categories as $category) {
            // MFOs for this office and category
            $mfos = $office->mfos()->where('f_category_id', $category->id)->get();
            $mfosArr = [];
            foreach ($mfos as $mfo) {
                $mfosArr[] = [
                    'id' => $mfo->id,
                    'name' => $mfo->name,
                    'outputs' => $mfo->outpots()->get()->toArray(),
                ];
            }

            // Outputs directly under category+office (not attached to any MFO)
            $outputs = $office->f_outpots()
                ->where('f_category_id', $category->id)
                ->whereNull('mfo_id')
                ->get();

            $result['categories'][] = [
                'id' => $category->id,
                'name' => $category->name,
                'mfos' => $mfosArr,
                'category_outputs' => $outputs,
            ];
        }

        return $result;
    }


    // get the all mfo of the office head
    public function getOfficeMfo(string $semester, int $year, int $officeId)
    {
        // $user = Auth::user();

        $employee = Employee::select('ControlNo', 'name', 'office', 'job_title', 'office_id',)
        ->where('office_id', $officeId)
        ->where('job_title', 'Office Head')->first();


        if (!$employee) {
            return response()->json([
                'message' => 'Office Head not found'
            ], 404);
        }

        $target_period = TargetPeriod::with('performanceStandards:id,category,mfo,target_period_id')->select('id','control_no','semester','year')
            ->where('control_no', $employee->ControlNo)
            ->where('semester', $semester)
            ->where('year', $year)
            ->first();

        return response()->json([
            'employee' => $employee,
            'target_period' => $target_period
        ]);
    }
}
