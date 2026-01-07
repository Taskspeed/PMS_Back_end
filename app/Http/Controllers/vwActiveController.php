<?php

namespace App\Http\Controllers;

use App\Models\office;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\vwplantillastructure;
use Illuminate\Support\Facades\Auth;

class vwActiveController extends Controller
{

    // public function getOfficeEmployee(Request $request,$office_name)
    // {
    //     $query = DB::table('vwActive')->select(

    //         'ControlNo',
    //         'BirthDate',
    //         'Office',
    //         // Make sure this field matches your database column name
    //         'name4',
    //         'Designation',
    //         'status'
    //     )  ->where('Office',$office_name);  // Match the exact office name

    //     $data = $query->get();

    //     return response()->json($data);
    // }

    public function getOfficeEmployee(Request $request)
    {
        $office_name = $request->query('office_name');

        if (!$office_name) {
            return response()->json([
                'message' => 'office_name is required'
            ], 422);
        }

        $data = DB::table('vwActive')
            ->select(
                'ControlNo',
                'BirthDate',
                'Office',
                'name4',
                'Designation',
                'status'
            )
            ->where('Office', $office_name)
            ->get();

        return response()->json($data);
    }
}
