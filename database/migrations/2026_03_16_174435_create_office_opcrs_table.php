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
        Schema::create('office_opcrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->nullable()->constrained('offices')->onDelete('cascade');
            $table->string('office_name')->nullable();
            $table->string('semester')->nullable();
            $table->integer('year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_opcrs');
    }
};
