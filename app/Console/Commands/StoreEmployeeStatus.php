<?php

namespace App\Console\Commands;

use App\Services\DashboardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StoreEmployeeStatus extends Command
{
    protected $signature = 'employee:store-status';
    protected $description = 'Automatically store employee status at end of June and December';


    // store status of employee 
    public function handle(DashboardService $dashboardService)
    {
        Log::info('Scheduler triggered at: ' . now());
        $dashboardService->storeEmployeeStatus();
        $this->info('Employee status stored successfully.');
    }
}
