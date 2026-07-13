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
        Schema::create('document_signatories', function (Blueprint $table) {
            $table->id();

            $table->string('control_no')->nullable();
            //performance standard
            $table->string('performance_standard_discussed_by_control_no')->nullable();
            $table->string('performance_standard_approved_by_control_no')->nullable();
            //ipcr
            $table->string('ipcr_reviewed_by_control_no')->nullable();
            $table->string('ipcr_approved_by_control_no')->nullable();
            $table->string('ipcr_assessed_by_control_no')->nullable();
            $table->string('ipcr_final_rating_by_control_no')->nullable();
            //por
            $table->string('por_confirmed_control_no')->nullable();
            $table->string('por_approved_final_rating_control_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_signatories');
    }
};
