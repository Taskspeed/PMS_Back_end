<?php

namespace Database\Seeders;

use App\Models\PerformanceRating;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceRatingSeeder extends Seeder
{
    public function run()
    {
        // $performanceStandardIds = [14, 30, 31, 32, 33, 34, 46, 47, 48, 49, 52, 53, 54, 91, 92, 93, 94, 95, 96, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 201, 202, 203, 204, 211, 212, 213, 216, 217, 224, 225, 226, 227, 232, 233, 234, 235, 236, 237, 238, 239, 240];

        $requestedIds = [14, 30, 31, 32, 33, 34, 46, 47, 48, 49, 52, 53, 54, 91, 92, 93, 94, 95, 96, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 201, 202, 203, 204, 211, 212, 213, 216, 217, 224, 225, 226, 227, 232, 233, 234, 235, 236, 237, 238, 239, 240];

    $performanceStandardIds = DB::table('performance_standards')
        ->whereIn('id', $requestedIds)
        ->pluck('id')
        ->toArray();
        $startDate = Carbon::create(2026, 6, 1);
        $endDate   = Carbon::create(2026, 12, 31);

        $period = CarbonPeriod::create($startDate, $endDate)
            ->filter(fn($date) => $date->isWeekday());
            foreach ($performanceStandardIds as $standardId) {
                foreach ($period as $date) {

                    $quantity = rand(3, 5);

                    $rating = PerformanceRating::create([
                        'performance_standard_id' => $standardId,
                        'control_no'              => null,
                        'date'                    => $date->format('m/d/Y'),
                        'quantity_actual'         => $quantity,
                        'effectiveness_actual'    => $quantity * 5,
                        'timeliness_actual'       => $quantity * 5,
                        'status'                  => 'Pending',
                    ]);

                    $rating->dropdownRating()->create([
                        'quantity'      => rand(3, 5),
                        'effectiveness' => rand(3, 5),
                        'timeliness'    => rand(3, 5),
                    ]);
                }
            }
    }
}
