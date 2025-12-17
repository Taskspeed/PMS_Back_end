<?php

namespace App\Http\Controllers;

use App\Models\office;
use App\Models\vwActive;
use App\Models\vwplantillastructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class vwActiveController extends Controller
{

    public function getOfficeEmployee(Request $request,$office_name)
    {
        $query = vwActive::select(
            'ControlNo',
            'BirthDate',
            'Office',
            // Make sure this field matches your database column name
            'name4',
            'Designation',
            'status'
        )  ->where('Office',$office_name);  // Match the exact office name

        $data = $query->get();

        return response()->json($data);
    }

}
