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
        Schema::table('lamps', function (Blueprint $table) {
            $table->integer('rotation')->default(0)->after('position_y')->comment('Rotasi lampu dalam derajat, terutama untuk lampu TL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lamps', function (Blueprint $table) {
            $table->dropColumn('rotation');
        });
    }
};
