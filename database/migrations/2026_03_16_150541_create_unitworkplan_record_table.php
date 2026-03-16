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
        Schema::create('unitworkplan_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unitworkplan_id')->nullable()->constrained('unitworkplans')->onDelete('cascade');
            $table->string('date')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // user_id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unitworkplan_records', function (Blueprint $table) {
            $table->dropForeign(['unitworkplan_id']);
        });

        Schema::dropIfExists('unitworkplan_records');
    }
};
