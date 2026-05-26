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

        $roles = [

            ['name' => 'office_admin', 'label' => 'Office Admin'], // 1
            ['name' => 'planning_admin', 'label' => 'Planning Admin'], // 2
            ['name' => 'hr_admin', 'label' => 'HR Admin'], // 3
            ['name' => 'supervisor_admin', 'label' => 'Supervisor Admin'], // 4
            ['name' => 'pmt_admin', 'label' => 'PMT Admin'], // 5
            ['name' => 'receiving_officer', 'label' => 'Receiving Officer'], // 5

        ];

        foreach ($roles as $role) {
            Role::create($role); // ✅ pass the whole $role array
        }
    }
}
