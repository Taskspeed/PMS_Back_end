<?php

namespace App\Http\Controllers\Erms;

use App\Http\Controllers\Controller;
use App\Services\Erms\ErmsMfoService;
use App\Services\MfoService;
use Illuminate\Http\Request;

class ErmsMfoController extends Controller
{
    //
    protected ErmsMfoService $ErmsMfoService;

    public function __construct(ErmsMfoService $ErmsMfoService)
    {
        $this->ErmsMfoService = $ErmsMfoService;
    }

        public function getMfoErms(int $officeId, Request $request)
    {

        $user = $this->ErmsMfoService->getErmsMfo($officeId, $request);

        return response()->json($user);
    }

    // fetch all mfo of office head
    public function officeMfo(string $semester, int $year,int  $officeId)
    {
        $mfo = $this->ErmsMfoService->getOfficeMfo($semester, $year, $officeId);

        return $mfo;
    }
}
