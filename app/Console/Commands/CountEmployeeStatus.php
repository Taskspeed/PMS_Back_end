<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CountEmployeeStatus extends Command
{
    protected $signature = 'employee:count-status';
    protected $description = 'Automatically count employee status per semester';

    public function handle()
    {
        $statuses = ['REGULAR', 'Job-Order', 'Others'];

        // Determine current semester
        $month = now()->month;
        $year = now()->year;

        $semester = $month >= 1 && $month <= 6 ? 'Jan-June' : 'July-Dec';

        // Count employees by status
        $data = DB::table('vwActive')
            ->select('Status', DB::raw('COUNT(*) as total'))
            ->whereIn('Status', $statuses)
            ->groupBy('Status')
            ->get()
            ->keyBy('Status');

        // Save to table
        foreach ($statuses as $status) {
            DB::table('employee_semester_counts')->updateOrInsert(
                [
                    'year' => $year,
                    'semester' => $semester,
                    'status' => $status,
                ],
                [
                    'employee_count' => $data[$status]->total ?? 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $this->info("Employee status counted for {$semester} {$year}");
    }
}
