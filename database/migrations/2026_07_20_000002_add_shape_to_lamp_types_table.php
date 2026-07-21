<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lamp_types', function (Blueprint $table) {
            if (!Schema::hasColumn('lamp_types', 'shape')) {
                $table->string('shape')->default('bulat')->after('type'); // 'bulat' or 'panjang'
            }
        });
    }

    public function down(): void
    {
        Schema::table('lamp_types', function (Blueprint $table) {
            if (Schema::hasColumn('lamp_types', 'shape')) {
                $table->dropColumn('shape');
            }
        });
    }
};
