<?php

namespace App\Http\Controllers\office;

use App\Models\F_category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FCategoryController extends Controller
{
    //

    public function data_f_category(){

        $data = F_category::all();
            return response()->json($data);

    }
}
