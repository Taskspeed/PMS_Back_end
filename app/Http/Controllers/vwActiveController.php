<?php

namespace App\Http\Controllers;

use App\Models\vwActive;
use Illuminate\Http\Request;

class vwActiveController extends Controller
{
    //
    public function index(){
        $data = vwActive::select(
            // 'ControlNo',
            // 'PMISNO',
            // 'Surname',  // Corrected from 'Sumame' to 'Surname'
            // 'Firstname',
            'BirthDate',
            // 'Sex',
            // 'Office',
            // 'Status',
            // 'MIddlename',
            'name4',
            'Designation',
            // 'Divisions',
            // 'Sections'
        )->get();

        return response()->json($data);

    }
}
