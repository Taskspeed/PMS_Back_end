<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop dependent foreign keys first
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('standard_outcomes');
        Schema::dropIfExists('performance_standards');
        Schema::dropIfExists('target_periods');

        Schema::enableForeignKeyConstraints();


        // Now drop target_periods
        Schema::dropIfExists('target_periods');

        // Recreate the table
        Schema::create('target_periods', function (Blueprint $table) {
            $table->id();
            $table->string('control_no')->nullable();
            $table->string('semester')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();


        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_period');
    }
};
