<?php

namespace Database\Seeders;

use App\Models\Kategorija;
use Illuminate\Database\Seeder;

class KategorijaSeeder extends Seeder
{
    public function run(): void
    {
        $kategorijas = [
            'Virsbūves kopšana' => 'Šampūni, vaski un pulēšanas līdzekļi automašīnas virsbūvei.',
            'Salona kopšana' => 'Līdzekļi audumam, ādai un plastmasas detaļām salonā.',
            'Disku un riepu kopšana' => 'Disku tīrītāji un riepu spīduma līdzekļi.',
            'Stikla kopšana' => 'Stikla tīrīšanas un pretapledošanas līdzekļi.',
            'Inventārs un piederumi' => 'Sūkļi, drānas, otas un citi mazgāšanas piederumi.',
        ];

        foreach ($kategorijas as $nosaukums => $apraksts) {
            Kategorija::firstOrCreate(
                ['nosaukums' => $nosaukums],
                ['apraksts' => $apraksts]
            );
        }
    }
}
