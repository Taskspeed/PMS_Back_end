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
        Schema::create('performance_ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('target_period_id')
                ->nullable()
                ->constrained('target_periods')
                ->noActionOnDelete(); // ✅ SQL Server compatible

            $table->foreignId('performance_standard_id')
                ->nullable()
                ->constrained('performance_standards')
                ->noActionOnDelete(); // ✅ SQL Server compatible
            $table->string('control_no')->nullable();
            $table->string('date')->nullable();
            $table->string('quantity_target_rate')->nullable();
            $table->string('effectiveness_criteria_rate')->nullable();
            $table->string('timeliness_range_rate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_ratings');
    }
};
