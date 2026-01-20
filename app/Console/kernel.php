<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // register your commands here if needed
    ];

    protected function schedule(Schedule $schedule)
    {
        // Run every June 30 at 23:59
        $schedule->command('employee:count-status')
            ->yearlyOn(6, 30, '23:59');

        // Run every December 31 at 23:59
        $schedule->command('employee:count-status')
            ->yearlyOn(12, 31, '23:59');
    }


    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
