<?php

namespace App\Http\Controllers;


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

}
