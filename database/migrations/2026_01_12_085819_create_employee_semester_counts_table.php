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
        Schema::create('employee_semester_counts', function (Blueprint $table) {
            $table->id();
            $table->year('year')->nullable();
            $table->string('semester')->nullable();// "Jan-June" / "July-Dec"
            $table->integer('consultant')->nullable();  // "Regular", "Job-Order", "Others"
            $table->integer('regular')->nullable();  // "Regular", "Job-Order", "Others"
            $table->integer('casual')->nullable();  // "Regular", "Job-Order", "Others"
            $table->integer('contractual')->nullable();  // "Regular", "Job-Order", "Others"
            $table->integer('elective')->nullable();  // "Regular", "Job-Order", "Others"
            $table->integer('co-terminous')->nullable();  // "Regular", "Job-Order", "Others"
            $table->integer('temporary')->nullable();
            $table->integer('not-known')->nullable();
            $table->integer('lsb')->nullable();
            $table->integer('probationary')->nullable();
            $table->integer('substitute')->nullable();
            $table->integer('appointed')->nullable();
            $table->integer('job-order')->nullable();
            $table->integer('re-elect')->nullable();
            $table->integer('emergency')->nullable();
            $table->integer('honorarium')->nullable();
            $table->integer('permanent')->nullable();
            $table->integer('provisional')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_semester_counts');
    }
};
