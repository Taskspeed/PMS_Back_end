<?php

namespace App\Http\Controllers;

use App\Http\Requests\opcrRequest;
use App\Http\Resources\OpcrResource;
use App\Models\opcr;
use App\Models\Employee;
use App\Services\OpcrService;
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
  
    // get the opcr of the office head
    public function opcr($controlNo, $semester, $year, opcrService $opcr)
    {

        $employeeOpcr = $opcr->opcrOfficeHead($controlNo, $semester, $year);

        if (!$employeeOpcr) {
            return response()->json([
                'message' => 'No OPCR found for the specified year and semester.'
            ], 404);
        }

        return new OpcrResource($employeeOpcr);
    }

    // saving the opcr of the office head
    public function opcrStore(opcrRequest $request, OpcrService $opcrService)
    {
        $validated = $request->validated();

        $opcr = $opcrService->storeAllotedBudget($validated);

        return response()->json($opcr);
    }


    // saving the opcr of the office head
    public function opcrUpdate(opcrRequest $request, OpcrService $opcrService)
    {
        $validated = $request->validated();

        $opcr = $opcrService->updateAllotedBudget($validated);

        return response()->json([
            'message' => 'Opcr update successfully',
             'data' =>$opcr

         ]);
    }
}
