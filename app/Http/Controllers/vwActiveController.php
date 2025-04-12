<?php

namespace App\Http\Controllers;

use App\Models\office;
use App\Models\vwActive;
use App\Models\vwplantillastructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class vwActiveController extends Controller
{
    //
    // public function index(){
    //     $data = vwActive::select(
    //         // 'ControlNo',
    //         // 'PMISNO',
    //         // 'Surname',  // Corrected from 'Sumame' to 'Surname'
    //         // 'Firstname',
    //         'BirthDate',
    //         // 'Sex',
    //         'Office',
    //         // 'Status',
    //         // 'MIddlename',
    //         'name4',
    //         'Designation',
    //         // 'Divisions',
    //         // 'Sections'
    //     )->get();

    //     return response()->json($data);

    // }
    public function index(Request $request)
    {
        $query = vwActive::select(
            'BirthDate',
            'Office',
            // Make sure this field matches your database column name
            'name4',
            'Designation'
        );

        if ($request->has('office_name')) {  // Changed from office_id to office_name
            $query->where('Office', $request->office_name);  // Match the exact office name
        }

        $data = $query->get();

        return response()->json($data);
    }

}
