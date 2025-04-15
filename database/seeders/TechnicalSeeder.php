<?php

namespace Database\Seeders;

use App\Models\Technical;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TechnicalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Technical::create([
            'Planning and Organizing'     => 4,
            'Monitoring and Evaluation'   => 4,
            'Records Management'          => 4,
            'Partnering and Networking'   => 4,
            'Process Management'          => 4,
            'Attention to Detail'         => 4,
        ]);

        Technical::create([
            'Planning and Organizing'     => 3,
            'Monitoring and Evaluation'   => 3,
            'Records Management'          => 3,
            'Partnering and Networking'   => 3,
            'Process Management'          => 3,
            'Attention to Detail'         => 3,
        ]);

        Technical::create([
            'Planning and Organizing'     => 0,
            'Monitoring and Evaluation'   => 0,
            'Records Management'          => 3,
            'Partnering and Networking'   => 0,
            'Process Management'          => 3,
            'Attention to Detail'         => 3,
        ]);

        Technical::create([
            'Planning and Organizing'     => 0,
            'Monitoring and Evaluation'   => 0,
            'Records Management'          => 2,
            'Partnering and Networking'   => 0,
            'Process Management'          => 2,
            'Attention to Detail'         => 2,
        ]);

        Technical::create([
            'Planning and Organizing'     => 0,
            'Monitoring and Evaluation'   => 0,
            'Records Management'          => 1,
            'Partnering and Networking'   => 0,
            'Process Management'          => 1,
            'Attention to Detail'         => 1,
        ]);
    }
}
