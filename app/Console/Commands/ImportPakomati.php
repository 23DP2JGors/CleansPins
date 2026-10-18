<?php

namespace App\Console\Commands;

use App\Models\Pakomats;
use Database\Seeders\PakomatiSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Ielādē pakomātu sarakstus mūsu DB.
 * Tikai LASA publiskos sarakstus — pārvadātājiem neko nesūta.
 *
 *   php artisan pakomati:import
 *   php artisan pakomati:import --omniva-file=storage/app/omniva_locations.json   (bez interneta)
 */
class ImportPakomati extends Command
{
    protected $signature = 'pakomati:import
                            {--omniva-file= : Lokāls Omniva locations.json fails URL vietā}';

    protected $description = 'Importē Omniva, DPD un Latvijas Pasta pakomātus tabulā "pakomati"';

    // Omniva publiskais visu Baltijas punktu saraksts (pārbaudīts 2026-10-18)
    private const OMNIVA_URL = 'https://www.omniva.ee/locations.json';

    public function handle(): int
    {
        $avoti = [
            Pakomats::OMNIVA => fn () => $this->importetOmniva(),
            // DPD un Latvijas Pasts — nav publiska saraksta, tāpēc parauga dati no seedera
            Pakomats::DPD => fn () => (new PakomatiSeeder)->importet(Pakomats::DPD),
            Pakomats::LATVIJAS_PASTS => fn () => (new PakomatiSeeder)->importet(Pakomats::LATVIJAS_PASTS),
        ];

        $kludas = 0;

        // katrs avots atsevišķā try/catch — viena kļūda neaptur pārējos
        foreach ($avoti as $pakalpojums => $imports) {
            $nosaukums = Pakomats::PAKALPOJUMI[$pakalpojums];

            try {
                $skaits = $imports();
                $this->info("{$nosaukums}: importēti {$skaits} pakomāti");
            } catch (\Throwable $e) {
                $kludas++;
                $this->error("{$nosaukums}: imports neizdevās — {$e->getMessage()}");
            }
        }

        return $kludas === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function importetOmniva(): int
    {
        $fails = $this->option('omniva-file');

        if ($fails) {
            if (! is_file($fails)) {
                throw new \RuntimeException("Fails nav atrasts: {$fails}");
            }
            $dati = json_decode(file_get_contents($fails), true);
        } else {
            $dati = Http::timeout(60)->retry(2, 1000)->get(self::OMNIVA_URL)->throw()->json();
        }

        if (! is_array($dati)) {
            throw new \RuntimeException('Omniva atbilde nav derīgs JSON saraksts');
        }

        $redzetie = [];

        foreach ($dati as $punkts) {
            // tikai Latvija (A0_NAME = LV) un tikai pakomāti (TYPE 0; TYPE 1 = pasta nodaļa)
            if (($punkts['A0_NAME'] ?? '') !== 'LV' || (string) ($punkts['TYPE'] ?? '') !== '0') {
                continue;
            }

            $lat = (float) ($punkts['Y_COORDINATE'] ?? 0);
            $lng = (float) ($punkts['X_COORDINATE'] ?? 0);

            if (empty($punkts['ZIP']) || ! $lat || ! $lng) {
                continue;
            }

            Pakomats::updateOrCreate(
                ['pakalpojums' => Pakomats::OMNIVA, 'ext_id' => (string) $punkts['ZIP']],
                [
                    'nosaukums' => trim($punkts['NAME']),
                    'adrese' => $this->omnivaAdrese($punkts),
                    'pilseta' => $this->omnivaPilseta($punkts),
                    // Omniva "ZIP" ir pakomāta kods, nevis pasta indekss
                    'pasta_indekss' => null,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'darba_laiks' => trim($punkts['SERVICE_HOURS'] ?? '') ?: null,
                    'aktivs' => true,
                ]
            );

            $redzetie[] = (string) $punkts['ZIP'];
        }

        // pakomāti, kas pazuduši no Omniva saraksta, tiek deaktivēti (nevis dzēsti — uz tiem var atsaukties pasūtījumi)
        if ($redzetie) {
            Pakomats::where('pakalpojums', Pakomats::OMNIVA)
                ->whereNotIn('ext_id', $redzetie)
                ->update(['aktivs' => false]);
        }

        return count($redzetie);
    }

    // "Brīvības iela 35" vai, ja ielas nav, objekta nosaukums ("Līdumnieki")
    private function omnivaAdrese(array $p): string
    {
        $iela = trim(trim($p['A5_NAME'] ?? '') . ' ' . trim($p['A7_NAME'] ?? ''));

        return $iela !== '' ? $iela : (trim($p['A6_NAME'] ?? '') ?: trim($p['NAME']));
    }

    /**
     * A1 = novads vai republikas pilsēta ("Rīga"), A2 = pilsēta vai pagasts, A3 = ciems.
     */
    private function omnivaPilseta(array $p): string
    {
        $a1 = trim($p['A1_NAME'] ?? '');
        $a2 = trim($p['A2_NAME'] ?? '');
        $a3 = trim($p['A3_NAME'] ?? '');

        $irNovads = str_ends_with($a1, 'novads') || str_ends_with($a1, 'nov.');

        if (! $irNovads && $a1 !== '') {
            return $a1;
        }

        return $a3 ?: ($a2 ?: $a1);
    }
}
