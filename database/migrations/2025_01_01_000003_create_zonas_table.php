<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->id('zonas_id');
            $table->string('nosaukums', 100); // virsbūve, salons, diski
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
