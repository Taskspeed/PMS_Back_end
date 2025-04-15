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
        Schema::create('technicals', function (Blueprint $table) {
            $table->id();
            $table->integer('Planning and Organizing')->nullable();
            $table->integer('Monitoring and Evaluation')->nullable();
            $table->integer('Records Management')->nullable();
            $table->integer('Partnering and Networking')->nullable();
            $table->integer('Process Management')->nullable();
            $table->integer('Attention to Detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technicals');
    }
};
