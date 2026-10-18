<?php

namespace Database\Seeders;

use App\Models\Pakomats;
use Illuminate\Database\Seeder;

/**
 * PARAUGA DATI (sample data) — DPD un Latvijas Pasts.
 *
 * Ne DPD, ne Latvijas Pasts nepublicē publisku pakomātu sarakstu bez līguma/API konta
 * (DPD "Interconnector" API prasa lietotājvārdu un paroli no DPD pārdošanas nodaļas).
 * Tāpēc šeit ir manuāli sastādīts saraksts:
 *  - nosaukumi un adreses ir reāli tirdzniecības centri / veikali Latvijā;
 *  - koordinātas ir šo ēku koordinātas (ņemtas no Omniva publiskā saraksta tajā pašā vietā);
 *  - ext_id ir mūsu izdomāti kodi, NAV pārvadātāja īstie ID;
 *  - nav pārbaudīts, vai konkrētā pārvadātāja pakomāts tiešām stāv tieši tur.
 * Produkcijā tos jāaizstāj ar pārvadātāja oficiālo sarakstu.
 *
 * Pasūtījumi pārvadātājiem netiek sūtīti — sarakstu tikai lasām un glabājam savā DB.
 */
class PakomatiSeeder extends Seeder
{
    // [ext_id, nosaukums, adrese, pilseta, pasta_indekss, lat, lng]
    private const DPD = [
        ['DPD-LV-001', 'DPD Pickup pakomāts T/C Alfa', 'Brīvības gatve 372', 'Rīga', 'LV-1006', 56.9817930, 24.2052090],
        ['DPD-LV-002', 'DPD Pickup pakomāts T/C Spice', 'Lielirbes iela 29', 'Rīga', 'LV-1046', 56.9296330, 24.0376990],
        ['DPD-LV-003', 'DPD Pickup pakomāts T/C Domina Shopping', 'Ieriķu iela 3', 'Rīga', 'LV-1084', 56.9656830, 24.1626860],
        ['DPD-LV-004', 'DPD Pickup pakomāts T/C Olimpia', 'Āzenes iela 5', 'Rīga', 'LV-1048', 56.9499240, 24.0812800],
        ['DPD-LV-005', 'DPD Pickup pakomāts T/C Akropole Rīga', 'Latgales iela 257', 'Rīga', 'LV-1019', 56.9248610, 24.1752830],
        ['DPD-LV-006', 'DPD Pickup pakomāts T/C Teika Plaza', 'Brīvības gatve 201', 'Rīga', 'LV-1039', 56.9746450, 24.1629460],
        ['DPD-LV-007', 'DPD Pickup pakomāts T/C Riga Plaza', 'Mūkusalas iela 71', 'Rīga', 'LV-1004', 56.9266080, 24.1031520],
        ['DPD-LV-008', 'DPD Pickup pakomāts T/C Pilsētas pasāža', 'Driksas iela 4', 'Jelgava', 'LV-3001', 56.6508690, 23.7231780],
        ['DPD-LV-009', 'DPD Pickup pakomāts T/C RAF', 'Rīgas iela 48', 'Jelgava', 'LV-3004', 56.6684520, 23.7612560],
        ['DPD-LV-010', 'DPD Pickup pakomāts T/C Rietumu centrs', 'Jaunā ostmala 3/5', 'Liepāja', 'LV-3401', 56.5150100, 21.0182190],
        ['DPD-LV-011', 'DPD Pickup pakomāts T/C Baata', 'Klaipēdas iela 104C', 'Liepāja', 'LV-3416', 56.4867860, 21.0049570],
        ['DPD-LV-012', 'DPD Pickup pakomāts T/C Ditton', 'Cietokšņa iela 60', 'Daugavpils', 'LV-5401', 55.8744170, 26.5172680],
        ['DPD-LV-013', 'DPD Pickup pakomāts T/C Solo', 'Rīgas iela 9', 'Daugavpils', 'LV-5401', 55.8718670, 26.5178900],
        ['DPD-LV-014', 'DPD Pickup pakomāts T/C Liedags', 'Ventspils šoseja 32', 'Jūrmala', 'LV-2011', 56.9466100, 23.6178190],
        ['DPD-LV-015', 'DPD Pickup pakomāts T/C Tobago', 'Lielais prospekts 3/5', 'Ventspils', 'LV-3601', 57.3877180, 21.5802090],
        ['DPD-LV-016', 'DPD Pickup pakomāts T/C Valleta', 'Rīgas iela 4', 'Valmiera', 'LV-4201', 57.5387940, 25.4242740],
        ['DPD-LV-017', 'DPD Pickup pakomāts Maxima XX', 'Atbrīvošanas aleja 141', 'Rēzekne', 'LV-4604', 56.5241470, 27.3394780],
        ['DPD-LV-018', 'DPD Pickup pakomāts T/C Dauga', 'Rīgas iela 23', 'Ogre', 'LV-5001', 56.8165080, 24.5853550],
    ];

    private const LATVIJAS_PASTS = [
        ['LP-LV-001', 'Latvijas Pasta pakomāts T/C Galleria Riga', 'Dzirnavu iela 67', 'Rīga', 'LV-1011', 56.9545010, 24.1205720],
        ['LP-LV-002', 'Latvijas Pasta pakomāts T/C Gaiļezers', 'Gaiļezera iela 8', 'Rīga', 'LV-1079', 56.9680750, 24.2367480],
        ['LP-LV-003', 'Latvijas Pasta pakomāts T/C IXO', 'Anniņmuižas iela 17', 'Rīga', 'LV-1029', 56.9433680, 24.0179360],
        ['LP-LV-004', 'Latvijas Pasta pakomāts T/C Dole', 'Latgales iela 357', 'Rīga', 'LV-1063', 56.9056450, 24.1912750],
        ['LP-LV-005', 'Latvijas Pasta pakomāts T/C Juglas centrs', 'Juglas krastmala 2', 'Rīga', 'LV-1024', 56.9895740, 24.2450970],
        ['LP-LV-006', 'Latvijas Pasta pakomāts T/C Venta', 'Zolitūdes iela 34', 'Rīga', 'LV-1029', 56.9493670, 23.9995830],
        ['LP-LV-007', 'Latvijas Pasta pakomāts Rīgas Autoosta', 'Prāgas iela 1', 'Rīga', 'LV-1050', 56.9450030, 24.1152120],
        ['LP-LV-008', 'Latvijas Pasta pakomāts T/C Valdeka', 'Rīgas iela 11A', 'Jelgava', 'LV-3002', 56.6617590, 23.7470570],
        ['LP-LV-009', 'Latvijas Pasta pakomāts T/C Vivo centrs', 'Katoļu iela 18', 'Jelgava', 'LV-3001', 56.6478620, 23.7264620],
        ['LP-LV-010', 'Latvijas Pasta pakomāts Rožu laukums', 'Lielā iela 13', 'Liepāja', 'LV-3401', 56.5074890, 21.0106970],
        ['LP-LV-011', 'Latvijas Pasta pakomāts T/C Aveņu centrs', 'Raipoles iela 11A', 'Daugavpils', 'LV-5417', 55.9003410, 26.5260130],
        ['LP-LV-012', 'Latvijas Pasta pakomāts Dubulti', 'Strēlnieku prospekts 1', 'Jūrmala', 'LV-2015', 56.9674440, 23.7698770],
        ['LP-LV-013', 'Latvijas Pasta pakomāts Ganību iela', 'Ganību iela 22/24', 'Ventspils', 'LV-3601', 57.3898890, 21.5626430],
        ['LP-LV-014', 'Latvijas Pasta pakomāts T/C Globuss', 'Raiņa iela 26/28', 'Cēsis', 'LV-4101', 57.3092830, 25.2768530],
        ['LP-LV-015', 'Latvijas Pasta pakomāts T/C Aplis', 'Eksporta iela 6', 'Tukums', 'LV-3101', 56.9527290, 23.1159080],
        ['LP-LV-016', 'Latvijas Pasta pakomāts T/C Šokolāde', 'Strēlnieku iela 2', 'Sigulda', 'LV-2150', 57.1431720, 24.8443280],
        ['LP-LV-017', 'Latvijas Pasta pakomāts T/C Sēlija', 'Vienības iela 7', 'Jēkabpils', 'LV-5201', 56.4954690, 25.8783930],
    ];

    public function run(): void
    {
        $this->importet(Pakomats::DPD);
        $this->importet(Pakomats::LATVIJAS_PASTS);
    }

    /**
     * Ieraksta viena pārvadātāja parauga pakomātus (idempotenti) un atgriež skaitu.
     */
    public function importet(string $pakalpojums): int
    {
        $saraksts = match ($pakalpojums) {
            Pakomats::DPD => self::DPD,
            Pakomats::LATVIJAS_PASTS => self::LATVIJAS_PASTS,
            default => [],
        };

        foreach ($saraksts as [$extId, $nosaukums, $adrese, $pilseta, $indekss, $lat, $lng]) {
            Pakomats::updateOrCreate(
                ['pakalpojums' => $pakalpojums, 'ext_id' => $extId],
                [
                    'nosaukums' => $nosaukums,
                    'adrese' => $adrese,
                    'pilseta' => $pilseta,
                    'pasta_indekss' => $indekss,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'darba_laiks' => 'P-Sv 00:00-24:00',
                    'aktivs' => true,
                ]
            );
        }

        return count($saraksts);
    }
}
