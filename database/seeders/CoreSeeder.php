<?php

namespace Database\Seeders;

use App\Models\Core;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Core::create([
            'Delivering Service Excellence' => 4,
            'Exemplifying Integrity' => 4,
            'Interpersonal Skills' => 4,
        ]);
      Core::create([
            'Delivering Service Excellence' => 3,
            'Exemplifying Integrity' => 3,
            'Interpersonal Skills' => 3,
        ]);
        Core::create([
                'Delivering Service Excellence' => 2,
                'Exemplifying Integrity' => 2,
                'Interpersonal Skills' => 2,
            ]);
        Core::create([
                'Delivering Service Excellence' => 1,
                'Exemplifying Integrity' => 1,
                'Interpersonal Skills' => 1,
         ]);
    }
}
