<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // Position::create([
        //     'name' => 'INFORMATION SYSTEMS ANALYST III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);
        Position::create([
            'name' => 'INFORMATION TECHNOLOGY OFFICER I',
            'sg' => 9,
            'level' => 2,
            'core_id' => 2,
            'technical_id' => 4,
            'leadership_id' => 5,
        ]);
        Position::create([
            'name' => 'INFORMATION SYSTEMS ANALYST III',
            'sg'=> 9,
            'level'=>1,
            'core_id' => 3,
            'technical_id' => 5,
            'leadership_id' => 6,
        ]);

        // Position::create([
        //     'name' => 'CITY ACCOUNTANT I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY ADMINISTRATOR I (CT)',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY AGRICULTURIST I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY ARCHITECT I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY ASSESSOR I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY BUDGET OFFICER I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY CIVIL REGISTRAR I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY ENGINEER I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY ENVIRONMENT AND NATURAL RESOURCES OFFICER I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY GENERAL SERVICES OFFICER I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY GOVERNMENT DEPARTMENT HEAD I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY HEALTH OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY LEGAL OFFICER I (CT)',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY PLANNING AND DEVELOPMENT COORDINATOR I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY SOCIAL WELFARE AND DEVELOPMENT OFFICER I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY TREASURER I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY VETERINARIAN I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'SECRETARY TO THE SANGGUNIANG PANLUNGSOD',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'ATTORNEY IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CITY ASSISTANT TREASURER I',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'CG ASSISTANT DEPARTMENT HEAD I (CLO/CEO/CHO/CSWDO)',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'DENTIST IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'MEDICAL OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 1,
        // ]);

        // Position::create([
        //     'name' => 'ACCOUNTANT IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'ADMINISTRATIVE OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);



        // Position::create([
        //     'name' => 'ARCHITECT IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'BUDGET OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'CASHIER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'DEVELOPMENT MANAGEMENT OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'ENGINEER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'HUMAN RESOURCE MANAGEMENT OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'INFORMATION OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'INTERNAL AUDITOR IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'LICENSING OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL ASSESSMENT OPERATIONS OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL DISASTER RISK REDUCTION MANAGEMENT OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL LEGISLATIVE STAFF OFFICER V',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL REVENUE COLLECTION OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL TREASURY OPERATIONS OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'MARKET SUPERVISOR IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'PLANNING OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT DEVELOPMENT OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT EVALUATION OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'PUBLIC SERVICES OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'REGISTRATION OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'SLAUGHTERHOUSE MASTER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'SOCIAL WELFARE OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'SUPERVISING AGRICULTURIST',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'SUPERVISING ENVIRONMENTAL MANAGEMENT SPECIALIST',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'SUPERVISING LABOR AND EMPLOYMENT OFFICER',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'SUPERVISING PUBLIC UTILITIES REGULATION OFFICER',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'TAX MAPPER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'VETERINARIAN IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'ZONING OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'DENTIST III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 2,
        // ]);

        // Position::create([
        //     'name' => 'ACCOUNTANT III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);



        // Position::create([
        //     'name' => 'ARCHITECT III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'ENGINEER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'HOUSING AND HOMESITE REGULATION OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);


        // Position::create([
        //     'name' => 'LOCAL LEGISLATIVE STAFF OFFICER IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'NURSE IV',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR LABOR AND EMPLOYMENT OFFICER',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'VETERINARIAN III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'ADMINISTRATIVE OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'BUDGET OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'CASHIER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNITY AFFAIRS OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'ECONOMIST III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'FISCAL EXAMINER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'HEALTH EDUCATION AND PROMOTION OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'HUMAN RESOURCE MANAGEMENT OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'INFORMATION OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'INTERNAL AUDITOR III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'LIBRARIAN III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'LICENSING OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL ASSESSMENT OPERATIONS OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL DISASTER RISK REDUCTION MANAGEMENT OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL REVENUE COLLECTION OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL TREASURY OPERATIONS OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'MANAGEMENT AND AUDIT ANALYST III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'MARKET SUPERVISOR III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'MEDICAL TECHNOLOGIST III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'NUTRITIONIST - DIETITIAN III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'PHARMACIST III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'PLANNING OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'POPULATION PROGRAM OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT DEVELOPMENT OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT EVALUATION OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'PUBLIC SERVICES OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'RECORDS OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'REGISTRATION OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SECURITY OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR AGRICULTURIST',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR AQUACULTURIST',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR COOPERATIVES DEVELOPMENT SPECIALIST',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR ENVIRONMENTAL MANAGEMENT SPECIALIST',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR PUBLIC UTILITIES REGULATION OFFICER',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR TOURISM OPERATIONS OFFICER',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SLAUGHTERHOUSE MASTER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SOCIAL WELFARE OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SPORTS DEVELOPMENT OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'SUPPLY OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'TAX MAPPER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);


        // Position::create([
        //     'name' => 'TRAFFIC OPERATIONS OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'ZONING OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);



        // Position::create([
        //     'name' => 'NURSE III',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'ACCOUNTANT II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'ARCHITECT II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'ENGINEER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'HOUSING AND HOMESITE REGULATION OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'LABOR AND EMPLOYMENT OFFICER III',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'ADMINISTRATIVE OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'AGRICULTURIST II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'AQUACULTURIST II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'BUDGET OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNITY AFFAIRS OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'COMPUTER MAINTENANCE TECHNOLOGIST II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'COMPUTER PROGRAMMER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'DEVELOPMENT MANAGEMENT OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'ENVIRONMENTAL MANAGEMENT SPECIALIST II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'FISCAL EXAMINER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'HUMAN RESOURCE MANAGEMENT OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'INFORMATION OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'LICENSING OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL ASSESSMENT OPERATIONS OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL DISASTER RISK REDUCTION MANAGEMENT OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL REVENUE COLLECTION OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);


        // Position::create([
        //     'name' => 'LOCAL TREASURY OPERATIONS OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'MANAGEMENT AND AUDIT ANALYST II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'MANPOWER DEVELOPMENT OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'MEDICAL TECHNOLOGIST II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'NURSE II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'PHARMACIST II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'PLANNING OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'POPULATION PROGRAM OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT DEVELOPMENT OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT EVALUATION OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'PUBLIC SERVICES OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'PUBLIC UTILITIES REGULATION OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'SECURITY OFFICER II (CT)',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'SOCIAL WELFARE OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'STATISTICIAN II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'TAX MAPPER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'TOURISM OPERATIONS OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'ZONING OFFICER II',
        //     'core_id' => 1,
        //     'technical_id' => 2,
        //     'leadership_id' => 4,
        // ]);




        // Position::create([
        //     'name' => 'CASHIER II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'HEALTH EDUCATION AND PROMOTION OFFICER II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'LIBRARIAN II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'MARKET SUPERVISOR II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'MUSEUM RESEARCHER II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'RECORDS OFFICER II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'REGISTRATION OFFICER II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'SUPPLY OFFICER II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);



        // Position::create([
        //     'name' => 'LOCAL LEGISLATIVE STAFF OFFICER II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'MIDWIFE III',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'RADIOLOGIC TECHNOLOGIST II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'ENGINEER I',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LANDSCAPING SUPERVISOR',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LEGAL ASSISTANT II',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);



        // Position::create([
        //     'name' => 'ADMINISTRATIVE OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'AGRICULTURIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'AQUACULTURIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'BUDGET OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNITY AFFAIRS OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'COMPUTER MAINTENANCE TECHNOLOGIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'COOPERATIVE DEVELOPMENT SPECIALIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);


        // Position::create([
        //     'name' => 'DATA CONROLLER III',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'DEVELOPMENT MANAGEMENT OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'ECONOMIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'ENVIRONMENTAL MANAGEMENT SPECIALIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'FISCAL EXAMINER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'HOME MANAGEMENT SPECIALIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'HUMAN RESOURCE MANAGEMENT OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'INFORMATION OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LABOR AND EMPLOYMENT OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LIBRARIAN I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LICENSING OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL ASSESSMENT OPERATIONS OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL DISASTER RISK REDUCTION MANAGEMENT OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL LEGISLATIVE STAFF OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL REVENUE COLLECTION OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL TREASURY OPERATIONS OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'MANAGEMENT AND AUDIT ANALYST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'MEDICAL TECHNOLOGIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'PLANNING OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'PRIVATE SECRETARY I (CT)',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT DEVELOPMENT OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'PROJECT EVALUATION OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'PSYCHOLOGIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'PUBLIC SERVICES OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'SOCIAL WELFARE OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'SOCIOLOGIST I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'STATISTICIAN I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'TAX MAPPER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'ZONING OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'AGRICULTURAL TECHNOLOGIST',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'AQUACULTURAL TECHNOLOGIST',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'CASHIER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);
        // Position::create([
        //     'name' => 'HOME MANAGEMENT TECHNOLOGIST',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'INFORMATION SYSTEMS RESEARCHER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'LEGAL ASSISTANT I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'MARKET SUPERVISOR I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'RECORDS OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'SECURITY AGENT II (CT)',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'SUPPLY OFFICER I',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'ECONOMIC RESEARCHER',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);


        // Position::create([
        //     'name' => 'SANITATION INSPECTOR VI',
        //     'core_id' => 1,
        //     'technical_id' => 1,
        //     'leadership_id' => 3,
        // ]);

        // Position::create([
        //     'name' => 'COMPUTER OPERATOR IV',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'LABORATORY INSPECTOR III',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 4,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNICATIONS EQUIPMENT OPERATOR V',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'DATA CONTROLLER IV',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'SANITATION INSPECTOR IV',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'STENOGRAPHIC REPORTER IV',
        //     'core_id' => 2,
        //     'technical_id' => 3,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'COMPUTER OPERATOR III',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'ARTIST ILLUSTRATOR III',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'CONSTRUCTION AND MAINTENANCE GENERAL FOREMAN',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'DATA CONTROLLER III',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'DRAFTSMAN III',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'ELECTRONICS & COMMUNICATIONS EQUIPMENT TECHNICIAN III',
        //     'core_id' => 2,
        //     'technical_id' => 4,
        //     'leadership_id' => 5,
        // ]);

        // Position::create([
        //     'name' => 'BOOKBINDER IV',
        //     'core_id' => 3,
        //     'technical_id' => 4,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL LEGISLATIVE STAFF ASSISTANT III',
        //     'core_id' => 3,
        //     'technical_id' => 4,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PARK MAINTENANCE GENERAL FOREMAN',
        //     'core_id' => 3,
        //     'technical_id' => 4,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PHOTOGRAPHER III',
        //     'core_id' => 3,
        //     'technical_id' => 4,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ASSESSMENT CLERK III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'BUYER III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNICATIONS EQUIPMENT OPERATOR III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'COMPUTER OPERATOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ELECTRICIAN FOREMAN',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MACHINIST III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MECHANIC III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MECHANICAL PLANT OPERATOR III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'REVENUE COLLECTION CLERK III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'SECRETARY II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'SENIOR BOOKKEEPER',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'STENOGRAPHER III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'STOREKEEPER III',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'WELDER FOREMAN',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ADMINISTRATIVE ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);
        // Position::create([
        //     'name' => 'ARTIST ILLUSTRATOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ASSISTANT REGISTRATION OFFICER',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'BOOKKEEPER I',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'BUDGETING ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'CARPENTER FOREMAN',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'CLERK IV',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNITY AFFAIRS ASSISTANT II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'CONSTRUCTION AND MAINTENANCE FOREMAN',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'DATA CONTROLLER II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'DRAFTSMAN II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ENGINEERING ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'FOOD-DRUG INSPECTOR',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'HOUSING AND HOMESITE REGULATION ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'HUMAN RESOURCE MANAGEMENT ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LABOR GENERAL FOREMAN',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LICENSE INSPECTOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LIVESTOCK INSPECTOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL DISASTER RISK REDUCTION MANAGEMENT ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL TREASURY OPERATIONS ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MARKET INSPECTOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MEAT INSPECTOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PAINTER FOREMAN',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PLANNING ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);
        // Position::create([
        //     'name' => 'PROJECT EVALUATION ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'SANITATION INSPECTOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'SOCIAL WELFARE ASSISTANT',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'SPECIAL AGENT I',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'WAREHOUSEMAN II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ZONING INSPECTOR II',
        //     'core_id' => 3,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);


        // Position::create([
        //     'name' => 'ASSISTANT NUTRITIONIST - DIETITIAN',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'AUDIO-VISUAL EQUIPMENT OPERATOR III',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'BOOKBINDER III',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNITY DEVELOPMENT ASSISTANT I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'COMPUTER OPERATOR I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PHOTOGRAPHER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'REPRODUCTION MACHINE OPERATOR III',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'TOURISM OPERATIONS ASSISTANT',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ACCOUNTING CLERK II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ASSESSMENT CLERK II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'AUDIO-VISUAL AIDE TECHNICIAN I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'CLERK III',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'COMMUNICATIONS EQUIPMENT OPERATOR II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'DATA CONROLLER I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ELECTRICIAN II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'FISCAL CLERK II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'HEAVY EQUIPMENT OPERATOR II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LABOR FOREMAN',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LICENSE INSPECTOR I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MACHINIST II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MARKET INSPECTOR I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);


        // Position::create([
        //     'name' => 'MECHANIC II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MOTOR POOL DISPATCHER',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'NURSING ATTENDANT II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PARKING AIDE III',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PUBLIC SERVICES FOREMAN',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'STOREKEEPER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'WAREHOUSEMAN I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'WELDER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'AUDIO-VISUAL EQUIPMENT OPERATOR II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'CARPENTER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'CONSTRUCTION AND MAINTENANCE CAPATAZ',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LINEMAN II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'MASON II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PLUMBER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'ANIMAL KEEPER I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'BOOKBINDER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'CLERK II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'DRIVER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'HEAVY EQUIPMENT OPERATOR I',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LOCAL LEGISLATIVE STAFF EMPLOYEE II (UTILITY WORKER, MESSENGER)',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'PERSONAL DRIVER (CT)',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'REPRODUCTION MACHINE OPERATOR II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'SOCIAL WELFARE AIDE',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'TAX MAPPING AIDE',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'LABORER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'TICKET CHECKER',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);

        // Position::create([
        //     'name' => 'UTIILITY WORKER II',
        //     'core_id' => 4,
        //     'technical_id' => 5,
        //     'leadership_id' => 6,
        // ]);
    }
}
