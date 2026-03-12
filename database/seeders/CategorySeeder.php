<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Category::create([
            'categories_name' => 'Production',

        ]);

        Category::create([
            'categories_name' => 'Quality Control',

        ]);

        Category::create([
            'categories_name' => 'Decision-Making',

        ]);
    }
}
