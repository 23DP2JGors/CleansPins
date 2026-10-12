<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasutijumi', function (Blueprint $table) {
            $table->id('pasutijuma_id');
            $table->foreignId('lietotaja_id')
                ->constrained('lietotaji', 'lietotaja_id')
                ->restrictOnDelete();
            $table->dateTime('datums')->useCurrent();
            $table->string('statuss', 30)->default('jauns'); // jauns, apstrādē, nosūtīts, piegādāts
            $table->string('piegades_adrese', 255);
            $table->decimal('kopsumma', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasutijumi');
    }
};
