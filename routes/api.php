
<?php

use App\Http\Controllers\Activity_log_Controller;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Hr\dashboardController;
use App\Http\Controllers\Hr\Hr_Unit_work_planController;
use App\Http\Controllers\Hr\IndicatorController;
use App\Http\Controllers\Hr\RankController;
use App\Http\Controllers\Hr\SpmsController as HrSpmsController;
use App\Http\Controllers\Hr\UnitWorkPlanController as HrUnitWorkPlanController;
use App\Http\Controllers\office\DashboardController as OfficeDashboardController;
use App\Http\Controllers\office\EmployeeController;
use App\Http\Controllers\office\EmployeeRatingController;
use App\Http\Controllers\office\FCategoryController;
use App\Http\Controllers\office\FOutpotController;
use App\Http\Controllers\office\IpcrController;
use App\Http\Controllers\office\MfoController;
use App\Http\Controllers\office\UnitWorkPlanController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\OpcrController;
use App\Http\Controllers\Planning\Planning_Unit_work_planController;
use App\Http\Controllers\QpefController;
use App\Http\Controllers\SpmsController;
use App\Http\Controllers\TargetPeriodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\vwActiveController;
use App\Http\Controllers\VwplantillastructureController;
use Illuminate\Support\Facades\Route;

// Route::get('/time-check', function () {
//     return [
//         'laravel_now' => now(),
//         'php_time' => date('Y-m-d H:i:s'),
//         'php_timezone' => date_default_timezone_get(),
//     ];
// });


Route::post('/login', [AuthController::class, 'login']);  // change route login
// Route::get('/user_info', [UserController::class, 'getUserInfo']);

// Route::get('/spms/office/structure', [HrUnitWorkPlanController::class, 'getOfficePlantilla']);

// Rating
Route::get('employee/target-periods/{controlNo}', [EmployeeRatingController::class, 'targetPeriodEmployee']);
Route::get('employee/target-periods/details/{targetperiodId}', [EmployeeRatingController::class, 'targetPeriodDetails']);
Route::get('employee/list/rated/{control_no}', [EmployeeRatingController::class, 'getListOfRatingEmployee']);

Route::post('employee/store/rating', [EmployeeRatingController::class, 'performanceRating']);


Route::prefix('ipcr')->group(function () {
    Route::get('/employee/{ControlNo}/{year}/{semester}', [IpcrController::class, 'getIpcrEmployee']); // allow any characters, including leading zeros
    Route::get('/performance-standard/{targerperiodId}', [IpcrController::class, 'getPerformanceStandard']); // allow any characters, including leading zeros

    Route::get('/monthly-performance/{targerperiodId}', [IpcrController::class, 'getMonthlyEmployee']); // allow any characters, including leading zeros

    Route::get('/summary-monthly-performance/{targerperiodId}', [IpcrController::class, 'getSummaryMonthlyEmployee']); // allow any characters, including leading zeros
    // Route::get('v2/summary-monthly-performance/{targerperiodId}', [IpcrController::class, 'getSummaryMonthlyEmployee']); // allow any characters, including leading zeros
    Route::post('/attendance', [IpcrController::class, 'attendance']); // late and absent of employee

    Route::put('/employee/target-periods/{controlNo}/{semester}/{year}', [IpcrController::class, 'approveIpcrEmployee']);

    Route::put('/update-status/{targetperoidId}', [IpcrController::class, 'statusIpcr']); // late and absent of employee

    // plantilla strtucture
    // Route::post('/structure', [IpcrController::class, 'getStructure']);

});



Route::middleware('auth:sanctum')->group(function () {


    Route::prefix('office')->group(function () {

        Route::get('/', [OfficeController::class, 'getOffices']); // fetch all

        Route::get('/dashboard/ipcr-status-counts', [OfficeDashboardController::class, 'getIpcrStatusCounts']);

        Route::get('/dashboard', [OfficeDashboardController::class, 'getTotalEmployee']);

        Route::get('/structure', [OfficeController::class, 'officeStructure']);

        Route::get('/structure/count', [VwplantillastructureController::class, 'plantillaStructureEmployeeWithCount']);

        Route::get('/mfo', [MfoController::class, 'Mfo']); // getting the mfo of user logged in

    });

    Route::prefix('user')->group(function(){
        Route::get('/', [UserController::class, 'getUserData']);
        Route::post('/register', [AuthController::class, 'register']); // change route name
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/update/credentials/{id}', [AuthController::class, 'changePassword']);
        Route::get('/account', [AuthController::class, 'userAccount']);
    });

    //office
    Route::post('/add_mfo', [MfoController::class, 'storeMfo']);
    Route::get('/mfos', [MfoController::class, 'index']);
    Route::get('/mfos/soft-deleted', [MfoController::class, 'getSoftDeleted']);
    Route::post('/mfos/{id}', [MfoController::class, 'update']);
    Route::delete('/mfos/{id}', [MfoController::class, 'delete']);
    Route::patch('/mfos/restore/{id}', [MfoController::class, 'restore']);

    Route::post('/add_output', [FOutpotController::class, 'addOutput']);
    Route::post('/outputs/{id}', [FOutpotController::class, 'updateOutput']);
    Route::delete('/outputs/{id}', [FOutpotController::class, 'deleteOutput']);

    Route::get('/user_activity_log', [Activity_log_Controller::class, 'index']);

    // HR Routes
    Route::prefix('hr')->group(function () {

        Route::get('/current-employee', [dashboardController::class, 'currentEmployeeStatus']);


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


        //spms plantilla structure
        // Route::get('/spms/office/structure', [HrUnitWorkPlanController::class, 'getOfficePlantilla']);
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

        Route::get('/employees-requested', [SpmsController::class, 'getEmployeeRequested']);
        Route::get('/fetch_employees', [SpmsController::class, 'getEmployees']);
        // Route::get('v2/fetch_employees', [SpmsController::class, 'getEmployees']);

        Route::get('/count', [SpmsController::class, 'getEmployeeCountAndUnitworkplan']); // Count unit work plan
        Route::get('/office/structure', [SpmsController::class, 'officePlantilla']); // structure of office
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
        Route::get('/office-employee', [vwActiveController::class, 'getOfficeEmployee']);
        Route::get('/by-office', [EmployeeController::class, 'listOfEmployee']); // fetch employees by office
        Route::post('/store', [EmployeeController::class, 'addEmployee']);
        Route::post('/rank/{id}', [EmployeeController::class, 'updateRank']);
        Route::get('/search', [EmployeeController::class, 'searchEmployee']);
        Route::delete('/delete/{id}', [EmployeeController::class, 'deleteEmployee']);
        Route::get('/{controlNo}', [UnitWorkPlanController::class, 'findEmployee']);
        // Route::get('/list-of-employee', [EmployeeController::class, 'listOfEmployee']);

    });

   // Target Period Library
    Route::prefix('targetPeriod')->group(function () {
        Route::get('/', [TargetPeriodController::class, 'getTargetPeriods']);
        Route::post('/store', [TargetPeriodController::class, 'storeTargetPeriod']);
        Route::put('/update/{targetPeriodId}', [TargetPeriodController::class, 'updateTargetPeriod']);
        Route::delete('/delete/{targetPeriodId}', [TargetPeriodController::class, 'deleteTargetPeriod']);
    });

    // Qpef
    Route::prefix('qpef')->group(function () {
        Route::get('/{control_no}/{quarterly}/{year}', [QpefController::class, 'employeeQpef']);
        Route::post('/store', [QpefController::class, 'qpefStore']);
        Route::put('/update/{qpefId}', [QpefController::class, 'qpefUpdate']);
    });


    Route::prefix('opcr')->group(function () {
        // Route::get('/{controlNo}/{semester}/{year}', [OpcrController::class, 'opcr']);
        Route::get('/{controlNo}/{semester}/{year}', [OpcrController::class, 'opcr']);
        Route::post('/store', [OpcrController::class, 'opcrStore']); // save the opcr
        Route::put('/update', [OpcrController::class, 'opcrUpdate']); // save the opcr

    });
});


