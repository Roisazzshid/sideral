<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lamp_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Philips LED Tube"
            $table->string('type'); // LED Tube, LED Bulb, Downlight, Panel, Spotlight
            $table->integer('watt');
            $table->decimal('price', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lamp_types');
    }
};
