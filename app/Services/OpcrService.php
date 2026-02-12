<?php

namespace App\Services;

use App\Models\opcr;
use App\Models\Employee;
use App\Http\Requests\opcrRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OpcrService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }
    public function opcrOfficeHead($controlNo, $semester, $year){

        $officeHeadOpcr = Employee::select('id', 'ControlNo', 'name')
            ->where('ControlNo', $controlNo)

            ->whereHas('targetPeriods', function ($q) use ($year, $semester) {
                $q->where('year', $year)
                    ->where('semester', $semester);
            })

            ->with([
                'targetPeriods' => function ($queryTargetPeriod) use ($year, $semester) {
                    $queryTargetPeriod
                        ->select('id', 'control_no', 'semester', 'year', 'status')
                        ->where('year', $year)
                        ->where('semester', $semester)
                        ->with([
                            'performanceStandards' => function ($queryPerformanceStandard) {
                                $queryPerformanceStandard->select(
                                    'id',
                                    'target_period_id',
                                    'category',
                                    'mfo',
                                    'output',
                                    'success_indicator',
                                    'core',
                                    'technical',
                                    'leadership',
                                )
                                    ->with([
                                        'opcr' => function ($queryopcr) {
                                            $queryopcr->select(
                                                'id',
                                                'performance_standard_id', // REQUIRED FK
                                                'competency',
                                                'budget',
                                                'accountable',
                                                'accomplishment',
                                                'rating_q',
                                                'rating_e',
                                                'rating_t',
                                                'rating_a',
                                                'profiency',
                                                'remarks',

                                            );
                                        }
                                    ]);
                            }
                        ]);
                }
            ])
            ->first();

            return $officeHeadOpcr;
    }


    public function storeAllotedBudget($validatedData)
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        DB::beginTransaction();

        try {

            $records = [];

            foreach ($validatedData as $data) {

                $records[] = Opcr::create([
                    'office_id' => $officeId,
                    'performance_standard_id' => $data['performance_standard_id'],
                    'budget' => $data['budget'],
                    'accountable' => $data['accountable'],
                    'accomplishment' => $data['accomplishment'],
                    // 'remarks' => $data['remarks'],
                ]);
            }

            DB::commit();

            return $records;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
