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
        Schema::table('target_periods', function (Blueprint $table) {
            $table->string('office')->nullable(); // draft/pending/approved/rejected
            $table->string('office2')->nullable(); // draft/pending/approved/rejected
            $table->string('group')->nullable(); // draft/pending/approved/rejected
            $table->string('division')->nullable(); // draft/pending/approved/rejected
            $table->string('section')->nullable(); // draft/pending/approved/rejected
            $table->string('unit')->nullable(); // draft/pending/approved/rejected
            $table->string('status')->default('pending'); // draft/pending/approved/rejected
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('target_periods', function (Blueprint $table) {
            //

            $table->dropColumn(['office', 'office2', 'group', 'division', 'section', 'unit', 'status']);
        });
    }
};
