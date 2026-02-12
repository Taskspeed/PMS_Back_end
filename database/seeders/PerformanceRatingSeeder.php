<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PerformanceRating;
use Carbon\Carbon;

class PerformanceRatingSeeder extends Seeder
{
    public function run()
    {
        $performanceStandardIds = 30; // 🔥 CHANGE IF NEEDED

        $data = [
            ['date' => '01/05/2026', 'quantity' => 5],
            ['date' => '01/01/2026', 'quantity' => 5],
            ['date' => '01/17/2026', 'quantity' => 5],
            ['date' => '01/17/2026', 'quantity' => 5],
            ['date' => '01/17/2026', 'quantity' => 5],
            ['date' => '01/19/2026', 'quantity' => 5],
            ['date' => '01/20/2026', 'quantity' => 5],

            ['date' => '02/01/2026', 'quantity' => 5],
            ['date' => '02/05/2026', 'quantity' => 5],
            ['date' => '02/08/2026', 'quantity' => 3],
            ['date' => '02/09/2026', 'quantity' => 4],
            ['date' => '02/17/2026', 'quantity' => 5],
            ['date' => '02/17/2026', 'quantity' => 5],
            ['date' => '02/22/2026', 'quantity' => 4],
            ['date' => '02/26/2026', 'quantity' => 3],

            ['date' => '03/02/2026', 'quantity' => 5],
            ['date' => '03/03/2026', 'quantity' => 4],
            ['date' => '03/05/2026', 'quantity' => 5],
            ['date' => '03/10/2026', 'quantity' => 4],
            ['date' => '03/12/2026', 'quantity' => 5],
            ['date' => '03/17/2026', 'quantity' => 5],
            ['date' => '03/24/2026', 'quantity' => 3],
            ['date' => '03/31/2026', 'quantity' => 4],

            ['date' => '04/02/2026', 'quantity' => 5],
            ['date' => '04/07/2026', 'quantity' => 4],
            ['date' => '04/12/2026', 'quantity' => 3],
            ['date' => '04/17/2026', 'quantity' => 5],
            ['date' => '04/30/2026', 'quantity' => 5],

            ['date' => '05/03/2026', 'quantity' => 5],
            ['date' => '05/12/2026', 'quantity' => 3],
            ['date' => '05/17/2026', 'quantity' => 5],
            ['date' => '05/31/2026', 'quantity' => 5],

            ['date' => '06/02/2026', 'quantity' => 5],
            ['date' => '06/11/2026', 'quantity' => 3],
            ['date' => '06/17/2026', 'quantity' => 5],
            ['date' => '06/30/2026', 'quantity' => 5],
        ];

        foreach ($data as $row) {
            PerformanceRating::create([
                'performance_standard_id' => $performanceStandardIds,
                'control_no' => null,
                'date' => Carbon::createFromFormat('m/d/Y', $row['date'])->format('m/d/Y'),

                'quantity_actual' => $row['quantity'],
                'effectiveness_actual' => $row['quantity'] * 5,
                'timeliness_actual' => $row['quantity'] * 5,
            ]);
        }
    }
}
