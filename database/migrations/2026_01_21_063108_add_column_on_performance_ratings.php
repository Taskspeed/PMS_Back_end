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
        Schema::table('performance_ratings', function (Blueprint $table) {
            //
            $table->string('quantity_actual')->nullable()->after('timeliness_range_rate');
            $table->string('effectiveness_actual')->nullable()->after('quantity_actual');
            $table->string('timeliness_actual')->nullable()->after('effectiveness_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_ratings', function (Blueprint $table) {
            //
            $table->dropColumn(['quantity_actual', 'effectiveness_actual', 'timeliness_actual']);
        });
    }
};
