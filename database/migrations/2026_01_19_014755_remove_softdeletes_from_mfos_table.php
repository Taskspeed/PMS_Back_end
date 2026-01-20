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
        Schema::table('mfos', function (Blueprint $table) {
            if (Schema::hasColumn('mfos', 'deleted_at')) {
                $table->dropSoftDeletes(); // removes deleted_at
            }
        });
    }

    public function down(): void
    {
        Schema::table('f_outmfospots', function (Blueprint $table) {
            $table->softDeletes(); // rollback support
        });
    }
};
