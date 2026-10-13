<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasutijuma_rindas', function (Blueprint $table) {
            $table->id('rindas_id');
            $table->foreignId('pasutijuma_id')
                ->constrained('pasutijumi', 'pasutijuma_id')
                ->cascadeOnDelete();
            $table->foreignId('produkta_id')
                ->constrained('produkti', 'produkta_id')
                ->restrictOnDelete(); // nedzēst produktu, ja tas ir jau noformētā pasūtījumā
            $table->integer('daudzums');
            $table->decimal('cena_bridi', 8, 2); // produkta cena pasūtīšanas brīdī

            $table->unique(['pasutijuma_id', 'produkta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasutijuma_rindas');
    }
};
