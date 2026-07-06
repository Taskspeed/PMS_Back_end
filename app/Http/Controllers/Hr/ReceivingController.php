<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Qpef;
use App\Services\Hr\ReceivingService;
use App\Services\StructureService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceivingController extends Controller
{
    //
    use ApiResponseTrait;

    protected  StructureService $structureService;
    protected  ReceivingService $receivingService;

    public function __construct(StructureService $structureService,ReceivingService $receivingService)
    {
        $this->structureService = $structureService;
        $this->receivingService = $receivingService;
    }

    // get the list of ApproveIpcr
    public function getApproveIpcr(Request $request)
    {
         $user = Auth::user();

        // Actually enforce the role check
        if ($user->role_id != 6) {
            return response()->json([
                'message' => 'Unauthorized. Access restricted to authorized person only.'
            ], 403);
        }

        $year     = $request->input('year');
        $semester = $request->input('semester');
        $office   = $request->input('office');

        $data = $this->receivingService->ipcr($year,$semester,$office);

        return $data;

    }

    // get the targetperiod of  office opcr,unitworkplan,office
    public function getUnitworkplan(Request $request)
    {

     $user = Auth::user();

        // Actually enforce the role check
        if ($user->role_id != 6) {
            return response()->json([
                'message' => 'Unauthorized. Access restricted to authorized person only.'
            ], 403);
        }

        $year     = $request->input('year');
        $semester = $request->input('semester');
        $office   = $request->input('office');

        $data = $this->receivingService->unitworkplan($year,$semester,$office);

        return $data;
     
    }

    // fetch Qpef
    public function getAllQpef(){

        $data = Qpef::select('id','control_no','quarterly','year','status','created_at')->where('status','Pending')->get();

        return $this->successMessage($data,'success',200);
    }

}
