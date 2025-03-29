<?php

namespace App\Http\Controllers\Auth_api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{



    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);
        // Find the user with office data
        $user = User::with('office')->where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'name' => ['The provided credentials are incorrect.'],
                ]
            ], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'office_id' =>$user->office_id,
                'role_id' => $user->role_id,
            ],
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:users,name',
            'password' => 'required|string|min:6',
            'office_id' => 'required|exists:offices,id',
            'role_id'=> 'required|exists:Roles,id',

        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'office_id' => $request->office_id,
            'role_id' => $request->role_id
        ]);

        event(new Registered($user));

        // Return the same structure as login
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'office_id' => $user->office_id,
                'role_id' => $user->role_id,
            ],
            'token' => $token, // Return the token
        ]);
    }


    public function logout(Request $request)
    {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
        ]);
    }

    public function user_account(){

        // Fetch the data using get()
        $data = User::select('office_id', 'name')->get();

        return response()->json($data);
    }
}


