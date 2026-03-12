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
        Schema::table('performance_standards', function (Blueprint $table) {
            $table->json('performance_indicator')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('performance_standards', function (Blueprint $table) {
            $table->string('performance_indicator')->nullable()->change();
        });
    }
};
