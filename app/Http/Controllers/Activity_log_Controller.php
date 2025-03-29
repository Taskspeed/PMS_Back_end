<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class Activity_log_Controller extends Controller
{
    //

    public function index(){

    $data = Activity::all();
     return response()->json($data);
    }
}
