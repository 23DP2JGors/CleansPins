<?php

namespace Database\Seeders;

use App\Models\Problema;
use Illuminate\Database\Seeder;

class ProblemaSeeder extends Seeder
{
    /**
     * Problēmas "gudrajam filtram" — lietotājs meklē pēc problēmas, nevis
     * pēc ķīmijas nosaukuma (sk. Projekta_apraksts: konkurences priekšrocība).
     */
    public function run(): void
    {
        $problemas = [
            'Traipi uz sēdekļa',
            'Netīrumi uz paklājiņiem',
            'Skrāpējumi uz virsbūves',
            'Blāvs lakojums',
            'Putekļi uz diskiem',
            'Nepatīkama smaka salonā',
            'Apledojums uz stikla',
            'Kukaiņu pēdas uz priekšējā stikla',
        ];

        foreach ($problemas as $nosaukums) {
            Problema::firstOrCreate(['nosaukums' => $nosaukums]);
        }
    }
}
