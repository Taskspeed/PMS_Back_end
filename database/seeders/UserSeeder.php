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

        User::create([
            'name' => 'admin', // hr admin
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 20, // Ensure this office exists in the offices table
            'role_id' => 3, // Ensure this role exists in the roles table
            'remember_token' => Str::random(24),
            'username' => 'admin'

        ]);

        User::create([
            'name' => 'deniel', // office
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 16, // Ensure this office exists in the offices table
            'role_id' => 1, // Ensure this role exists in the roles table
            'remember_token' => Str::random(24),
            'username' => 'deniel',
        ]);

        User::create([
            'name' => 'cliford', // planning
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 23, // Ensure this office exists in the offices table
            'role_id' => 2, // Ensure this role exists in the roles table
            'remember_token' => Str::random(24),
            'username' => 'cliford',
        ]);

    
        User::create([
            'name' => 'jeremie', // pmt
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 20, // Ensure this office exists in the offices table
            'role_id' => 4, // Ensure this role exists in the roles table
            'remember_token' => Str::random(24),
            'username' => 'jeremie',
        ]);

        User::create([
            'name' => 'niel', // supervisor
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 20, // Ensure this office exists in the offices table
            'role_id' => 5, // Ensure this role exists in the roles table
            'remember_token' => Str::random(24),
            'username' => 'niel',
        ]);
    }
}
