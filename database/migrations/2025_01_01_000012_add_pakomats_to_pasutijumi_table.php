<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasutijumi', function (Blueprint $table) {
            // nullable, lai esošie pasūtījumi netiktu salauzti; obligātumu nodrošina validācija
            $table->string('vards', 30)->nullable()->after('lietotaja_id');
            $table->string('uzvards', 30)->nullable()->after('vards');
            $table->string('talrunis', 20)->nullable()->after('uzvards');

            $table->foreignId('pakomata_id')
                ->nullable()
                ->after('piegades_adrese')
                ->constrained('pakomati', 'pakomata_id')
                ->nullOnDelete();

            // kopijas pasūtīšanas brīdī — pasūtījums paliek pareizs, pat ja pakomātu saraksts mainās
            $table->string('pakomata_pakalpojums', 20)->nullable()->after('pakomata_id');
            $table->string('pakomata_nosaukums', 150)->nullable()->after('pakomata_pakalpojums');
            $table->string('pakomata_adrese', 255)->nullable()->after('pakomata_nosaukums');
        });
    }

    public function down(): void
    {
        Schema::table('pasutijumi', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pakomata_id');
            $table->dropColumn([
                'vards',
                'uzvards',
                'talrunis',
                'pakomata_pakalpojums',
                'pakomata_nosaukums',
                'pakomata_adrese',
            ]);
        });
    }
};
