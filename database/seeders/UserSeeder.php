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
            'office_id' => 14, 
            'role_id' => 3, 
            'remember_token' => Str::random(24),
            'username' => 'admin',
            'active' => true
            

        ]);

        User::create([
            'name' => 'DENEIL TOMENIO', // office
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 15, // Ensure this office exists in the offices table
            'role_id' => 1, // Ensure this role exists in the roles table
            'remember_token' => Str::random(24),
            'username' => 'cictmo',
            'active' => true,
            'control_no' => '022395'
        ]);

        User::create([
            'name' => 'CLIFORD MILLAN', // planning
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 18, // Ensure this office exists in the offices table
            'role_id' => 2, // planning
            'remember_token' => Str::random(24),
            'username' => 'pdo',
            'active' => true,
            'control_no' => '022485'
        ]);


        User::create([
            'name' => 'REIL IGONA', // pmt
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 14, // Ensure this office exists in the offices table
            'role_id' => 5, // pmt
            'remember_token' => Str::random(24),
            'username' => 'pmt',
            'active' => true,
            'control_no' => '011690'
        ]);

        User::create([
            'name' => 'JOHN VIR TAUTHO', // Receiving officer
            'password' => static::$password ??= Hash::make('admin'),
            'office_id' => 15, //
            'role_id' => 6, // receiving office
            'remember_token' => Str::random(24),
            'username' => 'niel',
            'active' => true,
            'control_no' => '000998'
        ]);
    }
}
