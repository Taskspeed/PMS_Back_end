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
        Schema::create('targetperiod_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_period_id')->nullable()->constrained('target_periods')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // user_id
            $table->string('processed_by_name')->nullable(); // user_id name
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::table('targetperiod_records', function (Blueprint $table) {
            $table->dropForeign(['target_period_id']);
        });
        Schema::dropIfExists('targetperiod_records');
    }

};
