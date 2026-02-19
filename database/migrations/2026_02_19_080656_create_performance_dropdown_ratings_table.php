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
        Schema::create('performance_dropdown_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_rating_id')->nullable()->constrained('performance_ratings')->onDelete('cascade');
            // $table->integer('rate')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('effectiveness')->nullable();
            $table->integer('timeliness')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_dropdown_ratings');
    }
};
