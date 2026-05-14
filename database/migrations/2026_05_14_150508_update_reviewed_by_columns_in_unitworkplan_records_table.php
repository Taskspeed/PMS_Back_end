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
        Schema::table('unitworkplan_records', function (Blueprint $table) {
            $table->renameColumn('reviewed_by', 'processed_by');

            $table->string('processed_by_name')->nullable()->after('processed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unitworkplan_records', function (Blueprint $table) {
            $table->renameColumn('processed_by', 'reviewed_by');

            $table->dropColumn('processed_by_name');
        });
    }
};
