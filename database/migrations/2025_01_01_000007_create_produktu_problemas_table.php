<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produktu_problemas', function (Blueprint $table) {
            $table->id('produktu_problemas_id');
            $table->foreignId('produkta_id')
                ->constrained('produkti', 'produkta_id')
                ->cascadeOnDelete();
            $table->foreignId('problemas_id')
                ->constrained('problemas', 'problemas_id')
                ->cascadeOnDelete();

            // viens produkts nedrīkst divreiz parādīties pie vienas problēmas
            $table->unique(['produkta_id', 'problemas_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produktu_problemas');
    }
};
