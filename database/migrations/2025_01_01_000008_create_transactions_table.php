<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lamp_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lamp_type_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['penggantian', 'pemasangan'])->default('penggantian');
            $table->integer('quantity')->default(1);
            $table->date('transaction_date');
            $table->string('technician')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
