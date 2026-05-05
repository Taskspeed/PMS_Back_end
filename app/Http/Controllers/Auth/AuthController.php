<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\addEmployeeRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Testing\Concerns\Has;

use function PHPUnit\Framework\returnSelf;

class AuthController extends Controller
{
    use ApiResponseTrait;

    //test
    public function userAccount()
    {
        // Fetch users with office data and format date using Carbon
        $data = User::with('office:id,name', 'role:id,name')
            ->select('id', 'office_id', 'role_id', 'name', 'created_at', 'active')
            ->whereNotin('role_id',[4])
            ->orderBy('created_at', 'desc') // Add this line to sort by newest first
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'password' => $user->password,
                    'office_name' => $user->office->name ?? 'N/A',
                    'role_name' => $user->role->name ?? 'N/A',
                    'role_id' => $user->role_id, // <-- Add this line
                    'datecreated' => Carbon::parse($user->created_at)->format('F d, Y'),
                    'active' => $user->active,
                ];
            });

        return response()->json($data);
    }


    public function login(LoginRequest $request)
    {

        // Attempt to find the user
        $user = User::with('office', 'role')->where('username', $request->username)->where('active', 1)->first();

        if (!$user) {
            return response()->json([
                'errors' => [
                    'username' => ['Username does not exist.']
                ]
            ], 422); // 422 = Unprocessable Entity for validation-style errors
        }

        //  User is inactive
        if (!$user->active) {
            return response()->json([
                'errors' => [
                    'username' => ['Your account is inactive. Please contact admin.']
                ]
            ], 403); // 🔥 better status for forbidden
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
                'username' => $user->username,
                'office_id' => $user->office_id,
                'role_id' => $user->role_id,
                'role_name' => $user->role->name ?? null,
                'designation' => $user->designation,
                    'active' => $user->active,
            ],
            'token' => $token,
        ]);
    }


    public function register(RegisterRequest $request) // creating user account
    {
        try {

            $user = User::create([
                'control_no' => $request->control_no,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'office_id' => $request->office_id,
                'role_id' => $request->role_id,
                'remember_token' => Str::random(32),
                'designation' => $request->designation,
                'username' => $request->username,
                'active' => $request->active,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => [
                    'id' => $user->id,
                    'control_no' => $user->control_no,
                    'name' => $user->name,
                    'office_id' => $user->office_id,
                    'role_id' => $user->role_id,
                    'designation' => $user->designation,
                    'username' => $user->username,
                    'active' => $user->active,
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
    public function changePassword(ChangePasswordRequest $request)
    {


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

    // user edit
    public function edit(Request $request)
    {
        try {
            $validated = $request->validate([
                'userId' => 'required|exists:users,id',
                'roleId'   => 'required|exists:roles,id',
                'active' => 'required|boolean'
                // 'officeId'   => 'required|exists:offices,id'
            ]);

            $user = User::where('id', $validated['userId'])->first();

            $user->role_id = $validated['roleId'];
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User role updated successfully',
                'data'    => $user
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    //  excluded supervisor_admin
    public function adminRole()
    {

        $roles = Role::whereNotIn('name', ['supervisor_admin'])->get();

        return response()->json($roles);
    }

    // reset password for user
    public function resetPassword($userId)
    {

        $user = User::find($userId);

        $user->password = Hash::make('admin');
        $user->save();

        return response()->json($user);
    }

    // user account details
    public function viewDetailAccount($userId)
    {
        $user = User::with('office', 'role')->find($userId);

        if (empty($user)) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'User retrieved successfully.',
            'data' => $user,
        ], 200);
    }

    //create account supervisor admin
    public function createAccountSupervisor(Request $request)
    {

        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string',
            'designation' => 'required|string',
            'role_id'     => 'required|exists:roles,id',  // fixed: 'exist' → 'exists', 'role' → 'roles' (use actual table name)
            'controlNo' => 'required|string',
            'username'    => 'required|string|unique:users,username', // added unique check
            'password' => 'required|string|min:3',
              'active' => 'required|boolean'
        ]);

        // $validated['office_id'] = $user->office_id; // force office_id from authenticated user
        $validated['office_id'] = $user->office_id; // force office_id from authenticated user

        $validated['password']  = Hash::make($validated['password']); // fixed

        $createUser = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'data'    => $createUser->makeHidden(['password']), // hide password from response
        ], 201);
    }

    //  supervisory_admin
    public function supervisoryRole()
    {

        $roles = Role::select('id as role_id', 'name')
            ->where('name', 'supervisor_admin')->get();


        if ($roles->isEmpty()) {
            return $this->error('No data found', 404);
        }
        return $this->success($roles, 'Fetch Successfully', 200);
    }


    // list of head account on the office
    public function headAccount()
    {   
        $user = Auth::user();

        $account = User::where('role_id',4)->where('office_id',$user->office_id)->get();
        

        if ($account->isEmpty()) {
            return $this->error('No data found', 404);

        }
        return $this->success($account, 'Fetch Successfully', 200);
    }

    // delete user account
    public function userDelete($userId){

        $user = User::find($userId);

            if (!$user) {
                return $this->error('User not found', 404);
            }

        $user->delete();

        return $this->success($user,'deleted successfully',200);

    }

    // update the account of head account 
    public function updateHeadAccount(Request $request)
    {
        try {
            $validated = $request->validate([
                'userId' => 'required|exists:users,id',
                'active' => 'required|boolean'
              
            ]);

            $user = User::where('id', $validated['userId'])->first();

            $user->active = $validated['active'];
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User active updated successfully',
                'data'    => $user
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    
}
