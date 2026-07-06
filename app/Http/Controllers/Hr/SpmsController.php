<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Hr\SpmsService;

class SpmsController extends Controller
{

    protected SpmsService $spmsService;

    public function __construct(SpmsService $spmsService)
    {
        $this->spmsService = $spmsService;
    }

      // get the list of IPCR target period of spms
    public function listOfIpcr(Request $request)
    {
        $year  = $request->input('year');
        $semester = $request->input('semester');       
        $office = $request->input('office');   
        $employee = $this->spmsService->listOfIpcr($year, $semester,$office);

        return $employee;
    }

    // get the list of unit work plans
    public function listOfUnitWorkPlan(Request $request)
    {
        $year  = $request->input('year');
        $semester = $request->input('semester');       
        $office = $request->input('office');    

        $employee = $this->spmsService->listOfUnitWorkPlan($year, $semester,$office);

        return $employee;
    }


    // get the list of IPCR target period of spms
    public function listOfOpcr(Request $request)
    {
        $year  = $request->input('year');
        $semester = $request->input('semester');       
        // $office = $request->input('office');    
        $employee = $this->spmsService->listOfOpcr($year, $semester);

        return $employee;
    }

}
