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
        Schema::table('f_outpots', function (Blueprint $table) {
            if (Schema::hasColumn('f_outpots', 'deleted_at')) {
                $table->dropSoftDeletes(); // removes deleted_at
            }
        });
    }

    public function down(): void
    {
        Schema::table('f_outpots', function (Blueprint $table) {
            $table->softDeletes(); // rollback support
        });
    }
};
