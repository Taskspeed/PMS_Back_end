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
            $table->string('name')->nullable();
            $table->string('rank')->nullable();
            $table->string('position')->nullable();
            $table->string('division')->nullable();
            $table->string('target_period')->nullable();
            $table->year('year')->nullable();
            $table->string('category')->nullable();
            $table->string('mfo')->nullable();
            $table->string('output')->nullable();
            $table->json('core')->nullable();
            $table->json('technical')->nullable();
            $table->json('leadership')->nullable();
            $table->text('success_indicator')->nullable();
            $table->text('required_output')->nullable();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
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
