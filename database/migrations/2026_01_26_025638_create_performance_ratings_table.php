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

            $table->foreignId('performance_standard_id')->nullable()->constrained('performance_standards')->cascadeOnDelete();
            $table->string('control_no')->nullable();
            $table->string('date')->nullable();
            $table->string('quantity_target_rate')->nullable();
            $table->string('effectiveness_criteria_rate')->nullable();
            $table->string('timeliness_range_rate')->nullable();

            $table->string('quantity_actual')->nullable()->after('timeliness_range_rate');
            $table->string('effectiveness_actual')->nullable()->after('quantity_actual');
            $table->string('timeliness_actual')->nullable()->after('effectiveness_actual');
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
