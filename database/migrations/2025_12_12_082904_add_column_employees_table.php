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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('tblStructureID')->after('unit')->nullable();
            $table->string('sg')->after('tblStructureID')->nullable();
            $table->string('level')->after('sg')->nullable();
            $table->string('positionID')->after('level')->nullable();
            $table->string('itemNo')->after('positionID')->nullable();
            $table->string('pageNo')->after('itemNo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropColumn(['tblStructureID', 'positionID', 'itemNo', 'pageNo', 'sg', 'level']);
        });
    }
};
