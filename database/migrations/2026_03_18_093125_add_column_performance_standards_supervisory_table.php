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
            //
            $table->string('supervisory_control_no')->nullable()->after('target_period_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_standards', function (Blueprint $table) {
            //
            $table->dropColumn('supervisory_control_no');

        });
    }
};
