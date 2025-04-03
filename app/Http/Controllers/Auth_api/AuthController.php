<?php

namespace App\Http\Controllers\Auth_api;


use Carbon\Carbon;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
    // Find the user with office and role data
    $user = User::with('office')->with('role')->where('name', $request->name)->first();

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
                'role_name' => $user->role->name ?? null,
                'designation'=>$user->designation,
            ],
            'token' => $token,
        ]);

}

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:users,name',
                'password' => 'required|string|min:6',
                'office_id' => 'required|exists:offices,id',
                'role_id' => 'required|exists:Roles,id',
                'designation'=> 'required|string',
            ]);

            $user = User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'office_id' => $request->office_id,
                'role_id' => $request->role_id,
                'remember_token' => Str::random(60),
                'designation'=>$request->designation,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'office_id' => $user->office_id,
                    'role_id' => $user->role_id,
                    'designation'=>$user->designation,
                ]
            ], 201); // Use 201 Created status code

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Revoke the token
            $user->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed. Please try again.'
            ], 500);
        }
    }

    public function user_account()
    {
        // Fetch users with office data and format date using Carbon
        $data = User::with('office:id,name')
            ->select('office_id', 'name', 'created_at')
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'office_name' => $user->office->name ?? 'N/A',
                    'datecreated' => Carbon::parse($user->created_at)->format('F d, Y'),
                ];
            });

         return response()->json($data);
    }
}


