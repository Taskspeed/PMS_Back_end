<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('employee:store-status')
    ->yearlyOn(6, 30, '23:59')->timezone('Asia/Manila');  // June 30

Schedule::command('employee:store-status')
    ->yearlyOn(12, 31, '23:59')->timezone('Asia/Manila');   // December 31

Schedule::command('employee:store-status')
    ->yearlyOn(2, 18, '13:32')->timezone('Asia/Manila');   // February 18 at 12:30

// Schedule::command('employee:store-status')
//     ->everyMinute(2, 18, '12:47');   // February 18 at 12:30
