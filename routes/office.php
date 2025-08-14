<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\office\MfoController;
use App\Http\Controllers\office\FOutpotController;
use App\Http\Controllers\office\FCategoryController;

Route::get('/fetch_office', [OfficeController::class, 'index']);
Route::get('/fetch_f_category', [FCategoryController::class, 'index']);
Route::get('/fetch_mfo', [MfoController::class, 'index_data']);
Route::get('/Outputs', [FOutpotController::class, 'Outputs']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/add_mfo', [MfoController::class, 'store']);
    Route::get('/mfos', [MfoController::class, 'index']);
    Route::get('/mfos/soft-deleted', [MfoController::class, 'getSoftDeleted']);
    Route::post('/mfos/{id}', [MfoController::class, 'update']);
    Route::delete('/mfos/{id}', [MfoController::class, 'softDelete']);
    Route::patch('/mfos/restore/{id}', [MfoController::class, 'restore']);

    Route::post('/add_output', [FOutpotController::class, 'store']);
    Route::get('/outputs/soft-deleted', [FOutpotController::class, 'getSoftDeleted']);
    Route::post('/outputs/{id}', [FOutpotController::class, 'update']);
    Route::delete('/outputs/{id}', [FOutpotController::class, 'softDelete']);
    Route::patch('/outputs/restore/{id}', [FOutpotController::class, 'restore']);
});
