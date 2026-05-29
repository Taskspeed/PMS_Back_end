<?php

namespace App\Http\Controllers\Erms;

use App\Http\Controllers\Controller;
use App\Http\Requests\performanceRatingStoreRequest;
use App\Models\PerformanceRating;
use App\Models\TargetPeriod;
use App\Services\PerformanceRatingService;
use App\Services\TargetPeriodService;



class EmployeeRatingController extends Controller
{

    // service
    protected TargetPeriodService $targetperiodService;
    

    public function __construct(TargetPeriodService $targetperiodService)
    {
        return $this->targetperiodService = $targetperiodService;
    }

    // target period of employee
    public function targetPeriodEmployee(string $controlNo)
    {

        $result = $this->targetperiodService->targetPeriod($controlNo);

        return $result;
    }

    //  get the target peroid details the performance standard and standard outcome
    public function targetPeriodDetails(int $targetPeriodId)
    {
        
        $result = $this->targetperiodService->targetPeriodDetails($targetPeriodId);
    
        return $result;
    
    }

    //  get the target peroid details the performance standard and standard outcome
    public function targetPeriod(int $targetPeriodId, $month = null, $year = null,$week= null)
    {

        $data = $this->targetperiodService->getTargetPeriodWithStandardsAndRatings($targetPeriodId,$month,$year,$week);

        return $data;
    }

    // employee store his rate
    public function performanceRating(performanceRatingStoreRequest $request, PerformanceRatingService $performanceRatingService)
    {
        $validated = $request->validated();

        $rating = $performanceRatingService->performanceRatingStore($validated);

        return response()->json([
            'status' => true,
            'message' => 'Rate(s) successfully saved',
            'rates' => $rating
        ]);
    }

    // get the list of the employee the rate of date
    public function getListOfRatingEmployee(string $controlNo)
    {

        $list = PerformanceRating::select(
            'id',
            'performance_standard_id',
            // 'control_no',
            'date'
        )
            ->where('control_no', $controlNo)
            ->orderBy('date', 'asc')
            ->get();

        if ($list->isEmpty()) {
            return response()->json([
                'message' => 'Employee does not have ratings yet'
            ], 404);
        }

        return response()->json($list, 200);
    }

    //performance rating record
    public function performanceRatingRecord(int $targetPeriodId)
    {

        $record = $this->targetperiodService->performanceRatingRecord($targetPeriodId);

        return $record;
    
    }
}
