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
        Schema::create('performance_standards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_period_id')->constrained('target_periods')->onDelete('cascade');
            $table->string('category')->nullable();
            $table->string('mfo')->nullable();
            $table->string('output')->nullable();
            $table->json('core')->nullable();
            $table->json('technical')->nullable();
            $table->json('leadership')->nullable();
            $table->string('output_name')->nullable();
            $table->string('performance_indicator')->nullable();
            $table->string('success_indicator')->nullable();
            $table->string('required_output')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_standard');
    }
};
