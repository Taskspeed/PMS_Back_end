<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ⚠️ Deletes all existing roles first
        Role::truncate();

        $roles = [
            // 'office_admin',
            // 'planning_admin',
            // 'hr_admin',
            // 'pmt_admin',
            // 'supervisor_admin',

            ['name' => 'office_admin', 'label' => 'Office Admin'], // 1
            ['name' => 'planning_admin', 'label' => 'Planning Admin'], // 2
              ['name' => 'hr_admin', 'label' => 'HR Admin'], // 3
          ['name' => 'supervisor_admin', 'label' => 'Supervisor Admin'], // 4
            ['name' => 'pmt_admin', 'label' => 'PMT Admin'], // 5
        
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
