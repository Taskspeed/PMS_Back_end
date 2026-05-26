<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
          Rank::create([
            'rank_name' => 'Managerial',

        ]);

        Rank::create([
            'rank_name' => 'Rank-in-File',

        ]);

        Rank::create([
            'rank_name' => 'Supervisory',

        ]);
    }
}
