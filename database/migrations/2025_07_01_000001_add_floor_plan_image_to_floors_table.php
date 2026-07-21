<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->string('floor_plan_image')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('floors', function (Blueprint $table) {
            $table->dropColumn('floor_plan_image');
        });
    }
};
