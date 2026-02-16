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
        Schema::create('recommendation_developments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qpef_id')->nullable()->constrained('qpefs')->onDelete('cascade');
            $table->boolean('for_retention')->default(false);
            $table->boolean('for_commendation')->default(false);
            $table->boolean('for_improvement')->default(false);
            $table->boolean('for_non_renewal')->default(false);
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_developments');
    }
};
