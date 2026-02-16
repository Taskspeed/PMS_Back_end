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
        Schema::create('physical_mentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qpef_id')->nullable()->constrained('qpefs')->onDelete('cascade');
            $table->string('indicators')->nullable();
            $table->integer('rating')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_mentals');
    }
};
