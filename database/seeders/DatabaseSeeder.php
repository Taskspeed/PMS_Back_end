<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    protected static ?string $password;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => static::$password ??= Hash::make('password'),
        //     'role'=>'admin',
        //     'office'=> 'OFFICE OF THE CITY HUMAN RESOURCE MANAGEMENT OFFICER'
        // ]);
            $this->call([OfficeSeeder::class]);
            $this->call([RoleSeeder::class]);
            $this->call([UserSeeder::class]);
            $this->call([F_CategorySeeder::class]);
            $this->call([CoreSeeder::class]);

            // $this->call(PerformanceRatingSeeder::class);


    }
}
