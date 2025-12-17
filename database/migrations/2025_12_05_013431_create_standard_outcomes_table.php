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
        Schema::create('standard_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_period_id')->constrained('target_periods')->onDelete('cascade');

            $table->integer('rating'); // 5 to 1
            $table->string('quantity_target')->nullable();
            $table->string('effectiveness_criteria')->nullable();
            $table->string('timeliness_range')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_outcome');
    }
};
