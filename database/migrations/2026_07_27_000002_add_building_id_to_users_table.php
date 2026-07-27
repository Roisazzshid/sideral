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
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'building_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('building_id')->nullable()->after('role');
                $table->foreign('building_id')->references('id')->on('buildings')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'building_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['building_id']);
                $table->dropColumn('building_id');
            });
        }
    }
};
