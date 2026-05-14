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
        Schema::table('office_opcrs_records', function (Blueprint $table) {
            // ✅ rename reviewed_by to processed_by
            $table->renameColumn('reviewed_by', 'processed_by');

            // ✅ add new columns — adjust types as needed
            $table->string('processed_by_name')->nullable()->after('processed_by');
        });
    }

    public function down(): void
    {
        Schema::table('office_opcrs_records', function (Blueprint $table) {
            // rollback
            $table->renameColumn('processed_by', 'reviewed_by');
            $table->dropColumn('processed_by_name');
        });
    }
};
