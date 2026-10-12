<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produktu_zonas', function (Blueprint $table) {
            $table->id('produktu_zonas_id');
            $table->foreignId('produkta_id')
                ->constrained('produkti', 'produkta_id')
                ->cascadeOnDelete();
            $table->foreignId('zonas_id')
                ->constrained('zonas', 'zonas_id')
                ->cascadeOnDelete();

            // viens produkts nedrīkst divreiz parādīties vienā zonā
            $table->unique(['produkta_id', 'zonas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produktu_zonas');
    }
};
