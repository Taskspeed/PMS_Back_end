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

            ['name' => 'office_admin', 'label' => 'Can manage office-specific settings and users'], // 1
            ['name' => 'planning_admin', 'label' => 'Can manage planning-related functions and users'], // 2
            ['name' => 'hr_admin', 'label' => 'Creates accounts and manages the system'], // 3
            ['name' => 'supervisor_admin', 'label' => 'Can rate Qpef'], // 4
            ['name' => 'pmt_admin', 'label' => 'Performance Management Team — evaluations and monitoring'], // 5
            ['name' => 'receiving_officer', 'label' => 'Handles receiving and processing of documents and items'], // 6
            ['name' => 'receiving_staff_planning', 'label' => 'Handles receiving and processing of documents and items'], // 7

        ];

        foreach ($roles as $role) {
            Role::create($role); // ✅ pass the whole $role array
        }
    }
}
