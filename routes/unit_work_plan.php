<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitWorkPlanController;
use App\Http\Controllers\OpcrController;
use App\Http\Controllers\Hr\Hr_Unit_work_planController;
use App\Http\Controllers\Planning\Planning_Unit_work_planController;
use App\Http\Controllers\SpmsController;
use App\Http\Controllers\VwplantillastructureController;
use App\Http\Controllers\Activity_log_Controller;
use App\Http\Controllers\IpcrController;
use App\Http\Controllers\Hr\dashboardController;

Route::get('/unit_work_plan/index', [UnitWorkPlanController::class, 'unit_work_plan']);
Route::get('/unit_work_plan/data', [UnitWorkPlanController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/unit_work_plan/store', [UnitWorkPlanController::class, 'store']);
    Route::get('/employees/divisions', [UnitWorkPlanController::class, 'getDivisionsByOffice']);
    Route::get('/employees', [UnitWorkPlanController::class, 'getEmployeesByDivision']);
    Route::get('/employee/{id}/competencies', [UnitWorkPlanController::class, 'getEmployeeCompetencies']);
    Route::post('/employee/{id}/update/unitworkplan', [UnitWorkPlanController::class, 'updateEmployee']);
    Route::get('/division/status', [UnitWorkPlanController::class, 'get_division_status']);
    Route::get('/division/employee/performance', [UnitWorkPlanController::class, 'get_employee_performance']);

    Route::get('/f_category', [UnitWorkPlanController::class, 'category']);
    Route::get('/mfo', [UnitWorkPlanController::class, 'getMfosByCategory']);
    Route::get('/output', [UnitWorkPlanController::class, 'getOutputsByMfo']);
    Route::get('/getSupportOutputs', [UnitWorkPlanController::class, 'getSupportOutputs']);
    Route::get('/SupportOutputs', [UnitWorkPlanController::class, 'SupportOutputs']);

    Route::get('/office/structure', [VwplantillastructureController::class, 'index']);
    Route::get('/user_activity_log', [Activity_log_Controller::class, 'index']);

    Route::get('/opcr/divisions', [OpcrController::class, 'index']);
    Route::get('/opcr/office-head-functions/{officeId}', [OpcrController::class, 'getOfficeHeadFunctions']);
    Route::post('/opcr/save', [OpcrController::class, 'saveOpcr']);
    Route::get('/opcr/view/{officeId}', [OpcrController::class, 'view']);

    // HR Routes
    Route::get('/hr/unit_work_plan/office', [Hr_Unit_work_planController::class, 'office']);
    Route::get('/hr/unit_work_plan', [Hr_Unit_work_planController::class, 'unit_work_plan']);
    Route::get('/hr/unit_work_plan/employee', [Hr_Unit_work_planController::class, 'employee']);
    Route::get('/hr/unit_work_plan/divisions', [Hr_Unit_work_planController::class, 'getDivisionsWithWorkPlans']);
    Route::get('/hr/unit_work_plan/employees', [Hr_Unit_work_planController::class, 'getEmployeesByDivision']);
    Route::get('/hr/dashboard', [dashboardController::class, 'dashboard']);

    // Planning Routes
    Route::get('/planning/unit_work_plan/office', [Planning_Unit_work_planController::class, 'office']);
    Route::get('/planning/unit_work_plan', [Planning_Unit_work_planController::class, 'unit_work_plan']);
    Route::get('/planning/unit_work_plan/employee', [Planning_Unit_work_planController::class, 'employee']);
    Route::get('/planning/unit_work_plan/divisions', [Planning_Unit_work_planController::class, 'getDivisionsWithWorkPlans']);
    Route::get('/planning/unit_work_plan/employees', [Planning_Unit_work_planController::class, 'getEmployeesByDivision']);

    // SPMS
    Route::get('/spms/office/structure', [SpmsController::class, 'Spms_index']);
    Route::get('/Spms/fetch_employees', [SpmsController::class, 'fetchEmployees']);
});

// Public
Route::get('/ipcr/employee/unit_work_plan', [IpcrController::class, 'getEmployeesWithUnitWorkPlans']);
