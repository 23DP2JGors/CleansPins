<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Dzēšot lietotāju, viņa (atceltie) pasūtījumi paliek kā vēsture ar lietotaja_id = null,
// nevis tiek dzēsti kaskādē. Aktīvu pasūtījumu gadījumā dzēšanu bloķē LietotajuController::destroy().
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasutijumi', function (Blueprint $table) {
            $table->dropForeign(['lietotaja_id']);
        });

        Schema::table('pasutijumi', function (Blueprint $table) {
            $table->foreignId('lietotaja_id')->nullable()->change();
            $table->foreign('lietotaja_id')
                ->references('lietotaja_id')
                ->on('lietotaji')
                ->nullOnDelete();
        });
    }

    // atgriešana iespējama tikai tad, ja nav pasūtījumu ar lietotaja_id = null
    public function down(): void
    {
        Schema::table('pasutijumi', function (Blueprint $table) {
            $table->dropForeign(['lietotaja_id']);
        });

        Schema::table('pasutijumi', function (Blueprint $table) {
            $table->foreignId('lietotaja_id')->nullable(false)->change();
            $table->foreign('lietotaja_id')
                ->references('lietotaja_id')
                ->on('lietotaji')
                ->cascadeOnDelete();
        });
    }
};
