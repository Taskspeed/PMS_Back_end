
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpcrController;
use App\Http\Controllers\SpmsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\Hr\RankController;
use App\Http\Controllers\vwActiveController;
use App\Http\Controllers\office\MfoController;
use App\Http\Controllers\office\IpcrController;
use App\Http\Controllers\Hr\dashboardController;
use App\Http\Controllers\Hr\IndicatorController;
use App\Http\Controllers\TargetPeriodController;
use App\Http\Controllers\Activity_log_Controller;
use App\Http\Controllers\Auth_api\AuthController;
use App\Http\Controllers\office\FOutpotController;
use App\Http\Controllers\office\EmployeeController;
use App\Http\Controllers\office\FCategoryController;
use App\Http\Controllers\office\UnitWorkPlanController;
use App\Http\Controllers\Hr\Hr_Unit_work_planController;
use App\Http\Controllers\VwplantillastructureController;
use App\Http\Controllers\office\EmployeeRatingController;
use App\Http\Controllers\Planning\Planning_Unit_work_planController;
use App\Http\Controllers\office\DashboardController as OfficeDashboardController;

Route::post('/login', [AuthController::class, 'login']);  // change route login
// Route::get('/user_info', [UserController::class, 'getUserInfo']);


// Rating
Route::get('employee/target-periods/{controlNo}', [EmployeeRatingController::class, 'targetPeriodEmployee']);
Route::get('employee/target-periods/details/{targetperiodId}', [EmployeeRatingController::class, 'targetPeriodDetails']);
Route::get('employee/list/rated/{control_no}', [EmployeeRatingController::class, 'getListOfRatingEmployee']);

Route::post('employee/store/rating', [EmployeeRatingController::class, 'performanceRatingStore']);






// Route::get('/fetch_office', [OfficeController::class, 'getOffices']);
// Route::get('/fetch_f_category', [FCategoryController::class, 'index']);
// Route::get('/fetch_mfo', [MfoController::class, 'index_data']);
// Route::get('/Outputs', [FOutpotController::class, 'Outputs']);





Route::middleware('auth:sanctum')->group(function () {


    Route::prefix('office')->group(function () {

        Route::get('/', [OfficeController::class, 'getOffices']); // fetch all

        Route::get('/dashboard/ipcr-status-counts', [OfficeDashboardController::class, 'getIpcrStatusCounts']);

        Route::get('/dashboard', [OfficeDashboardController::class, 'getTotalEmployee']);

        Route::get('/structure', [OfficeController::class, 'plantillaStructureEmployee']);

        Route::get('/structure/count', [VwplantillastructureController::class, 'plantillaStructureEmployeeWithCount']);

        Route::get('/mfo', [MfoController::class, 'getUserMfo']); // getting the mfo of user logged in

    });


    Route::prefix('ipcr')->group(function(){
        Route::get('/employee/{ControlNo}/{year}/{semester}', [IpcrController::class, 'getIpcr']); // allow any characters, including leading zeros
        Route::get('/performance-standard/{targerperiodId}', [IpcrController::class, 'getPerformanceStandard']); // allow any characters, including leading zeros
        Route::get('/monthly-performance/{targerperiodId}', [IpcrController::class, 'getMonthlyRate']); // allow any characters, including leading zeros
        Route::get('/summary-monthly-performance/{targerperiodId}', [IpcrController::class, 'getSummaryMonthlyRate']); // allow any characters, including leading zeros

        Route::put('/employee/target-periods/{controlNo}/{semester}/{year}', [IpcrController::class, 'approveIpcrEmployee']);

    });


    Route::prefix('user')->group(function(){
        Route::get('/', [UserController::class, 'getUserData']);
        Route::post('/register', [AuthController::class, 'register']); // change route name
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/update/credentials/{id}', [AuthController::class, 'update']);
        Route::get('/account', [AuthController::class, 'userAccount']);
    });

    // Route::post('/user_assign', [AuthController::class, 'register']); // change route name
    // Route::post('/user_logout', [AuthController::class, 'logout']);
    // Route::get('/user_data', [UserController::class, 'get_user_data']);
    // Route::get('/my-unit-workplans', [UserController::class, 'getUserUnitWorkPlans']);
    // Route::get('/user_account', [AuthController::class, 'user_account']);







    //office
    Route::post('/add_mfo', [MfoController::class, 'store']);
    Route::get('/mfos', [MfoController::class, 'index']);
    Route::get('/mfos/soft-deleted', [MfoController::class, 'getSoftDeleted']);
    Route::post('/mfos/{id}', [MfoController::class, 'update']);
    Route::delete('/mfos/{id}', [MfoController::class, 'delete']);
    Route::patch('/mfos/restore/{id}', [MfoController::class, 'restore']);

    Route::post('/add_output', [FOutpotController::class, 'store']);
    Route::get('/outputs/soft-deleted', [FOutpotController::class, 'getSoftDeleted']);
    Route::post('/outputs/{id}', [FOutpotController::class, 'update']);
    Route::delete('/outputs/{id}', [FOutpotController::class, 'softDelete']);
    Route::patch('/outputs/restore/{id}', [FOutpotController::class, 'restore']);


    // Route::get('/employees-by-office', [EmployeeController::class, 'show_employee']); // fetch employees by office
    // Route::get('/fetch_employees', [EmployeeController::class, 'fetchEmployees']); // office employees
    // Route::post('/add/employee', [EmployeeController::class, 'store']);
    // Route::post('/employees/{id}/rank', [EmployeeController::class, 'updateRank']);
    // Route::get('/search-employees', [EmployeeController::class, 'searchEmployees']);
    // Route::get('/employee/counts', [EmployeeController::class, 'getEmployeeCounts']);
    // Route::get('/employee/soft-deleted', [EmployeeController::class, 'getSoftDeleted']);
    // Route::delete('/employees/{id}', [EmployeeController::class, 'deleteEmployee']);
    // Route::patch('/employee/restore/{id}', [EmployeeController::class, 'restore']);
    // Route::patch('employee/fetchEmployeeCounts', [EmployeeController::class, 'getOfficeStructureCounts']);
    // Route::get('/position', [EmployeeController::class, 'index_position']);
    // Route::get('/employee/office-structure-counts', [EmployeeController::class, 'getOfficeStructureCounts']);





    Route::get('/user_activity_log', [Activity_log_Controller::class, 'index']);

    // Route::get('/opcr/divisions', [OpcrController::class, 'index']);
    // Route::get('/opcr/office-head-functions/{officeId}', [OpcrController::class, 'getOfficeHeadFunctions']);
    // Route::post('/opcr/save', [OpcrController::class, 'saveOpcr']);
    Route::get('/opcr/{controlNo}/{semester}/{year}', [OpcrController::class, 'getOpcr']); // get the opcr of office
    Route::post('/opcr/store', [OpcrController::class, 'storeOpcr']); // save the opcr


    // HR Routes
    Route::prefix('hr')->group(function () {

        Route::get('/unit_work_plan/office', [Hr_Unit_work_planController::class, 'office']);
        Route::get('/unit_work_plan', [Hr_Unit_work_planController::class, 'unit_work_plan']);
        Route::get('/unit_work_plan/employee', [Hr_Unit_work_planController::class, 'employee']);
        Route::get('/unit_work_plan/divisions', [Hr_Unit_work_planController::class, 'getDivisionsWithWorkPlans']);
        Route::get('/unit_work_plan/employees', [Hr_Unit_work_planController::class, 'getEmployeesByDivision']);

        Route::get('/dashboard', [dashboardController::class, 'dashboard']);

        // indicator library
        Route::get('/indicator', [IndicatorController::class, 'getIndicator']);
        Route::post('/indicator/store', [IndicatorController::class, 'storeIndicator']);
        Route::put('/indicator/update/{indicatorId}', [IndicatorController::class, 'updateIndicator']);
        Route::delete('/indicator/delete/{indicatorId}', [IndicatorController::class, 'deleteIndicator']);

        // rank library
        Route::get('/rank', [RankController::class, 'getRank']);
        Route::post('/rank/store', [RankController::class, 'storeRank']);
        Route::put('/rank/update/{rankId}', [RankController::class, 'updateRank']);
        Route::delete('/rank/delete/{rankId}', [RankController::class, 'deleteRank']);
    });

    // Planning
    Route::prefix('planning')->group(function () {
        Route::get('/unit_work_plan/office', [Planning_Unit_work_planController::class, 'office']);
        Route::get('/unit_work_plan', [Planning_Unit_work_planController::class, 'unit_work_plan']);
        Route::get('/unit_work_plan/employee', [Planning_Unit_work_planController::class, 'employee']);
        Route::get('/unit_work_plan/divisions', [Planning_Unit_work_planController::class, 'getDivisionsWithWorkPlans']);
        Route::get('/unit_work_plan/employees', [Planning_Unit_work_planController::class, 'getEmployeesByDivision']);
    });

    // SPMS
    Route::prefix('spms')->group(function () {
        Route::get('/fetch_employees', [SpmsController::class, 'fetchEmployees']);
        Route::get('/count', [SpmsController::class, 'getEmployeeCountAndUnitworkplan']); // Count unit work plan
        Route::get('/office/structure', [SpmsController::class, 'plantillaStructureSpms']);
        Route::get('/target_periods/semester-year', [SpmsController::class, 'getTargetPeriodsSemesterYear']); // geting the year and semester
        Route::get('/employee/{ControlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'getUnitworkplan']); // allow any characters, including leading zeros
        // Route::get('/employee/{control_no}', [SpmsController::class, 'getEmployeeHaveUnitWorkPlan']); // geting the year and semester

    });

    // unit work plan of the employee
    Route::prefix('unit_work_plan')->group(function () {
        Route::post('/', [UnitWorkPlanController::class, 'getUniWorkPlanOfficeOrganization']);
        Route::post('/store', [UnitWorkPlanController::class, 'storeUnitWorkPlan']);
        Route::put('/update/{controlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'updateUnitWorkPlan']);
        Route::delete('/delete/{controlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'deleteUnitWorkPlan']);
    });


    // Employee
    Route::prefix('employee')->group(function () {

        Route::get('/', [EmployeeController::class, 'getEmployee']);
        // Route::get('/office-employee/{office_name}', [vwActiveController::class, 'getOfficeEmployee']);
        Route::get('/office-employee', [vwActiveController::class, 'getOfficeEmployee']);

        Route::get('/by-office', [EmployeeController::class, 'show_employee']); // fetch employees by office
        Route::post('/store', [EmployeeController::class, 'store']);
        Route::post('/rank/{id}', [EmployeeController::class, 'updateRank']);
        Route::get('/search', [EmployeeController::class, 'searchEmployees']);
        Route::delete('/delete/{id}', [EmployeeController::class, 'deleteEmployee']);
        Route::get('/{controlNo}', [UnitWorkPlanController::class, 'findEmployee']);

    });

   // Target Period Library
    Route::prefix('targetPeriod')->group(function () {
        Route::get('/', [TargetPeriodController::class, 'getTargetPeriods']);
        Route::post('/store', [TargetPeriodController::class, 'storeTargetPeriod']);
        Route::put('/update/{targetPeriodId}', [TargetPeriodController::class, 'updateTargetPeriod']);
        Route::delete('/delete/{targetPeriodId}', [TargetPeriodController::class, 'deleteTargetPeriod']);
    });

});


