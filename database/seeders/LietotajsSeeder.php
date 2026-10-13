<?php

namespace Database\Seeders;

use App\Models\Lietotajs;
use Illuminate\Database\Seeder;

class LietotajsSeeder extends Seeder
{
    /**
     * Testa konti izstrādei un demonstrācijai.
     * UZMANĪBU: paroles šeit ir vienkāršas tikai izstrādes vajadzībām —
     * produkcijas vidē tās jānomaina / jāģenerē nejauši.
     */
    public function run(): void
    {
        Lietotajs::firstOrCreate(
            ['e_pasts' => 'admin@autocare.lv'],
            [
                'vards' => 'Admin',
                'uzvards' => 'Lietotājs',
                'parole' => 'Admin123!', // automātiski sahashēts (sk. Lietotajs::casts -> 'hashed')
                'loma' => 1, // administrators
            ]
        );

        Lietotajs::firstOrCreate(
            ['e_pasts' => 'janis@piemers.lv'],
            [
                'vards' => 'Jānis',
                'uzvards' => 'Kalniņš',
                'parole' => 'Janis123!',
                'loma' => 0, // pircējs
            ]
        );
    }
}
