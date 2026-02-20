<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PerformanceConfigurations;
use App\Models\PerformanceStandard;
use App\Models\StandardOutcome;
use App\Models\TargetPeriod;
use App\Models\Tracker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitWorkPlanService
{
    /**
     * Create a new class instance.
     */
    // public function __construct()
    // {
    //     //
    // }
    public function store($validated) //  old working store function
    {
        $user = Auth::user();

        DB::beginTransaction(); // Start transaction

        try {
            foreach ($validated['employees'] as $employeeData) {
                // Check if already exists
                $existing = TargetPeriod::where('control_no', $employeeData['control_no'])
                    ->where('semester', $employeeData['semester'])
                    ->where('year', $employeeData['year'])
                    ->first();

                if ($existing) {
                    throw new \Exception("Employee ({$employeeData['control_no']}) already has a Unit Work Plan for {$employeeData['semester']} {$employeeData['year']}.");
                }

                // Create Target Period
                $targetPeriod = TargetPeriod::create([
                    'control_no' => $employeeData['control_no'],
                    'semester'   => $employeeData['semester'],
                    'year'       => $employeeData['year'],
                    'office'     => $employeeData['office'],
                    'office2'    => $employeeData['office2'] ?? null,
                    'group'      => $employeeData['group'] ?? null,
                    'division'   => $employeeData['division'] ?? null,
                    'section'    => $employeeData['section'] ?? null,
                    'unit'       => $employeeData['unit'] ?? null,
                    'office_id'  => $user->office_id,
                    'status'     => 'Draft',
                ]);

                // Create Performance Standards
                foreach ($employeeData['performance_standards'] as $standard) {
                    $performanceStandard = PerformanceStandard::create([
                        'target_period_id'      => $targetPeriod->id,
                        'category'              => $standard['category'],
                        'mfo'                   => $standard['mfo'],
                        'output'                => $standard['output'],
                        'output_name'           => $standard['output_name'],
                        'core'                  => $standard['core_competency'] ?? null,
                        'technical'             => $standard['technical_competency'] ?? null,
                        'leadership'            => $standard['leadership_competency'] ?? null,
                        'performance_indicator' => $standard['performance_indicator'],
                        'success_indicator'     => $standard['success_indicator'],
                        'required_output'       => $standard['required_output'],
                    ]);

                    foreach ($standard['ratings'] as $rating) {
                        $standard_outcome = StandardOutcome::create([
                            'performance_standard_id' => $performanceStandard->id,
                            'rating'                  => $rating['rating'],
                            'quantity_target'         => $rating['quantity'],
                            'effectiveness_criteria'  => $rating['effectiveness'],
                            'timeliness_range'        => $rating['timeliness'],
                        ]);
                    }

                    $config = $standard['config']; // single object

                    $configuration = PerformanceConfigurations::create([
                        'performance_standard_id' => $performanceStandard->id,
                        'target_output'           => $config['target_output'],
                        'quantity_indicator'      => $config['quantity_indicator'],
                        'timeliness_indicator'    => $config['timeliness_indicator'],
                        'timeliness_range'        => $config['timelinessType']['range'],
                        'timeliness_date'         => $config['timelinessType']['date'],
                        'timeliness_description'  => $config['timelinessType']['description'],
                    ]);
                }
            }

            DB::commit(); // Commit transaction

            return [
                'target_period'        => $targetPeriod,
                'performance_standard' => $performanceStandard,
                'standard_outcome'     => $standard_outcome,
                'configuration'        => $configuration,
            ];
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback if any error occurs

            return response()->json([
                'success' => false,
                'message' => 'Failed to create Unit Work Plan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // select organization on the office

    // fetch unit work plan of the division base on the division and other organization
    public function organization($request)
    {

        /**
         * =====================================
         * 0️⃣ VALIDATE ORGANIZATION BELONGS TO OFFICE
         * =====================================
         */
        $orgExistsInOffice = DB::table('employees')
            ->where('office', $request->office_name)
            ->where(function ($q) use ($request) {
                $q->where('office2', $request->organization)
                    ->orWhere('group', $request->organization)
                    ->orWhere('division', $request->organization)
                    ->orWhere('section', $request->organization)
                    ->orWhere('unit', $request->organization);
            })
            ->exists();

        // if (!$orgExistsInOffice) {
        //     return response()->json([
        //         // 'message' => 'Invalid organization. The organization does not belong to the selected office.'
        //         'message' => 'There are no employees assigned to the selected organization in this office.'

        //     ], 422);
        // }
        if (! $orgExistsInOffice) {
            throw new \Exception('There are no employees assigned to the selected organization in this office.', 422);
        }

        /**
         * ===============================
         * 1️⃣ OFFICE HEAD
         * ===============================
         */
        $officeEmployee = DB::table('employees')
            ->where('office', $request->office_name)
            ->whereNull('division')
            ->whereNull('section')
            ->whereNull('unit')
            ->select('ControlNo', 'name', 'rank', 'position', 'sg', 'level')
            ->first();

        if (! $officeEmployee) {
            return response()->json([
                'message' => 'Office head not found.',
            ], 404);
        }

        /**
         * ===============================
         * 2️⃣ ORGANIZATION EMPLOYEES
         * ===============================
         */
        $employees = DB::table('employees')
            ->where('office', $request->office_name)
            ->where(function ($q) use ($request) {
                $q->where('office2', $request->organization)
                    ->orWhere('group', $request->organization)
                    ->orWhere('division', $request->organization)
                    ->orWhere('section', $request->organization)
                    ->orWhere('unit', $request->organization);
            })
            ->select('ControlNo', 'name', 'rank', 'position', 'sg', 'level')
            ->get();

        $controlNos = $employees->pluck('ControlNo');

        $organizationTargetPeriods = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->with([
            'employee:ControlNo,name,rank,position,sg,level',
            'performanceStandards.standardOutcomes' => function ($query) {
                $query->select(
                    'id',
                    'performance_standard_id',
                    'rating',
                    'quantity_target',
                    'effectiveness_criteria',
                    'timeliness_range'
                );
            },
        ])
            ->whereIn('control_no', $controlNos)
            ->where('semester', $request->semester)
            ->where('year', $request->year)
            ->get();

        /**
         * ===============================
         * 3️⃣ GET ORGANIZATION MFOs
         * ===============================
         */
        // Extract unique MFOs from organization employees
        $organizationMFOs = $organizationTargetPeriods
            ->pluck('performanceStandards')
            ->flatten()
            ->pluck('mfo')
            ->unique()
            ->values()
            ->toArray();

        /**
         * ===============================
         * 4️⃣ FETCH OFFICE HEAD TARGET PERIOD WITH FILTERED MFOs
         * ===============================
         */
        $officeTargetPeriod = TargetPeriod::select('id', 'control_no', 'semester', 'year', 'status')->with([
            'employee:ControlNo,name,rank,position', // this maps via control_no
            'performanceStandards'                  => function ($query) use ($organizationMFOs) {
                $query->select('id', 'target_period_id', 'mfo', 'output', 'core as core_competencies', 'technical as technical_competencies', 'leadership as leadership_competencies', 'required_output', 'success_indicator')->whereIn('mfo', $organizationMFOs);
            },
            'performanceStandards.standardOutcomes' => function ($query) {
                $query->select(
                    'id',
                    'performance_standard_id',
                    'rating',
                    'quantity_target',
                    'effectiveness_criteria',
                    'timeliness_range'
                );
            },
        ])
            ->where('control_no', $officeEmployee->ControlNo)
            ->where('semester', $request->semester)
            ->where('year', $request->year)
            ->first();

        /**
         * ===============================
         * FINAL RESPONSE
         * ===============================
         */

        // checking  the status of the unit work plan on the office
        // Trackers

        $tracker = Tracker::where('office_name', $request->office_name) // ⚠️ fix typo (offiice_name)
            ->where('year', $request->year)
            ->where('semester', $request->semester)
            ->first();

        // If no record → Pending
        $unitWorkPlanStatus = $tracker ? $tracker->status : 'Draft';

        return (object) [
            'office_name'               => $request->office_name,
            'organization'              => $request->organization,
            'officeEmployee'            => $officeEmployee,
            'officeTargetPeriod'        => $officeTargetPeriod,
            'organizationTargetPeriods' => $organizationTargetPeriods,
            'unitworkplan_status'       => $unitWorkPlanStatus, // ✅ ADD THIS
        ];
    }

    // updating the unit work plan of employee
    public function update($validated, $controlNo, $semester, $year)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            // ✅ STEP 1: Employee + office restriction
            $employee = Employee::where('ControlNo', $controlNo)
                ->where('office_id', $user->office_id)
                ->first();

            if (! $employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found or access denied.',
                ], 404);
            }

            // ✅ STEP 2: Target Period
            $targetPeriod = $employee->targetPeriods()
                ->where('year', $year)
                ->where('semester', $semester)
                ->first();

            if (! $targetPeriod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit Work Plan not found.',
                ], 404);
            }

            // ✅ Reset status
            $targetPeriod->update([
                'status' => 'pending',
            ]);

            // ✅ DELETE CHILD RECORDS IN CORRECT ORDER (reverse dependency)
            // 1. Delete PerformanceConfigurations first
            PerformanceConfigurations::whereIn(
                'performance_standard_id',
                PerformanceStandard::where('target_period_id', $targetPeriod->id)->pluck('id')
            )->delete();

            // 2. Delete StandardOutcome
            StandardOutcome::whereIn(
                'performance_standard_id',
                PerformanceStandard::where('target_period_id', $targetPeriod->id)->pluck('id')
            )->delete();

            // 3. Delete PerformanceStandard last
            PerformanceStandard::where('target_period_id', $targetPeriod->id)->delete();

            // ✅ RE-CREATE PERFORMANCE STANDARDS
            foreach ($validated['performance_standards'] as $standard) {
                $performanceStandard = PerformanceStandard::create([
                    'target_period_id'      => $targetPeriod->id,
                    'category'              => $standard['category'],
                    'mfo'                   => $standard['mfo'],
                    'output'                => $standard['output'],
                    'output_name'           => $standard['output_name'], // ✅ ADDED - was missing
                    'core'                  => $standard['core_competency'] ?? null,
                    'technical'             => $standard['technical_competency'] ?? null,
                    'leadership'            => $standard['leadership_competency'] ?? null,
                    'performance_indicator' => $standard['performance_indicator'],
                    'success_indicator'     => $standard['success_indicator'],
                    'required_output'       => $standard['required_output'],
                ]);

                // ✅ Create ratings (StandardOutcome)
                foreach ($standard['ratings'] as $rating) {
                    $standard_outcome = StandardOutcome::create([
                        'performance_standard_id' => $performanceStandard->id,
                        'rating'                  => $rating['rating'],
                        'quantity_target'         => $rating['quantity'],
                        'effectiveness_criteria'  => $rating['effectiveness'],
                        'timeliness_range'        => $rating['timeliness'],
                    ]);
                }

                // ✅ Create config
                $config = $standard['config'];

                $configuration = PerformanceConfigurations::create([
                    'performance_standard_id' => $performanceStandard->id,
                    'target_output'           => $config['target_output'],
                    'quantity_indicator'      => $config['quantity_indicator'],
                    'timeliness_indicator'    => $config['timeliness_indicator'],
                    'timeliness_range'        => $config['timelinessType']['range'],
                    'timeliness_date'         => $config['timelinessType']['date'],
                    'timeliness_description'  => $config['timelinessType']['description'],
                ]);
            }

            DB::commit();

            return [
                'target_period'        => $targetPeriod,
                'performance_standard' => $performanceStandard,
                'standard_outcome'     => $standard_outcome,
                'configuration'        => $configuration,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update Unit Work Plan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
