<?php

namespace Database\Seeders;

use App\Models\Leadership;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Leadership::create([
            'Thinking Strategically and Creatively' => 4,
            'Problem Solving and Decision Making' => 4,
            'Building Collaborative & Inclusive Working Relationships' => 4,
            'Managing Performance & Coaching for Results' => 4,

        ]);
        Leadership::create([
            'Thinking Strategically and Creatively' => 4,
            'Problem Solving and Decision Making' => 4,
            'Building Collaborative & Inclusive Working Relationships' => 4,
            'Managing Performance & Coaching for Results' => 3,

        ]);
        Leadership::create([
            'Thinking Strategically and Creatively' => 3,
            'Problem Solving and Decision Making' => 3,
            'Building Collaborative & Inclusive Working Relationships' => 3,
            'Managing Performance & Coaching for Results' => 3,

        ]);
        Leadership::create([
            'Thinking Strategically and Creatively' => 2,
            'Problem Solving and Decision Making' => 2,
            'Building Collaborative & Inclusive Working Relationships' => 2,
            'Managing Performance & Coaching for Results' => 0,

        ]);
        Leadership::create([
            'Thinking Strategically and Creatively' => 1,
            'Problem Solving and Decision Making' => 1,
            'Building Collaborative & Inclusive Working Relationships' => 1,
            'Managing Performance & Coaching for Results' => 0,

        ]);
        Leadership::create([
            'Thinking Strategically and Creatively' => 0,
            'Problem Solving and Decision Making' => 0,
            'Building Collaborative & Inclusive Working Relationships' => 0,
            'Managing Performance & Coaching for Results' => 0,

        ]);

    }
}
