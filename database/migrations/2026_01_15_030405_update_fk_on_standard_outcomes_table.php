<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standard_outcomes', function (Blueprint $table) {

            // 1️⃣ Drop old FK
            $table->dropForeign(['target_period_id']);

            // 2️⃣ Drop old column
            $table->dropColumn('target_period_id');

            // 3️⃣ Add new FK
            $table->foreignId('performance_standard_id')
                ->nullable()
                ->constrained('performance_standards')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('standard_outcomes', function (Blueprint $table) {

            // 1️⃣ Drop new FK
            $table->dropForeign(['performance_standard_id']);
            $table->dropColumn('performance_standard_id');

            // 2️⃣ Restore old FK
            $table->foreignId('target_period_id')
                ->constrained('target_periods')
                ->cascadeOnDelete();
        });
    }
};
