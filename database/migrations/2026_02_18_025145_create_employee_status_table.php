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
        Schema::create('employee_status', function (Blueprint $table) {
            $table->id();
            $table->year('year')->nullable();
            $table->string('semester')->nullable();
            $table->integer('elective')->default(0)->nullable(false);
            $table->integer('appointed')->default(0)->nullable(false);
            $table->integer('co_terminous')->default(0)->nullable(false);
            $table->integer('temporary')->default(0)->nullable(false);
            $table->integer('regular')->default(0)->nullable(false);
            $table->integer('casual')->default(0)->nullable(false);
            $table->integer('contractual')->default(0)->nullable(false);
            $table->integer('honorarium')->default(0)->nullable(false);
            $table->integer('lsb')->default(0)->nullable(false);
            $table->integer('probationary')->default(0)->nullable(false);
            $table->integer('substitute')->default(0)->nullable(false);
            $table->integer('job_order')->default(0)->nullable(false);
            $table->integer('re_elect')->default(0)->nullable(false);
            $table->integer('emergency')->default(0)->nullable(false);
            $table->integer('permanent')->default(0)->nullable(false);
            $table->integer('provisional')->default(0)->nullable(false);
            $table->integer('not_known')->default(0)->nullable(false);
            $table->integer('consultant')->default(0)->nullable(false);
            $table->integer('total_employee')->default(0)->nullable(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_status');
    }
};
