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
       Schema::create('performance_rating_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_standard_id')->constrained('performance_standards')->onDelete('cascade');
            $table->integer('week_number');          // 1,2,3,4,5
            $table->string('month');                     // e.g. "June"
            $table->integer('year');                     // e.g. 2026
            $table->string('file_path');                 // storage path
            $table->string('original_name')->nullable(); // original filename
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_rating_attachments');
    }
};
