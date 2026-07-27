<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to_id')->nullable()->after('assigned_to');
            $table->foreign('assigned_to_id')->references('id')->on('users')->nullOnDelete();
        });

        // Migrate existing string data to IDs
        try {
            $maintenances = \Illuminate\Support\Facades\DB::table('maintenances')->get();
            foreach ($maintenances as $mt) {
                if ($mt->assigned_to) {
                    $user = \Illuminate\Support\Facades\DB::table('users')
                        ->where('name', 'like', $mt->assigned_to)
                        ->first();
                    if ($user) {
                        \Illuminate\Support\Facades\DB::table('maintenances')
                            ->where('id', $mt->id)
                            ->update(['assigned_to_id' => $user->id]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence if database not ready
        }

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropColumn('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->string('assigned_to')->nullable()->after('assigned_to_id');
        });

        try {
            $maintenances = \Illuminate\Support\Facades\DB::table('maintenances')->get();
            foreach ($maintenances as $mt) {
                if ($mt->assigned_to_id) {
                    $user = \Illuminate\Support\Facades\DB::table('users')
                        ->where('id', $mt->assigned_to_id)
                        ->first();
                    if ($user) {
                        \Illuminate\Support\Facades\DB::table('maintenances')
                            ->where('id', $mt->id)
                            ->update(['assigned_to' => $user->name]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Silence
        }

        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_id']);
            $table->dropColumn('assigned_to_id');
        });
    }
};
