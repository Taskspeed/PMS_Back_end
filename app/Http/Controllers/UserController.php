<?php

namespace App\Http\Controllers;

use App\Models\mfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function getUserData(Request $request) // user data with his mfo
    {
        // Get authenticated user
        $user = Auth::user();

        // Check if authenticated
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Load office relationship
        if ($user instanceof \App\Models\User) {
            $user->load('office');
        }

        // Validate if user has an office
        if (!$user->office) {
            return response()->json(['error' => 'User does not have an associated office'], 400);
        }

        // Fetch MFOs with category name using eager loading
        $mfos = mfo::with('category:id,name')
            ->where('office_id', $user->office_id)
            ->get();

        return response()->json([
            'user' => $user,
            'mfos' => $mfos,

        ]);
    }
}
