<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lamp_type_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique()->nullable(); // kode unik lampu, e.g. "L-001"
            $table->integer('position_x')->default(0); // posisi X pada denah
            $table->integer('position_y')->default(0); // posisi Y pada denah
            $table->enum('status', ['on', 'off', 'rusak', 'warning'])->default('on');
            $table->date('installed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lamps');
    }
};
