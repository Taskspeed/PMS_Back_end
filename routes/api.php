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
    use App\Http\Controllers\PositionController;
    use App\Http\Controllers\UnitWorkPlanController;
    use App\Http\Controllers\VwplantillastructureController;
    use App\Models\Employee;

    // Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    //     return $request->user();

    //     //fetch data name4/position
    //     Route::get('/employee', [vwActiveController::class, 'index']);

    // });



    // Public Auth Route
    Route::post('/user_login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {

        //account
        Route::post('/user_assign', [AuthController::class, 'register']);
        Route::post('/user_logout', [AuthController::class, 'logout']);

        //for mfos

        Route::post('/add_mfo', [MfoController::class, 'store']);
        Route::get('/mfos', [MfoController::class, 'index']);
        Route::get('/mfos/soft-deleted', [MfoController::class, 'getSoftDeleted']);
        Route::post('/mfos/{id}', [MfoController::class, 'update']);

        Route::delete('/mfos/{id}', [MfoController::class, 'softDelete']);
        Route::patch('/mfos/restore/{id}', [MfoController::class, 'restore']);

        //for output
        Route::get('/allOutputs', [FOutpotController::class, 'getAllOutputs']);
        Route::post('add_output', [FOutpotController::class, 'store']); // adding
        Route::get('/outputs', [FOutpotController::class, 'index']); // fetching output
        Route::get('/outputs/soft-deleted', [FOutpotController::class, 'getSoftDeleted']); // fetch_mfo_SoftDeleted
        Route::post('/outputs/{id}', [FOutpotController::class, 'update']); // updating the output
        Route::delete('/outputs/{id}', [FOutpotController::class, 'softDelete']); // softDelete for MFO
        Route::patch('/outputs/restore/{id}', [FOutpotController::class, 'restore']); // restore soft-deleted data

        Route::get('/employee/designation', [vwActiveController::class, 'index']); // fetch employee name and designation
        Route::get('/employees-by-office', [EmployeeController::class, 'show_employee']); //fetch emplyee base on office
        Route::get('/fetch_employees', [EmployeeController::class, 'fetchEmployees']);  // fetch employee  base where employee assign division, section, unit

         //for employee
        Route::post('/add/employee', [EmployeeController::class, 'store']); // adding
        Route::post('/employees/{id}/rank', [EmployeeController::class, 'updateRank']); //updating the rank of employee
        Route::get('/search-employees', [EmployeeController::class, 'searchEmployees']); // search employee
        Route::get('/employee/counts', [EmployeeController::class, 'getEmployeeCounts']); // employee counts

        Route::get('/employee', [EmployeeController::class, 'index']);   // Fetch only active (non-deleted) mfo
        Route::get('/employee/soft-deleted', [EmployeeController::class, 'getSoftDeleted']); // fetch_mfo_SoftDeleted
        Route::delete('/employee/{id}', [EmployeeController::class, 'softDelete']);  // softDelete for MFO
        Route::patch('/employee/restore/{id}', [EmployeeController::class, 'restore']);     // restore soft-deleted data

        // Unit Work Plan route
        Route::get('/employees/divisions', [UnitWorkPlanController::class, 'getDivisionsByOffice']); // get the division by office
        Route::get('/employees', [UnitWorkPlanController::class, 'getEmployeesByDivision']);



        //mfo on UnitWorkPlan
        Route::get('/f_category', [UnitWorkPlanController::class, 'category']); //fetching category
        Route::get('/mfo', [UnitWorkPlanController::class, 'getMfosByCategory']); // For fetching MFOs by category
        Route::get('/output', [UnitWorkPlanController::class, 'getOutputsByMfo']); // Add this for fetching outputs
        Route::get('/getSupportOutputs', [UnitWorkPlanController::class, 'getSupportOutputs']);
        Route::get('/SupportOutputs', [UnitWorkPlanController::class, 'SupportOutputs']);


        Route::get('/user_data', [MfoController::class, 'getUserData']);
        // Route::get('/allOutputs', [UnitWorkPlanController::class, 'getAllOutputs']);


        Route::get('/user_account', [AuthController::class, 'user_account']); // user account and role
        Route::get('/office/structure', [VwplantillastructureController::class, 'index']); //plantilla structure
        Route::get('/user_activity_log', [Activity_log_Controller::class, 'index']); //user_activity_log

        Route::post('/unit_work_plan/store', [UnitWorkPlanController::class, 'store']); //for adding unit work plan

        Route::get('/unit_work_plan/index', [UnitWorkPlanController::class, 'unit_work_plan']); //for fetching unit work plan
        Route::get('/employee/{id}/competencies', [UnitWorkPlanController::class, 'getEmployeeCompetencies']);
        Route::get('/position', [EmployeeController::class, 'index_position']);

        Route::get('/division/status', [UnitWorkPlanController::class, 'get_division_status']);
        Route::get('/division/employee/performance', [UnitWorkPlanController::class, 'get_employee_performance']);
        Route::post('/employee/{id}/update/unitworkplan', [UnitWorkPlanController::class, 'updateEmployee']); // employee unit work plan

    });



    // Route::get('/division/status', [UnitWorkPlanController::class, 'get_division_status']);
    // Route::get('/division/employee/performance', [UnitWorkPlanController::class, 'get_employee_performance']);


    // Route::get('/office/structure', [VwplantillastructureController::class, 'index']); //plantilla structure
    // Route::get('/user_activity_log', [Activity_log_Controller::class, 'index']); //user_activity_log



    //fetching
    Route::get('/fetch_f_category', [FCategoryController::class, 'index']); //fetching category

    Route::get('/fetch_office', [OfficeController::class, 'index']); //fetch office

    Route::get('/fetch_mfo', [MfoController::class, 'index_data']); // mfo

    Route::get('/Outputs', [FOutpotController::class, 'Outputs']);

    // Route::get('/user_account', [AuthController::class, 'user_account']); // user account and role

    // Route::get('/offices', [OfficeController::class, 'index']);

