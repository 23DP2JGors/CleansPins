<?php

namespace Database\Seeders;

use App\Models\GrozaRinda;
use App\Models\Lietotajs;
use App\Models\Produkts;
use Illuminate\Database\Seeder;

class GrozaRindaSeeder extends Seeder
{
    /**
     * Piepilda testa lietotāja "janis@piemers.lv" grozu ar dažiem produktiem,
     * lai būtu ko parādīt groza/pasūtīšanas ekrānos.
     */
    public function run(): void
    {
        $lietotajs = Lietotajs::where('e_pasts', 'janis@piemers.lv')->first();

        if (! $lietotajs) {
            return; // LietotajsSeeder vēl nav palaists
        }

        $groza_saturs = [
            'Auto šampūns' => 1,
            'Mikrošķiedras drāna (komplekts 3 gab.)' => 2,
        ];

        foreach ($groza_saturs as $nosaukums => $daudzums) {
            $produkts = Produkts::where('nosaukums', $nosaukums)->first();

            if (! $produkts) {
                continue;
            }

            GrozaRinda::firstOrCreate(
                [
                    'lietotaja_id' => $lietotajs->lietotaja_id,
                    'produkta_id' => $produkts->produkta_id,
                ],
                ['daudzums' => $daudzums]
            );
        }
    }
}
