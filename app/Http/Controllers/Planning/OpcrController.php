<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpcrStoreStatusRequest;
use App\Services\OpcrService;
use Illuminate\Http\Request;

class OpcrController extends Controller
{
    //upating the status of the Opcr

    protected $opcrService;

    public function __construct(OpcrService $opcrService)
    {
        return  $this->opcrService = $opcrService;
    }


     public function opcrStatus(Request $request){

         $validated = $request->validate([
            'office_opcr_id'   => 'required|array',
            'office_opcr_id.*' => 'required|exists:office_opcrs,id',
            'status'    => 'required|in:Received Target,Received Accomplishment',
            'remarks'   => 'nullable|string',
        ], [
            'status.in' => "Status must be either 'Received Target' or 'Received Accomplishment'.",
        ]);

        $result = $this->opcrService->opcrStoreStatus($validated);

     return $result;

    }

}
