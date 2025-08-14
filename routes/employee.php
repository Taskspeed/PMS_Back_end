<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\vwActiveController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/employee/designation', [vwActiveController::class, 'index']);
    Route::get('/employees-by-office', [EmployeeController::class, 'show_employee']);
    Route::get('/fetch_employees', [EmployeeController::class, 'fetchEmployees']);
    Route::post('/add/employee', [EmployeeController::class, 'store']);
    Route::post('/employees/{id}/rank', [EmployeeController::class, 'updateRank']);
    Route::get('/search-employees', [EmployeeController::class, 'searchEmployees']);
    Route::get('/employee/counts', [EmployeeController::class, 'getEmployeeCounts']);
    Route::get('/employee', [EmployeeController::class, 'index']);
    Route::get('/employee/soft-deleted', [EmployeeController::class, 'getSoftDeleted']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'softDelete']);
    Route::patch('/employee/restore/{id}', [EmployeeController::class, 'restore']);
    Route::patch('employee/fetchEmployeeCounts', [EmployeeController::class, 'getOfficeStructureCounts']);
    Route::get('/position', [EmployeeController::class, 'index_position']);
    Route::get('/employee/office-structure-counts', [EmployeeController::class, 'getOfficeStructureCounts']);
});
