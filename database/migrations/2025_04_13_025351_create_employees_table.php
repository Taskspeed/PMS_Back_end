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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('rank')->nullable();
            $table->string('division')->nullable();
            $table->string('section')->nullable();
            $table->string('unit')->nullable();
            // $table->foreignId('position_id')->constrained('positions')->nullable(); // Add this line
            // $table->foreignId('office_id')->constrained()->onDelete('cascade')->nullable(); // Add this line
            $table->foreignId('position_id')->nullable()->constrained('positions');
            $table->foreignId('office_id')->nullable()->constrained()->onDelete('cascade');
            $table->softDeletes(); // Adds deleted_at column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
