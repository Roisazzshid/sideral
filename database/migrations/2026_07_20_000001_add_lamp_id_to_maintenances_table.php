<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenances', 'lamp_id')) {
                $table->foreignId('lamp_id')->nullable()->after('room_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('maintenances', 'work_start_time')) {
                $table->string('work_start_time')->nullable()->after('resolution_notes');
            }
            if (!Schema::hasColumn('maintenances', 'work_end_time')) {
                $table->string('work_end_time')->nullable()->after('work_start_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            if (Schema::hasColumn('maintenances', 'lamp_id')) {
                $table->dropForeign(['lamp_id']);
                $table->dropColumn('lamp_id');
            }
            if (Schema::hasColumn('maintenances', 'work_start_time')) {
                $table->dropColumn('work_start_time');
            }
            if (Schema::hasColumn('maintenances', 'work_end_time')) {
                $table->dropColumn('work_end_time');
            }
        });
    }
};
