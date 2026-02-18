<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class vwActiveController extends Controller
{


    public function getOfficeEmployee(Request $request)
    {
        $office_name = $request->query('office_name');

        if (!$office_name) {
            return response()->json([
                'message' => 'office_name is required'
            ], 422);
        }

        // kunin lahat ng control_no sa users table
        $existingUsers = User::whereNotNull('control_no')
            ->pluck('control_no')
            ->toArray();

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
            ->whereNotIn('ControlNo', $existingUsers)
            ->get();

        return response()->json($data);
    }
}
