<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use App\Http\Requests\OpcrStoreStatusRequest;
use App\Services\OpcrService;
use Illuminate\Http\Request;

class OpcrController extends Controller
{
    //upating the status of the Opcr

    protected OpcrService $opcrService;

    public function __construct(OpcrService $opcrService)
    {
        return  $this->opcrService = $opcrService;
    }


  

}
