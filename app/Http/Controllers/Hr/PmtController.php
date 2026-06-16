<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\User;
use App\Models\vwActive;
use App\Services\Hr\Pmt\PmtService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Routing\Controller as BaseController;

class PmtController extends BaseController
{
    // response format
    use ApiResponseTrait;

    protected ?Authenticatable $user = null;
    protected PmtService $pmtService;

    public function __construct(PmtService $pmtService)
    {
        $this->pmtService = $pmtService;
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();


            return $next($request);
        });
    }

    // fetch the  list of office assign on the user pmt 
    public function office()
    {
        try {
            $data = $this->pmtService->getoffice($this->user);
            return $this->successMessage($data, 'Successfully fetched');
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
    }

    // fetch the list of employee ipcr
    public function listOfEmployeeIpcr(Request $request)
    {
        $year     = $request->input('year');
        $semester = $request->input('semester');
        $office   = $request->input('office');
       
        try{
            $data = $this->EmployeeIpcr($this->user,$year,$semester,$office);
        return $this->successMessage($data, 'Successfully fetch');

        } catch (\Exception $e){
              return $this->errorMessage($e->getMessage());
        }
    }

    // list of the employee for pmt
    public function getOfficeEmployeePmt(Request $request)
    {
        $office_name = $request->query('office_name');
       try {
            $data = $this->OfficeEmployeePmt($office_name);
            return $this->successMessage($data, 'Successfully fetched');
       } catch (Exception $e) {
            return $this->errorMessage($e->getMessage());
       }
    }
}
