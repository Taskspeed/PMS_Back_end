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
        Schema::create('lates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('month_id')->nullable()->constrained('months')->onDelete('cascade');
            $table->integer('week1')->nullable();
            $table->integer('week2')->nullable();
            $table->integer('week3')->nullable();
            $table->integer('week4')->nullable();
            $table->integer('week5')->nullable();
            $table->integer('total_late')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lates');
    }
};
