
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



Route::prefix('ipcr')->group(function () {
    Route::get('/employee/{ControlNo}/{year}/{semester}', [IpcrController::class, 'getIpcrEmployee']); // allow any characters, including leading zeros
    Route::get('/performance-standard/{targerperiodId}', [IpcrController::class, 'getPerformanceStandard']); // allow any characters, including leading zeros

    Route::get('/monthly-performance/{targerperiodId}', [IpcrController::class, 'getMonthlyEmployee']); // allow any characters, including leading zeros

    Route::get('/summary-monthly-performance/{targerperiodId}', [IpcrController::class, 'getSummaryMonthlyEmployee']); // allow any characters, including leading zeros
    // Route::get('v2/summary-monthly-performance/{targerperiodId}', [IpcrController::class, 'getSummaryMonthlyEmployee']); // allow any characters, including leading zeros
    Route::post('/attendance', [IpcrController::class, 'attendance']); // late and absent of employee

    Route::put('/employee/target-periods/{controlNo}/{semester}/{year}', [IpcrController::class, 'approveIpcrEmployee']);

    Route::put('/update-status/{targetperoidId}', [IpcrController::class, 'statusIpcr']);

    // plantilla strtucture
    // Route::post('/structure', [IpcrController::class, 'getStructure']);

});

// get the target period on the employee on erms
Route::get('employee/target-periods/{controlNo}', [EmployeeRatingController::class, 'targetPeriodEmployee']);

//target detials  args targetperiodId
Route::get('employee/target-periods/details/{targetperiodId}', [EmployeeRatingController::class, 'targetPeriodDetails']);

// list of date that the employee rated already args controlNo
Route::get('employee/list/rated/{control_no}', [EmployeeRatingController::class, 'getListOfRatingEmployee']);



Route::middleware('auth:sanctum')->group(function () {


    // store mfo
    Route::post('/add_mfo', [MfoController::class, 'addMfo']);

    // get Mfo
    Route::get('/mfos', [MfoController::class, 'index']);

    // update mfo
    Route::post('/mfos/{id}', [MfoController::class, 'updateMfo']);

    //delete mfo
    Route::delete('/mfos/{id}', [MfoController::class, 'delete']);

    // store output
    Route::post('/add_output', [FOutpotController::class, 'addOutput']);

    // update output
    Route::post('/outputs/{id}', [FOutpotController::class, 'updateOutput']);

    // delete output
    Route::delete('/outputs/{id}', [FOutpotController::class, 'deleteOutput']);

    // activity logs
    Route::get('/user_activity_log', [Activity_log_Controller::class, 'index']);


    Route::prefix('office')->group(function () {

        Route::get('/', [OfficeController::class, 'getOffices']); // fetch all
        Route::get('/dashboard/ipcr-status-counts', [OfficeDashboardController::class, 'getIpcrStatusCounts']);
        Route::get('/dashboard', [OfficeDashboardController::class, 'getTotalEmployee']);
        Route::get('/structure', [OfficeController::class, 'officeStructure']);
        Route::get('/structure/count', [VwplantillastructureController::class, 'plantillaStructureEmployeeWithCount']);
        Route::get('/mfo', [MfoController::class, 'Mfo']); // getting the mfo of user logged in

    });

    Route::prefix('user')->group(function () {

        // get user data
        Route::get('/', [UserController::class, 'getUserData']);

        // add an account
        Route::post('/register', [AuthController::class, 'register']);

        // logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // updating the password
        Route::post('/update/credentials/{id}', [AuthController::class, 'changePassword']);

        // fetch the credential of user
        Route::get('/account', [AuthController::class, 'userAccount']);
    });


    // HR Routes
    Route::prefix('hr')->group(function () {

        Route::prefix('dashboard')->group(function (){

            // get the current number of job-order, casual, regular, honoraruim, and others status
            Route::get('/current-employee', [dashboardController::class, 'currentEmployeeStatus']);

            // old data of employee status
            Route::get('/employee/status/{year}/{semester}', [dashboardController::class, 'previousEmployeeStatus']);

            // fetching the available old data employee status
            Route::get('/employee/status/available', [dashboardController::class, 'fetchEmployeeStatus']);
        });



        // indicator library

        // fetch indicator
        Route::get('/indicator', [IndicatorController::class, 'getIndicator']);

        //store indicator
        Route::post('/indicator/store', [IndicatorController::class, 'storeIndicator']);

        // update indicator
        Route::put('/indicator/update/{indicatorId}', [IndicatorController::class, 'updateIndicator']);

        // delete indicator
        Route::delete('/indicator/delete/{indicatorId}', [IndicatorController::class, 'deleteIndicator']);

        // rank library

        //fetch  ranks
        Route::get('/rank', [RankController::class, 'getRank']);

        // store rank
        Route::post('/rank/store', [RankController::class, 'storeRank']);

        //update rank
        Route::put('/rank/update/{rankId}', [RankController::class, 'updateRank']);

        // delete rank
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

        // get the employee requested
        Route::get('/employees-requested', [SpmsController::class, 'getEmployeeRequested']);

        // fetch employee base on the user admin  with the target peroid of employee base on the semester and year
        Route::get('/fetch_employees', [SpmsController::class, 'getEmployees']);

        // get the structure of office
        Route::get('/office/structure', [SpmsController::class, 'officePlantilla']);

        // fetch the targer period  semester and year
        Route::get('/target_periods/semester-year', [SpmsController::class, 'getTargetPeriodsSemesterYear']);

        // get the unit work plan of employee
        Route::get('/employee/{ControlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'getUnitworkplan']);

    });

    // unit work plan of the employee
    Route::prefix('unit_work_plan')->group(function () {

        // fetch the organization of office
        Route::post('/', [UnitWorkPlanController::class, 'getUniWorkPlanOfficeOrganization']);

        //storing unitworkplan
        Route::post('/store', [UnitWorkPlanController::class, 'addUnitWorkPlan']);

        // updating unit work plan
        Route::put('/update/{controlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'updateUnitWorkPlan']);

        // deleting unit work plan
        Route::delete('/delete/{controlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'deleteUnitWorkPlan']);
    });

    // Employee
    Route::prefix('employee')->group(function () {

        // fetch employee base on the user admin
        Route::get('/', [EmployeeController::class, 'getEmployee']);

        // fetching the employee base on the selected office on the creating account
        Route::get('/office-employee', [vwActiveController::class, 'getOfficeEmployee']);

        // fetching the list of the employee under on the office
        Route::get('/by-office', [EmployeeController::class, 'listOfEmployee']);

        // storing employee on the office plantilla
        Route::post('/store', [EmployeeController::class, 'addEmployee']);

        // updating the rank of employee args  rank-in-file, supervisory, and others
        Route::post('/rank/{id}', [EmployeeController::class, 'updateRank']);

        //  search employee
        Route::get('/search', [EmployeeController::class, 'searchEmployee']);

        // deleting or remove the employee on the office plantilla
        Route::delete('/delete/{id}', [EmployeeController::class, 'deleteEmployee']);

        Route::get('/{controlNo}', [UnitWorkPlanController::class, 'findEmployee']);

        // storing rating
        Route::post('store/rating', [EmployeeRatingController::class, 'performanceRating']);
    });

    // Target Period Library
    Route::prefix('targetPeriod')->group(function () {

        // fetch target period
        Route::get('/', [TargetPeriodController::class, 'getTargetPeriods']);

        // storing target period  semester  and year
        Route::post('/store', [TargetPeriodController::class, 'storeTargetPeriod']);

        // updating targer period
        Route::put('/update/{targetPeriodId}', [TargetPeriodController::class, 'updateTargetPeriod']);

        // deleting target period
        Route::delete('/delete/{targetPeriodId}', [TargetPeriodController::class, 'deleteTargetPeriod']);
    });

    // Qpef
    Route::prefix('qpef')->group(function () {

        // get qpef Q1-Q2-Q3-Q4
        Route::get('/{control_no}/{quarterly}/{year}', [QpefController::class, 'employeeQpef']);

        // storing qpef
        Route::post('/store', [QpefController::class, 'qpefStore']);

        // updating qpef
        Route::put('/update/{qpefId}', [QpefController::class, 'qpefUpdate']);
    });


    Route::prefix('opcr')->group(function () {

        // get the opcr
        Route::get('/{controlNo}/{semester}/{year}', [OpcrController::class, 'opcr']);

        // storing opcr
        Route::post('/store', [OpcrController::class, 'opcrStore']);

        //updating opcr
        Route::put('/update', [OpcrController::class, 'opcrUpdate']);
    });


    Route::prefix('monitor')->group(function(){

        // store monitor
        Route::post('/store', [HrUnitWorkPlanController::class, 'monitorUnitworkplan']);

    });
});
