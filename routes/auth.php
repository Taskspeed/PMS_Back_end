<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth_api\AuthController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);  // change route login
Route::get('/user_info', [UserController::class, 'get_user_info']);



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user_assign', [AuthController::class, 'register']); // change route name
    Route::post('/user_logout', [AuthController::class, 'logout']);
    Route::get('/user_data', [UserController::class, 'get_user_data']);
    Route::get('/my-unit-workplans', [UserController::class, 'getUserUnitWorkPlans']);
    Route::get('/user_account', [AuthController::class, 'user_account']);
    Route::post('/user/update/credentials/{id}', [AuthController::class, 'update']);
});
