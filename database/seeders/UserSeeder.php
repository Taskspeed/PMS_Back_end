<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
      protected static ?string $password;
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'admin',
        //     'password' => static::$password ??= Hash::make('password'),
        //     'office' => '1'
        // ]);
    }
}
