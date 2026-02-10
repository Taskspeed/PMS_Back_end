<?php

namespace App\Http\Controllers;

use App\Http\Requests\opcrRequest;
use App\Models\opcr;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
class OpcrController extends BaseController
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
    // public function getOpcr($controlNo,$year,$semester) //
    // {
    //     $employeeOpcr = Employee::select('id', 'ControlNo', 'name')
    //         ->where('ControlNo', $controlNo)
    //         ->with([
    //             'targetPeriods' => function ($queryTargetPeriod) use($year, $semester) {
    //                 $queryTargetPeriod
    //                     ->select('id', 'control_no', 'semester', 'year', 'status')
    //                     ->where('semester',$semester)
    //                     ->where('year',$year)
    //                     ->with([
    //                         'performanceStandards' => function ($queryPerformanceStandard) {
    //                             $queryPerformanceStandard
    //                                 ->select(
    //                                     'id',
    //                                     'target_period_id', // REQUIRED FK
    //                                     'category',
    //                                     'mfo',
    //                                     'output',
    //                                     // 'output_name',
    //                                     // 'performance_indicator',
    //                                     'success_indicator',
    //                                     // 'required_output'
    //                                 );
                                    // ->with([
                                    //     'standardOutcomes' => function ($queryStandardOutcomes) {
                                    //         $queryStandardOutcomes->select(
                                    //             'id',
                                    //             'performance_standard_id', // REQUIRED FK
                                    //             'rating',
                                    //             'quantity_target',
                                    //             'effectiveness_criteria',
                                    //             'timeliness_range'
                                    //         );
                                    //     }
                                    // ]);
    //                         }
    //                     ]);
    //             }
    //         ])
    //         ->first();

    //     return response()->json($employeeOpcr);
    // }

    public function getOpcr($controlNo, $semester, $year) // get the opcr of the office head
    {
        $employeeOpcr = Employee::select('id', 'ControlNo', 'name')
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

        if (!$employeeOpcr) {
            return response()->json([
                'message' => 'No OPCR found for the specified year and semester.'
            ], 404);
        }

        return response()->json($employeeOpcr);
    }

        // save the opcr
    public function storeOpcr(opcrRequest $request){

      // validated
      $validated = $request->validated();

      // create
      $opcr  = opcr::create([
            'office_id' => $this->officeId,
            'performance_standard_id' => $validated['performance_standard_id'],
            'compentency' => $validated['compentency'],
            'budget' => $validated['budget'],
            'accountable' => $validated['accountable'],
            'accomplishment' => $validated['accomplishment'],
            'rating_q' => $validated['rating_q'],
            'rating_e' => $validated['rating_e'],
            'rating_t' => $validated['rating_t'],
            'rating_a' => $validated['rating_a'],
            'profiency' => $validated['profiency'],
            'remarks' => $validated['remarks'],

        ]);

      return response()->json($opcr);


    }
}
