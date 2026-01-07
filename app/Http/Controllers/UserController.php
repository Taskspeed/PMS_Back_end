<?php

namespace App\Http\Controllers;

use App\Models\mfo;
use App\Models\Unit_work_plan;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function getUserData()
    {
        $user = Auth::user();
        $office = $user->office;

        // Get all categories having either MFOs or outputs for this office

        $result = [
            'user' => $user,

        ];


        return response()->json($result);
    }



    public function getUserInfo()
    {
        $users = vwActive::where('Surname', 'LIKE', '%mahusay%')->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
}
