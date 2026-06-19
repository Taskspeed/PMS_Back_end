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
        Schema::table('target_period_lib', function (Blueprint $table) {
            //
            $table->boolean('target_period_status')->default(false)->after('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_period_lib', function (Blueprint $table) {
            //
            $table->dropColumn('target_period_status');
        });
    }
};
