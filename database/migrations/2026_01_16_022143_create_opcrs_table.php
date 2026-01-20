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
        Schema::create('opcrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->foreignId('performance_standard_id')->nullable()->constrained('performance_standards')->onDelete('cascade');
            $table->json('competency')->nullable();
            $table->text('budget')->nullable();
            $table->text('accountable')->nullable();
            $table->text('accomplishment')->nullable();
            $table->decimal('rating_q')->nullable();
            $table->decimal('rating_e')->nullable();
            $table->decimal('rating_t')->nullable();
            $table->decimal('rating_a')->nullable();
            $table->json('profiency')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcrs');
    }
};
