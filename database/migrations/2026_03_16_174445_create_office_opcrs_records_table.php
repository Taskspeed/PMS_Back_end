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
        Schema::create('office_opcrs_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_opcr_id')->nullable()->constrained('office_opcrs')->onDelete('cascade');
            $table->string('date')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {


        Schema::table('office_opcrs_records', function (Blueprint $table) {
            $table->dropForeign(['office_opcr_id']);
        });
        Schema::dropIfExists('office_opcrs_records');
    }
};
