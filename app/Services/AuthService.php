<?php

namespace App\Services;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserOfficeAssign;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AuthService
{

    // fetch user credentials
    public function userAccount()
    {
        // Fetch users with office data and format date using Carbon
        $data = User::with('office:id,name', 'role:id,name')
            ->select('id', 'office_id', 'role_id', 'name', 'created_at', 'active','username')
            ->whereNotin('role_id', [4])
            ->orderBy('created_at', 'desc') // Add this line to sort by newest first
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'password' => $user->password,
                    'office_name' => $user->office->name ?? 'N/A',
                    'role_name' => $user->role->name ?? 'N/A',
                    'role_id' => $user->role_id, // <-- Add this line
                    'datecreated' => Carbon::parse($user->created_at)->format('F d, Y'),
                    'active' => $user->active,
                    'prefix' => $user->prefix,
                    'suffix' => $user->suffix,
                ];
            });

        return response()->json($data);
    }

    // user login
    public function login(LoginRequest $request)
    {

        // Attempt to find the user
        $user = User::with('office', 'role')->where('username', $request->username)->first();

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

    const ALLOWED_ROLES = [1, 2, 3, 4, 5, 6];
    public function register(RegisterRequest $request)
    {
        try {
            // ✅ Prevent duplicate: same control_no + same role_id
            $alreadyExists = User::where('control_no', $request->control_no)
                ->where('role_id', $request->role_id)
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This employee already has this role assigned.'
                ], 422);
            }

            // ✅ Prevent registering if employee already has all 6 roles
            $currentRoleCount = User::where('control_no', $request->control_no)
                ->whereIn('role_id', self::ALLOWED_ROLES)
                ->distinct('role_id')
                ->count('role_id');

            if ($currentRoleCount >= count(self::ALLOWED_ROLES)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'This employee already has all roles assigned and cannot be registered again.'
                ], 422);
            }

            $user = User::create([
                'control_no'     => $request->control_no,
                'name'           => $request->name,
                'password'       => Hash::make($request->password),
                'office_id'      => $request->office_id,
                'role_id'        => $request->role_id,
                'remember_token' => Str::random(32),
                'designation'    => $request->designation,
                'username'       => $request->username,
                'active'         => $request->active,
                'prefix'         => $request->prefix,
                'suffix'         => $request->suffix,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'User created successfully',
                'user'    => [
                    'id'         => $user->id,
                    'control_no' => $user->control_no,
                    'name'       => $user->name,
                    'office_id'  => $user->office_id,
                    'role_id'    => $user->role_id,
                    'designation' => $user->designation,
                    'username'   => $user->username,
                    'active'     => $user->active,
                    'prefix'     => $user->prefix,
                    'suffix'     => $user->suffix,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
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
    public function edit(?array $validated,Authenticatable $current_user)
    {
        try {

            $user = User::where('id', $validated['userId'])->first();

            $user->role_id = $validated['roleId'];
            $user->active  = $validated['active'];
            $user->prefix  = $validated['prefix'] ?? null;
            $user->suffix  = $validated['suffix'] ?? null;
            $user->save();

            // assign multiple offices (delete old ones first to avoid duplicates)
            if (!empty($validated['office_id_assign'])) {
                UserOfficeAssign::where('user_id', $user->id)->delete();

                foreach ($validated['office_id_assign'] as $officeId) {
                    UserOfficeAssign::create([
                      'assigned_by' => $current_user->name,  // or auth()->id()
                        'user_id'     => $current_user->id,   // was $create_user_account->id (undefined variable)
                        'office_id'   => $officeId,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
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

    //create account supervisor admin
    public function createAccountSupervisor(?array $validated)
    {

        $user = Auth::user();

        // $validated['office_id'] = $user->office_id; // force office_id from authenticated user
        $validated['office_id'] = $user->office_id; // force office_id from authenticated user

        $validated['password']  = Hash::make($validated['password']); // fixed

        // camelCase → snake_case to match DB column and $fillable
        $validated['control_no'] = $validated['controlNo'];
        unset($validated['controlNo']);

        $createUser = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully.',
            'data'    => $createUser->makeHidden(['password']), // hide password from response
        ], 201);
    }

    // update the account of head account
    public function updateHeadAccount(array $validated)
    {
        try {

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

    // create pmt account
    public function createPmtAccount(?array $validated)
    {
        $user = Auth::user();
        try {
            $create_user_account = DB::transaction(function () use ($validated, $user) {

                // create user
                $create_user_account = User::create([
                    'control_no'   => $validated['controlNo'],
                    'name'        => $validated['name'],
                    'designation' => $validated['designation'],
                    'role_id'     => $validated['role_id'],
                    'office_id'   => $validated['office_id'],
                    'password'    => $validated['password'],
                    'username'    => $validated['username'],
                    'active'      => $validated['active'],
                    'pmt_type'      => $validated['pmt_type'] ?? null,
                    'suffix'      => $validated['suffix'] ?? null,
                    'prefix'      => $validated['prefix'] ?? null

                ]);

                // assign multiple offices
                foreach ($validated['office_id_assign'] as $officeId) {
                    UserOfficeAssign::create([
                        'assigned_by' => $user->name,
                        'user_id'     => $create_user_account->id,
                        'office_id'   => $officeId,
                    ]);
                }

                return $create_user_account;
            });

            return response()->json([
                'success' => true,
                'message' => 'PMT account created successfully.',
                'data'    => $create_user_account,
            ], 201);
        } catch (\Exception $e) {
            // If anything fails, DB::transaction auto rolls back everything
            return response()->json([
                'success' => false,
                'message' => 'Failed to create account. No data was saved.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
