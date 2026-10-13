<?php

namespace Database\Seeders;

use App\Models\Zona;
use Illuminate\Database\Seeder;

class ZonaSeeder extends Seeder
{
    /**
     * Automašīnas zonas interaktīvajai shēmai (sk. 04 ekrāna skici).
     */
    public function run(): void
    {
        $zonas = [
            'Virsbūve',
            'Salons',
            'Diski',
        ];

        foreach ($zonas as $nosaukums) {
            Zona::firstOrCreate(['nosaukums' => $nosaukums]);
        }
    }
}
