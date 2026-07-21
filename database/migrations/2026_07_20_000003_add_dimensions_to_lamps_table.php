<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lamps', function (Blueprint $table) {
            if (!Schema::hasColumn('lamps', 'width')) {
                $table->integer('width')->nullable()->default(32)->after('rotation')->comment('Panjang visual lampu dalam piksel');
            }
            if (!Schema::hasColumn('lamps', 'height')) {
                $table->integer('height')->nullable()->default(14)->after('width')->comment('Lebar visual lampu dalam piksel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lamps', function (Blueprint $table) {
            if (Schema::hasColumn('lamps', 'width')) {
                $table->dropColumn(['width', 'height']);
            }
        });
    }
};
