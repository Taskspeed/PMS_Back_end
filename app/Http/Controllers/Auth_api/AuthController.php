<?php

namespace App\Http\Controllers\Auth_api;


use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    //test
    public function user_account()
    {
        // Fetch users with office data and format date using Carbon
        $data = User::with('office:id,name','role:id,name')
            ->select('office_id', 'role_id','name', 'created_at')
            ->orderBy('created_at', 'desc') // Add this line to sort by newest first
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'password' => $user->password,
                    'office_name' => $user->office->name ?? 'N/A',
                    'role_name' => $user->role->name ?? 'N/A',
                'role_id' => $user->role_id, // <-- Add this line
                'datecreated' => Carbon::parse($user->created_at)->format('F d, Y'),
                ];
            });

        return response()->json($data);
    }


    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to find the user
        $user = User::with('office', 'role')->where('name', $request->name)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    'name' => ['Username does not exist.']
                ]
            ], 422); // 422 = Unprocessable Entity for validation-style errors
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['Password is incorrect.']
                ]
            ], 422);
        }

        // Generate token if credentials are valid
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'office_id' => $user->office_id,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name ?? null,
                'designation' => $user->designation,
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
                'remember_token' => Str::random(32),
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


    // Add this method to your AuthController.php
    public function update(Request $request)
    {
        $request->validate([
            'oldPassword' => 'required|string',
            'newPassword' => 'nullable|string|min:6|different:oldPassword',
        ]);

        try {
            $user = $request->user();

            // Verify old password
            if (!Hash::check($request->oldPassword, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The provided old password is incorrect.'
                ], 422);
            }

            // Update password if new password is provided
            if ($request->newPassword) {
                $user->password = Hash::make($request->newPassword);
                $user->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTempPassword(Request $request)
    {
        try {
            $user = $request->user();

            // Verify user has permission
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Generate a temporary password (valid for 10 seconds)
            $tempPassword = Str::random(12); // Or use your actual decryption logic

            // Log this access
            Log::info('Password viewed by user', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'time' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'tempPassword' => $tempPassword,
                'expires_in' => 10 // seconds
            ]);
        } catch (\Exception $e) {
            Log::error('Password view error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve password'
            ], 500);
        }
    }
}


