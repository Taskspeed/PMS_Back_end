<?php

namespace App\Http\Controllers;

use App\Models\office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    //

    public function index (){

        $data = office::all();

        return response()->json($data);
    }
}
