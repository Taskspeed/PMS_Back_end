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
        Schema::create('performance_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_standard_id')->nullable()->constrained('performance_standards')->onDelete('cascade');
            // Core config values
            $table->string('target_output')->nullable(); // 100
            $table->string('quantity_indicator')->nullable(); // C
            $table->string('timeliness_indicator')->nullable(); // beforeDeadline

            // Timeliness type flags
            $table->boolean('timeliness_range')->default(false);
            $table->boolean('timeliness_date')->default(false);
            $table->boolean('timeliness_description')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_configurations');
    }
};
