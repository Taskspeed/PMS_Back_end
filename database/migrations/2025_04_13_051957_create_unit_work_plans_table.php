<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_work_plans', function (Blueprint $table) {

            $table->id();
            $table->string('division');
            $table->string('target_period');
            $table->year('year');
            $table->string('rank');
            $table->string('position');
            $table->string('category')->nullable();
            $table->string('mfo')->nullable();
            $table->string('output')->nullable();
            $table->json('core')->nullable();
            $table->json('technical')->nullable();
            $table->json('leadership')->nullable();
            $table->text('success_indicator');
            $table->text('required_output');
            $table->text('mode');
            $table->json('standard_outcomes');
            $table->foreignId('office_id')->constrained()->nullable();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending')->after('employee_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_work_plans');
    }
};
