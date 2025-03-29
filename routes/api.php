<?php

use App\Http\Controllers\Activity_log_Controller;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MfoController;

use App\Http\Controllers\OfficeController;
use App\Http\Controllers\vwActiveController;

use App\Http\Controllers\Auth_api\AuthController;
use App\Http\Controllers\Auth_api\LoginController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();


});

//Auth
Route::post('/user_login', [AuthController::class, 'login']);
Route::post('/user_assign', [AuthController::class, 'register']);
Route::post('/user_logout', [AuthController::class, 'logout']);

// user account and role
Route::get('/user_account', [AuthController::class, 'user_account']);


//fetch data name4/position
Route::get('/employee', [vwActiveController::class, 'index']);


//fetch office data
Route::get('/fetch_office',[OfficeController::class,'index']);




Route::post('/add-mfo',[MfoController::class,'store']);



//user_activity_log
Route::get('/user_activity_log',[Activity_log_Controller::class,'index']);
