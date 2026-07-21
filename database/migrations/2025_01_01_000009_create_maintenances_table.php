<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // Lampu Mati, Lampu Flicker, Pembersihan, dll
            $table->text('description')->nullable();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('lamp_id')->nullable()->constrained()->nullOnDelete();
            $table->string('work_start_time')->nullable();
            $table->string('work_end_time')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
