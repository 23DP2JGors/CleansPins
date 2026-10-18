<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pakomati', function (Blueprint $table) {
            $table->id('pakomata_id');
            $table->string('pakalpojums', 20); // omniva, dpd, latvijas_pasts
            $table->string('ext_id', 50);      // pārvadātāja paša ID
            $table->string('nosaukums', 150);
            $table->string('adrese', 255);
            $table->string('pilseta', 100);
            $table->string('pasta_indekss', 20)->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('darba_laiks', 255)->nullable();
            // nosaukums + adrese + pilsēta mazajiem burtiem bez garumzīmēm — meklēšanai,
            // lai "rigas" atrastu "Rīgas" gan MySQL, gan SQLite
            $table->string('meklesanai', 500)->default('');
            $table->boolean('aktivs')->default(true);
            $table->timestamps();

            $table->unique(['pakalpojums', 'ext_id']);
            $table->index('pilseta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pakomati');
    }
};
