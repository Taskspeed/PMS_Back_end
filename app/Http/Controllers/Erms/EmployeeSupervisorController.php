<?php

namespace App\Http\Controllers\Erms;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\EmployeeSupervisorService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class EmployeeSupervisorController extends Controller
{
    // response format
    use ApiResponseTrait;

    protected EmployeeSupervisorService $employeeSupervisorService;


    // services
    public function __construct(EmployeeSupervisorService $employeeSupervisorService)
    {
        $this->employeeSupervisorService = $employeeSupervisorService;
    }

    // get employee information
    public function employeeInformation(string $controlNo){

        $employee = Employee::select('id','ControlNo','name','job_title')->where('ControlNo',$controlNo)->first();

        if(!$employee){
           return $this->errorMessage('not found',404);
        }
        return $this->successMessage($employee,'Successfully fetch',200);

    }


    // get my Supervisor and managerial
    public function getMySupervisor(Request $request)
    {

        $controlNo     = $request->input('controlNo');
        $year     = $request->input('year');
        $semester     = $request->input('semester');


        try {
            $result = $this->employeeSupervisorService->getListOfEmployeeBaseOnSupervisor($year, $semester, $controlNo);
            return $this->successMessage($result, 'Successfully', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        // $data =  $this->employeeSupervisorService->getListOfEmployeeBaseOnSupervisor($year, $semester, $controlNo);

        // return $this->successMessage($data, 'Successfully', 200);
    }
}
