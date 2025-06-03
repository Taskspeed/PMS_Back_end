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
            $table->foreignId('employee_id')->constrained()->nullable();
            $table->string('target_period');
            $table->year('year');
            $table->json('strategic function')->nullable();
            $table->json('core function')->nullable();
            $table->json('support function')->nullable();
            $table->json('core')->nullable();
            $table->json('technical')->nullable();
            $table->json('leadership')->nullable();
            $table->string('alloted budget')->nullable();
            $table->string('accountable')->nullable();
            $table->text('actual accomplishment')->nullable();
            $table->float('rating_q')->nullable();
            $table->float('rating_e')->nullable();
            $table->float('rating_t')->nullable();
            $table->float('rating_a')->nullable();
            $table->json('profiency result')->nullable();
            $table->string('remarks')->nullable();
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
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
