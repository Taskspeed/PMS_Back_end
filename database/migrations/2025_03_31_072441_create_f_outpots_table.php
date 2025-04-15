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
        Schema::create('f_outpots', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('mfo_id')->nullable()->constrained(); // no cascade
            $table->foreignId('f_category_id')->constrained('f_categories'); // no cascade
            $table->foreignId('office_id')->constrained()->onDelete('cascade'); // keep one cascade
            $table->softDeletes(); // Adds deleted_at column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('f_outpots');
    }
};
