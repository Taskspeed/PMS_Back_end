
<?php

use App\Http\Controllers\Activity_log_Controller;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Erms\EmployeeRatingController;
use App\Http\Controllers\Erms\EmployeeSupervisorController;
use App\Http\Controllers\Erms\ErmsMfoController;

use App\Http\Controllers\Erms\ErmsUnitWorkPlanController;
use App\Http\Controllers\Erms\TargetperiodController as ErmsTargetperiodController;
use App\Http\Controllers\Hr\dashboardController;
use App\Http\Controllers\Hr\IndicatorController;
use App\Http\Controllers\Hr\PmtController;
use App\Http\Controllers\Hr\RankController;
use App\Http\Controllers\Hr\UnitWorkPlanController as HrUnitWorkPlanController;
use App\Http\Controllers\office\DashboardController as OfficeDashboardController;
use App\Http\Controllers\office\EmployeeController;
use App\Http\Controllers\office\FOutpotController;
use App\Http\Controllers\office\IpcrController;
use App\Http\Controllers\office\MfoController;
use App\Http\Controllers\office\QpefController;
use App\Http\Controllers\office\UnitWorkPlanController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\OpcrController;
use App\Http\Controllers\Planning\DashboardController as PlanningDashboardController;
use App\Http\Controllers\Planning\OpcrController as PlanningOpcrController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\SpmsController;
use App\Http\Controllers\SpmsProcessController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TargetPeriodController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\vwActiveController;
use App\Http\Controllers\VwplantillastructureController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\HttpCache\SurrogateInterface;


Route::post('/login', [AuthController::class, 'login']);  // change route login


Route::prefix('ipcr')->group(function () {
    Route::get('/employee/{ControlNo}/{year}/{semester}', [IpcrController::class, 'getIpcrEmployee']); // allow any characters, including leading zeros
    Route::get('/performance-standard/{targerperiodId}', [IpcrController::class, 'getPerformanceStandard']); // allow any characters, including leading zeros

    Route::get('/monthly-performance/{targerperiodId}', [IpcrController::class, 'getMonthlyEmployee']); // allow any characters, including leading zeros

    Route::get('/summary-monthly-performance/{targerperiodId}', [IpcrController::class, 'getSummaryMonthlyEmployee']); // allow any characters, including leading zeros
    // Route::get('v2/summary-monthly-performance/{targerperiodId}', [IpcrController::class, 'getSummaryMonthlyEmployee']); // allow any characters, including leading zeros
    Route::post('/attendance', [IpcrController::class, 'attendance']); // late and absent of employee

    Route::put('/employee/target-periods/{controlNo}/{semester}/{year}', [IpcrController::class, 'approveIpcrEmployee']);

    // todo: need to change
    // Route::post('/update-status', [IpcrController::class, 'statusIpcr']); // update ipcr of employee 

    // plantilla strtucture
    // Route::post('/structure', [IpcrController::class, 'getStructure']);

});


    // list of date that the employee rated already args controlNo
    Route::get('employee/list/rated/{control_no}', [EmployeeRatingController::class, 'getListOfRatingEmployee']);

    // get the target period on the employee on erms
    Route::get('employee/target-periods/{controlNo}', [EmployeeRatingController::class, 'targetPeriodEmployee']);

    //target detials  args targetperiodId
    Route::get('employee/target-periods/details/{targetperiodId}', [EmployeeRatingController::class, 'targetPeriodDetails']);


Route::prefix('erms')->group(function () {

     // get my supervipor
    Route::get('employee/supervisor', [EmployeeSupervisorController::class, 'getMySupervisor']);
    // employee information
    Route::get('employee/{controlNo}', [EmployeeSupervisorController::class, 'employeeInformation']);

    // get the target period on the employee on erms
    Route::get('employee/target-periods/{controlNo}', [EmployeeRatingController::class, 'targetPeriodEmployee']);

    //target detials  args targetperiodId
    // Route::get('employee/target-periods/details/{targetperiodId}', [EmployeeRatingController::class, 'targetPeriodDetails']);

    // list of date that the employee rated already args controlNo
    Route::get('employee/list/rated/{control_no}', [EmployeeRatingController::class, 'getListOfRatingEmployee']);


    Route::get('employee/performance-record/{targetPeriodId}', [EmployeeRatingController::class, 'performanceRatingRecord']);

    Route::get('/employee/target-periods/details/{targetperiodId}/{month}/{year}/{week}', [EmployeeRatingController::class, 'targetPeriod']);

    // storing rating
    Route::post('employee/store/rating', [EmployeeRatingController::class, 'performanceRating']);

    Route::get('/target-period', [ErmsTargetperiodController::class, 'lastestTargetPeriods']);

    Route::get('/mfo/{officeId}', [ErmsMfoController::class, 'getMfoErms']);

    // get the office head mfo
    Route::get('/head-mfo/{semester}/{year}/{officeId}', [ErmsMfoController::class, 'officeMfo']); 

    Route::get('/managerial/{year}/{semester}/{mfo}/{officeId}', [ErmsUnitWorkPlanController::class, 'findManagerial']);


});



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
        Route::get('/structure', [OfficeController::class, 'officeStructure']);
        Route::get('/structure/number', [OfficeController::class, 'officeStructure']);
        Route::get('/structure/count', [VwplantillastructureController::class, 'plantillaStructureEmployeeWithCount']);
        Route::get('/mfo', [MfoController::class, 'Mfo']); // getting the mfo of user logged in
        Route::get('/head-mfo/{semester}/{year}', [MfoController::class, 'fetchMfo']); // getting the mfo of user logged in

        // todo: need to fix what is the flow
        Route::get('/employee-draft-rating/{semester}/{year}', [OfficeController::class, 'listOfEmployeeRatingDraft']); // fetch all
        Route::get('/pmt/available', [OfficeController::class, 'pmtOfficeAvailable']); // fetch the office available
        Route::get('/ipcr', [IpcrController::class, 'listIpcr']); // fetch the office available


        Route::prefix('dashboard')->group(function () {
            Route::get('/', [OfficeDashboardController::class, 'dashboardStatus']);
            Route::get('/employee/without-ipcr', [OfficeDashboardController::class, 'listOfEmployeeNoIpcr']);
        });
    });

    Route::prefix('user')->group(function () {

        // get user data
        Route::get('/', [UserController::class, 'getUserData']);

        // add an account
        Route::post('/register', [AuthController::class, 'register']);

        // logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // updating the password
        Route::post('/update/credentials', [AuthController::class, 'changePassword']);

        // fetch the credential of user
        Route::get('/account', [AuthController::class, 'userAccount']);

        // updating access role
        Route::post('/edit', [AuthController::class, 'edit']);

        // user delete
        Route::delete('/delete/{userId}', [AuthController::class, 'userdelete']);

        // fetch role excluded supervisor_admin
        Route::get('/role', [AuthController::class, 'adminRole']);

        // // fetch role of supervisory only
        Route::get('/supervisor-role', [AuthController::class, 'supervisoryRole']);

        // reset password
        Route::post('/reset-password/{userId}', [AuthController::class, 'resetPassword']);

        // view account detials
        Route::get('/view/account/{userId}', [AuthController::class, 'viewDetailAccount']);

        // create supervisor admin
        Route::post('supervisory', [AuthController::class, 'createAccountSupervisor']);

        // list of account of head account
        Route::get('/head-account', [AuthController::class, 'headAccount']);
        // update the account of head
        Route::post('/update/head-account', [AuthController::class, 'updateHeadAccount']);

        Route::post('/create/pmt/account', [AuthController::class, 'createPmtAccount']);
    });


    // HR Routes
    Route::prefix('hr')->group(function () {

        Route::prefix('dashboard')->group(function () {

            // // get the current number of job-order, casual, regular, honoraruim, and others status
            // Route::get('/current-employee', [dashboardController::class, 'currentEmployeeStatus']);

            // // old data of employee status
            // Route::get('/employee/status/{year}/{semester}', [dashboardController::class, 'previousEmployeeStatus']);

            Route::get('/list/ipcr', [dashboardController::class, 'listOfIpcr']); // OPTIONAL FILTER
            Route::get('/list/opcr', [dashboardController::class, 'listOfOpcr']);  // OPTIONAL FILTER
            Route::get('/list/unit-work-plan', [dashboardController::class, 'listOfUnitWorkPlan']);  // OPTIONAL FILTER

            Route::get('/current/target-period', [dashboardController::class, 'currentTargetPeriod']);
            Route::get('/plantilla', [dashboardController::class, 'plantillaEmployee']);

            Route::get('/employee', [dashboardController::class, 'dashboardSummaryData']);

        });


        Route::prefix('unit-work-plan')->group(function () {

            // updating the unit-work-plan of status
            Route::post('/update-status', [HrUnitWorkPlanController::class, 'updateUnitWorkPlan']);
        });


        Route::prefix('target-period')->group(function () {

            // updating the unit-work-plan of status
            Route::post('/update-status', [HrUnitWorkPlanController::class, 'lockTargetPeriod']);
        });


        Route::prefix('receiving')->group(function () {

            // fetch only office assign on pmt account, get all approve ipcr 
            Route::get('/ipcr', [ReceivingController::class, 'getApproveIpcr']); // need to change into pmt this is for pmt

            //get the unit work plan 
            Route::get('/unitworkplan', [ReceivingController::class, 'getUnitworkplan']); //

            // update  status of the ipcr to receive
            // Route::post('/ipcr/receive', [ReceivingController::class, 'updateIpcrReceive']);

            // update  status of the unit work plan to receive
            // Route::post('/unitworkplan/receive', [ReceivingController::class, 'updateUnitWorkPlanReceive']);
        });



        // indicator library

        // fetch indicator
        Route::get('/indicator', [IndicatorController::class, 'getIndicator'])->withoutMiddleware(['auth:sanctum']);

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
        Route::get('/category', [CategoryController::class, 'fetchCategory']);
    });

    // Planning
    Route::prefix('planning')->group(function () {

        // dashboard
        Route::prefix('dashboard')->group(function () {
            Route::get('/status/{semester}/{year}', [PlanningDashboardController::class, 'numberOfStatus']);
        });

        // opcr  status Received need to finalize if this will be needed or not
        Route::prefix('opcr')->group(function () {
            // list of received opcr
            Route::get('/list-received-opcr/{semester}/{year}', [PlanningDashboardController::class, 'listOfOpcrReceived']);

        });

        // receiving  // check if needed
        Route::prefix('receiving')->group(function () {
            // list of pending
            Route::get('/list-pending-opcr/{semester}/{year}', [PlanningDashboardController::class, 'listOfOpcrPending']);
        });
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

        Route::get('/employee/{ControlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'getUnitworkplan'])->withoutMiddleware(['auth:sanctum']);

        //get the unitworkplan to update
        Route::post('/update/unitworkplan', [SpmsProcessController::class, 'updateUnitworkplan']); 

        // updating opcr
        Route::post('/update/opcr', [SpmsProcessController::class, 'updateOpcr']);

        // updating ipcr
        Route::post('/update/ipcr', [SpmsProcessController::class, 'updateIpcr']);


    });

    // unit work plan of the employee
    Route::prefix('unit_work_plan')->group(function () {

        // find and get the data of managerial on the office
        Route::get('/managerial/{year}/{semester}/{mfo}', [UnitWorkPlanController::class, 'findManagerial']);

        // fetch the organization of office
        Route::post('/', [UnitWorkPlanController::class, 'getUniWorkPlanOfficeOrganization']);

        //storing unitworkplan
        Route::post('/store', [UnitWorkPlanController::class, 'addUnitWorkPlan'])->withoutMiddleware(['auth:sanctum']);

        // updating unit work plan
        // Route::put('/update/{controlNo}/{semester}/{year}', [UnitWorkPlanController::class, 'updateUnitWorkPlan']);
        Route::post('/update', [UnitWorkPlanController::class, 'updateUnitWorkPlan'])->withoutMiddleware(['auth:sanctum']);

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
        Route::post('v1/store', [EmployeeController::class, 'addEmployee']);

        // updating the rank of employee args  rank-in-file, supervisory, and others
        Route::post('/rank/{id}', [EmployeeController::class, 'updateRank']);

        // updating job title
        Route::post('/title/{employeeId}', [EmployeeController::class, 'updateJobTitle']);

        //  search employee
        Route::get('/search', [EmployeeController::class, 'searchEmployee']);

        // deleting or remove the employee on the office plantilla
        Route::delete('/delete/{id}', [EmployeeController::class, 'deleteEmployee']);
        // list of supervisor
        Route::get('/list-of-Head', [EmployeeController::class, 'listOfHead']);

        // get the employee on head
        Route::get('/head', [SpmsController::class, 'getEmployeeUnderOfHead']);

        Route::get('/{controlNo}', [UnitWorkPlanController::class, 'findEmployee']);
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

        // fetch all qpef of employee
        Route::get('/all-quarter/{control_no}/{year}', [QpefController::class, 'employeeQpefAllQuarter']);

        // get qpef Q1-Q2-Q3-Q4
        Route::get('/{control_no}/{quarterly}/{year}', [QpefController::class, 'employeeQpef']);

        Route::post('/employee/quarter', [QpefController::class, 'getAllEmployeeQpefQuater']);

        // storing qpef
        Route::post('/store', [QpefController::class, 'qpefStore']);

        // updating qpef
        Route::put('/update/{qpefId}', [QpefController::class, 'qpefUpdate']);
    });


    Route::prefix('opcr')->group(function () {

        // get the opcr of office head
        Route::get('/{controlNo}/{semester}/{year}', [OpcrController::class, 'opcr']);

        // storing opcr
        Route::post('/store', [OpcrController::class, 'opcrStore']);

        //updating opcr
        Route::put('/update', [OpcrController::class, 'opcrUpdate']);
    });

    Route::prefix('pmt')->group(function () {

        // fetch only office assign on pmt account 
        Route::get('/office', [PmtController::class, 'office']);

        Route::get('/ipcr', [PmtController::class, 'listOfEmployeeIpcr']); // fetch only if they already receive by the hr staff

        Route::get('/office-employee', [PmtController::class, 'getOfficeEmployeePmt']);
    });



    Route::prefix('supervisor')->group(function () {

        // fetch the list of the ipcr of the employee 
        Route::get('/ipcr', [SupervisorController::class, 'getAdvisoryEmployeeIpcr']);

        // updating ipcr of my advisory
        Route::post('/update/ipcr', [SupervisorController::class, 'updateIpcr']);

        // get the employee on head
        Route::get('/list/employee/ipcr', [SupervisorController::class, 'getSupervisor']);
    });
});
