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
            $table->string('position')->nullable();
            $table->string('rank')->nullable();
            $table->string('office')->nullable();
            $table->string('division')->nullable();
            $table->string('section')->nullable();
            $table->string('unit')->nullable();
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
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
