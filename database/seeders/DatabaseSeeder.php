<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Galvenais seeders — izsauc pārējos stingri pareizā secībā,
     * lai ievērotu ārējo atslēgu (FK) atkarības starp tabulām:
     *
     *   1) lietotaji, kategorijas, zonas, problemas — bez atkarībām
     *   2) produkti — atkarīgs no kategorijas_id (un many-to-many no zonas/problemas)
     *   3) groza_rindas, pasutijumi(+rindas) — atkarīgi no lietotaji + produkti
     *   4) pakomati — komanda `pakomati:import` (Omniva no interneta + DPD/Latvijas Pasts parauga dati);
     *      ja nav interneta, Omniva tiek izlaists, bet pārējie tik un tā ielādējas
     */
    public function run(): void
    {
        $this->call([
            LietotajsSeeder::class,
            KategorijaSeeder::class,
            ZonaSeeder::class,
            ProblemaSeeder::class,
            ProduktsSeeder::class,
            GrozaRindaSeeder::class,
            PasutijumsSeeder::class,
        ]);

        Artisan::call('pakomati:import', [], $this->command->getOutput());
    }
}
