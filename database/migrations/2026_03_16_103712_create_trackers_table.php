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
        Schema::create('trackers', function (Blueprint $table) {
            $table->id();
            $table->string('office_name')->nullable(); // office of user
            $table->string('date')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->foreignId('unitworkplan_record_id')->nullable()->constrained('unitworkplan_records')->onDelete('cascade');
            $table->unsignedBigInteger('reviewed_by')->nullable(); // user_id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trackers');
    }
};
