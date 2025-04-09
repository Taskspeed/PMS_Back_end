<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\vwActiveController;
use App\Http\Controllers\office\MfoController;
use App\Http\Controllers\Activity_log_Controller;
use App\Http\Controllers\Auth_api\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\office\FOutpotController;
use App\Http\Controllers\office\FCategoryController;
use App\Http\Controllers\VwplantillastructureController;
use App\Models\Employee;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();

//     //fetch data name4/position
//     Route::get('/employee', [vwActiveController::class, 'index']);

// });





// Public Auth Route
Route::post('/user_login', [AuthController::class, 'login']);
Route::post('/user_assign', [AuthController::class, 'register']);
// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });

    //User Management
    // Route::post('/user_assign', [AuthController::class, 'register']);
    Route::post('/user_logout', [AuthController::class, 'logout']);

    // fetch data name4/position
    Route::get('/employee', [vwActiveController::class, 'index']);

    //for output
    Route::post('add_output', [FOutpotController::class, 'store']);
    Route::get('/outputs', [FOutpotController::class, 'index']);
    Route::get('/outputs/soft-deleted', [FOutpotController::class, 'getSoftDeleted']);
    Route::post('/outputs/{id}', [FOutpotController::class, 'update']);
    Route::delete('/outputs/{id}', [FOutpotController::class, 'softDelete']);
    Route::patch('/outputs/restore/{id}', [FOutpotController::class, 'restore']);

     //for mfos
    Route::post('/add_mfo', [MfoController::class, 'store']);
    Route::get('/mfos', [MfoController::class, 'index']);
    Route::get('/mfos/soft-deleted', [MfoController::class, 'getSoftDeleted']);
    Route::post('/mfos/{id}', [MfoController::class, 'update']);
    Route::delete('/mfos/{id}', [MfoController::class, 'softDelete']);
    Route::patch('/mfos/restore/{id}', [MfoController::class, 'restore']);




    //office structure
    Route::get('/office/structure', [VwplantillastructureController::class, 'index']);
    //for division
    Route::get('/employees-by-office', [EmployeeController::class, 'show_employee']);
    // adding
    Route::post('/add/employee', [EmployeeController::class, 'store']);
    Route::get('/fetch_employees', [EmployeeController::class, 'fetchEmployees']);
    Route::get('/employee/counts', [EmployeeController::class, 'getEmployeeCounts']);

    //soft delete
    Route::get('/employee', [EmployeeController::class, 'index']);   // Fetch only active (non-deleted) mfo
    Route::get('/employee/soft-deleted', [EmployeeController::class, 'getSoftDeleted']); // fetch_mfo_SoftDeleted
    // Route::post('/employee/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'softDelete']);  // softDelete for MFO
    Route::patch('/employee/restore/{id}', [EmployeeController::class, 'restore']);     // restore soft-deleted data
    
});


//user with office data
Route::middleware('auth:sanctum')->get('/user_data', [MfoController::class, 'getUserData']);



Route::get('/user_activity_log', [Activity_log_Controller::class, 'index']); //user_activity_log

Route::get('/show1', [VwplantillastructureController::class, 'show']);


//fetching
Route::get('/show',[vwActiveController::class, 'show']);



Route::get('/fetch_f_category', [FCategoryController::class, 'index']); //fetching category


Route::get('/fetch_office', [OfficeController::class, 'index']); //fetch office


Route::get('/fetch_mfo', [MfoController::class, 'index_data']); // mfo


Route::get('/user_account', [AuthController::class, 'user_account']); // user account and role
