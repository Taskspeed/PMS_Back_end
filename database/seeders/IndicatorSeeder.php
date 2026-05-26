<?php

namespace Database\Seeders;

use App\Models\Indicator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

       $indicators = [
            ['indicator_name' => 'supervised', 'category_id' => 3],
            ['indicator_name' => 'conducted', 'category_id' => 1],
            ['indicator_name' => 'facilitated', 'category_id' => 1],
            ['indicator_name' => 'managed', 'category_id' => 2],
            ['indicator_name' => 'designed', 'category_id' => 1],
            ['indicator_name' => 'drafted', 'category_id' => 1],
            ['indicator_name' => 'prevented', 'category_id' => 1],
            ['indicator_name' => 'delivered', 'category_id' => 1],
            ['indicator_name' => 'submitted', 'category_id' => 1],
            ['indicator_name' => 'inspected', 'category_id' => 1],
            ['indicator_name' => 'deployed', 'category_id' => 1],
            ['indicator_name' => 'rendered', 'category_id' => 1],
            ['indicator_name' => 'verified', 'category_id' => 2],
            ['indicator_name' => 'created', 'category_id' => 1],
            ['indicator_name' => 'edited', 'category_id' => 1],
            ['indicator_name' => 'review', 'category_id' => 2],
            ['indicator_name' => 'develop', 'category_id' => 1],
            ['indicator_name' => 'assess', 'category_id' => 2],
            ['indicator_name' => 'validate', 'category_id' => 2],
            ['indicator_name' => 'prepared', 'category_id' => 1],
            ['indicator_name' => 'approved', 'category_id' => 3],
            ['indicator_name' => 'signed', 'category_id' => 3],
            ['indicator_name' => 'attended', 'category_id' => 1],
        ];
        foreach ($indicators as $indicator) {
            Indicator::create([
                'indicator_name' => $indicator['indicator_name'],
                'category_id' => $indicator['category_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
}
}