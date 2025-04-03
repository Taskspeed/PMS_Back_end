<?php

namespace Database\Seeders;

use App\Models\F_category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class F_CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        F_category::create([ 'name'=>'A. STRATEGIC FUNCTION']);
        F_category::create(['name' => 'B. CORE FUNCTION',]);
        F_category::create(['name' => 'C. SUPPORT FUNCTION', ]);
    }
}
