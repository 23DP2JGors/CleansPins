<?php

namespace Database\Seeders;

use App\Models\Lietotajs;
use App\Models\Pasutijums;
use App\Models\Produkts;
use Illuminate\Database\Seeder;

class PasutijumsSeeder extends Seeder
{
    /**
     * Viens demonstrācijas pasūtījums testa lietotājam —
     * ļauj uzreiz pārbaudīt "pasūtījumu vēstures" ekrānu.
     */
    public function run(): void
    {
        $lietotajs = Lietotajs::where('e_pasts', 'janis@piemers.lv')->first();

        if (! $lietotajs) {
            return;
        }

        $pasutijums = Pasutijums::firstOrCreate(
            [
                'lietotaja_id' => $lietotajs->lietotaja_id,
                'piegades_adrese' => 'Brīvības iela 10, Rīga, LV-1010',
            ],
            [
                'datums' => now()->subDays(3),
                'statuss' => Pasutijums::STATUS_PIEGADATS,
                'kopsumma' => 0, // pārrēķinās zemāk
            ]
        );

        // ja pasūtījumam vēl nav rindu, izveidojam tās
        if ($pasutijums->rindas()->count() === 0) {
            $rindas = [
                'Disku tīrītājs' => 2,
                'Salona tīrītājs' => 1,
            ];

            foreach ($rindas as $nosaukums => $daudzums) {
                $produkts = Produkts::where('nosaukums', $nosaukums)->first();

                if (! $produkts) {
                    continue;
                }

                $pasutijums->rindas()->create([
                    'produkta_id' => $produkts->produkta_id,
                    'daudzums' => $daudzums,
                    'cena_bridi' => $produkts->cena, // fiksē cenu pasūtīšanas brīdī
                ]);
            }

            $pasutijums->parreikinatKopsummu();
            $pasutijums->save();
        }
    }
}
