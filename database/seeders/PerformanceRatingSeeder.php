<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerformanceRating;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PerformanceRatingSeeder extends Seeder
{
    public function run()
    {
        $performanceStandardIds = [201, 202, 203, 204, 211, 212, 213, 239, 240, 216, 217, 232, 233, 234, 235, 236, 237, 238,];

        $startDate = Carbon::create(2026, 7, 1);
        $endDate   = Carbon::create(2026, 12, 31);

        $period = CarbonPeriod::create($startDate, $endDate)
            ->filter(fn($date) => $date->isWeekday());

        foreach ($performanceStandardIds as $standardId) {
            foreach ($period as $date) {

                $quantity = rand(3, 5);

                PerformanceRating::create([
                    'performance_standard_id' => $standardId,
                    'control_no'              => null,
                    'date'                    => $date->format('m/d/Y'), // ✅ FIXED
                    'quantity_actual'         => $quantity,
                    'effectiveness_actual'    => $quantity * 5,
                    'timeliness_actual'       => $quantity * 5,
                ]);
            }
        }
    }
}
