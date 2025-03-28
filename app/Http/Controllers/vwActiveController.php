<?php

namespace App\Http\Controllers;

use App\Models\vwActive;
use Illuminate\Http\Request;

class vwActiveController extends Controller
{
    //
    public function index(){
        $data = vwActive::select(
            'ControlNo',
            'PMISNO',
            'Surname',  // Corrected from 'Sumame' to 'Surname'
            'Firstname',
            'BirthDate',
            'Sex',
            'Office',
            'Status',
            'MIddlename',
            'Designation',
            'Divisions',
            'Sections'
        )->get();

        return response()->json($data);

    }
}
