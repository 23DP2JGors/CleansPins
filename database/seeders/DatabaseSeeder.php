<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Galvenais seeders — izsauc pārējos stingri pareizā secībā,
     * lai ievērotu ārējo atslēgu (FK) atkarības starp tabulām:
     *
     *   1) lietotaji, kategorijas, zonas, problemas — bez atkarībām
     *   2) produkti — atkarīgs no kategorijas_id (un many-to-many no zonas/problemas)
     *   3) groza_rindas, pasutijumi(+rindas) — atkarīgi no lietotaji + produkti
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
    }
}
