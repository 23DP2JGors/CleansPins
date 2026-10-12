<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Grozi" tabula ir apvienota ar "Groza_rindas": lietotāja grozs ir implicīts —
     * tas vienkārši ir visas "groza_rindas" rindas ar attiecīgo lietotaja_id,
     * bez atsevišķas starptabulas un bez papildu JOIN, iegūstot grozu.
     */
    public function up(): void
    {
        Schema::create('groza_rindas', function (Blueprint $table) {
            $table->id('rindas_id');
            $table->foreignId('lietotaja_id')
                ->constrained('lietotaji', 'lietotaja_id')
                ->cascadeOnDelete();
            $table->foreignId('produkta_id')
                ->constrained('produkti', 'produkta_id')
                ->cascadeOnDelete();
            $table->integer('daudzums')->default(1);
            $table->timestamps();

            // viens produkts viena lietotāja grozā parādās tikai vienā rindā
            $table->unique(['lietotaja_id', 'produkta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groza_rindas');
    }
};
