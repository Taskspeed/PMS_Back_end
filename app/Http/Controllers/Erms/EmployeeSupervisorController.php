<?php

namespace App\Http\Controllers\Erms;

use App\Http\Controllers\Controller;
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


    // get my Supervisor and managerial
    public function getMySupervisor(Request $request){

        $controlNo     = $request->input('controlNo');
        $year      = $request->input('year');
        $semester  = $request->input('semester');

        $data =  $this->employeeSupervisorService->getListOfEmployeeBaseOnSupervisor($year,$semester,$controlNo);

     return $this->successMessage($data,'Successfully',200);

    }
}
