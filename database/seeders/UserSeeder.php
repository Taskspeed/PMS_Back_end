<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
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
        User::create([
            'name' => 'admin',
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 20, // Ensure this office exists in the offices table
            'role_id' => 3, // Ensure this role exists in the roles table
            'remember_token' => Str::random(24),
        ]);

    }
}
