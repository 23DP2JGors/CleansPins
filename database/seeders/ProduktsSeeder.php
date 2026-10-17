<?php

namespace Database\Seeders;

use App\Models\Kategorija;
use App\Models\Problema;
use App\Models\Produkts;
use App\Models\Zona;
use Illuminate\Database\Seeder;

class ProduktsSeeder extends Seeder
{
    /**
     * Produktu testa dati. Katram produktam var piesaistīt:
     *  - "zonas"    -> automašīnas zonas interaktīvajai shēmai (04 ekrāna skice)
     *  - "problemas" -> "gudrajam filtram" (01 ekrāna skice)
     * Abas piesaistes iet caur belongsToMany attiecībām Produkts modelī.
     */
    public function run(): void
    {
        $produkti = [
            [
                'nosaukums' => 'Auto šampūns',
                'apraksts' => 'Saudzīgs pH-neitrāls šampūns virsbūves mazgāšanai, nebojā vasku.',
                'cena' => 15.50,
                'krajums' => 40,
                'kategorija' => 'Virsbūves kopšana',
                'zonas' => ['Virsbūve'],
                'problemas' => ['Blāvs lakojums'],
            ],
            [
                'nosaukums' => 'Vaska pulēšanas līdzeklis',
                'apraksts' => 'Veido aizsargājošu vaska kārtu un piešķir virsbūvei spīdumu.',
                'cena' => 18.90,
                'krajums' => 25,
                'kategorija' => 'Virsbūves kopšana',
                'zonas' => ['Virsbūve'],
                'problemas' => ['Blāvs lakojums', 'Skrāpējumi uz virsbūves'],
            ],
            [
                'nosaukums' => 'Virsbūves spray-vasks',
                'apraksts' => 'Ātrai kopšanai bez skalošanas — izsmidzina un noslauka.',
                'cena' => 12.40,
                'krajums' => 30,
                'kategorija' => 'Virsbūves kopšana',
                'zonas' => ['Virsbūve'],
                'problemas' => [],
            ],
            [
                'nosaukums' => 'Skrāpējumu korektors',
                'apraksts' => 'Noslēpj seklus skrāpējumus uz lakotas virsmas.',
                'cena' => 21.90,
                'krajums' => 15,
                'kategorija' => 'Virsbūves kopšana',
                'zonas' => ['Virsbūve'],
                'problemas' => ['Skrāpējumi uz virsbūves'],
            ],
            [
                'nosaukums' => 'Salona tīrītājs',
                'apraksts' => 'Universāls tīrītājs audumam un plastmasas detaļām salonā.',
                'cena' => 12.90,
                'krajums' => 35,
                'kategorija' => 'Salona kopšana',
                'zonas' => ['Salons'],
                'problemas' => ['Traipi uz sēdekļa', 'Netīrumi uz paklājiņiem'],
            ],
            [
                'nosaukums' => 'Ādas kopšanas līdzeklis',
                'apraksts' => 'Baro un aizsargā ādas sēdekļus un apdari no izbalēšanas.',
                'cena' => 16.20,
                'krajums' => 20,
                'kategorija' => 'Salona kopšana',
                'zonas' => ['Salons'],
                'problemas' => ['Traipi uz sēdekļa'],
            ],
            [
                'nosaukums' => 'Salona smaržas atsvaidzinātājs',
                'apraksts' => 'Neitralizē smakas un atstāj vieglu, ilgstošu aromātu.',
                'cena' => 6.50,
                'krajums' => 50,
                'kategorija' => 'Salona kopšana',
                'zonas' => ['Salons'],
                'problemas' => ['Nepatīkama smaka salonā'],
            ],
            [
                'nosaukums' => 'Paklājiņu dziļtīrīšanas līdzeklis',
                'apraksts' => 'Putu tīrītājs iestrādātu netīrumu un traipu izvākšanai no paklājiņiem.',
                'cena' => 14.30,
                'krajums' => 18,
                'kategorija' => 'Salona kopšana',
                'zonas' => ['Salons'],
                'problemas' => ['Netīrumi uz paklājiņiem'],
            ],
            [
                'nosaukums' => 'Disku tīrītājs',
                'apraksts' => 'Efektīvi šķīdina bremžu putekļus un metāla skaidas no diskiem.',
                'cena' => 10.90,
                'krajums' => 28,
                'kategorija' => 'Disku un riepu kopšana',
                'zonas' => ['Diski'],
                'problemas' => ['Putekļi uz diskiem'],
            ],
            [
                'nosaukums' => 'Riepu spīduma līdzeklis',
                'apraksts' => 'Piešķir riepām matētu vai spīdīgu izskatu pēc izvēles.',
                'cena' => 9.40,
                'krajums' => 32,
                'kategorija' => 'Disku un riepu kopšana',
                'zonas' => ['Diski', 'Riepas'],
                'problemas' => [],
            ],
            [
                'nosaukums' => 'Disku aizsargpārklājums',
                'apraksts' => 'Ilgstoša keramiska aizsardzība pret bremžu putekļiem un netīrumiem.',
                'cena' => 22.00,
                'krajums' => 12,
                'kategorija' => 'Disku un riepu kopšana',
                'zonas' => ['Diski'],
                'problemas' => ['Putekļi uz diskiem'],
            ],
            [
                'nosaukums' => 'Stikla tīrītājs',
                'apraksts' => 'Bez svītrām notīra putekļus, taukus un kukaiņu pēdas no stikla.',
                'cena' => 7.90,
                'krajums' => 45,
                'kategorija' => 'Stikla kopšana',
                'zonas' => ['Stikli'],
                'problemas' => ['Kukaiņu pēdas uz priekšējā stikla'],
            ],
            [
                'nosaukums' => 'Pretapledošanas šķidrums',
                'apraksts' => 'Novērš stikla apledošanu zemās temperatūrās.',
                'cena' => 8.50,
                'krajums' => 38,
                'kategorija' => 'Stikla kopšana',
                'zonas' => ['Stikli'],
                'problemas' => ['Apledojums uz stikla'],
            ],
            [
                'nosaukums' => 'Mikrošķiedras drāna (komplekts 3 gab.)',
                'apraksts' => 'Neskrāpējošas drānas virsbūves un stikla slaucīšanai.',
                'cena' => 9.90,
                'krajums' => 60,
                'kategorija' => 'Inventārs un piederumi',
                'zonas' => ['Virsbūve', 'Stikli'],
                'problemas' => [],
            ],
            [
                'nosaukums' => 'Auto mazgāšanas sūklis',
                'apraksts' => 'Mīksts augstas absorbcijas sūklis virsbūves mazgāšanai.',
                'cena' => 4.90,
                'krajums' => 55,
                'kategorija' => 'Inventārs un piederumi',
                'zonas' => ['Virsbūve'],
                'problemas' => [],
            ],
        ];

        foreach ($produkti as $dati) {
            $kategorija = Kategorija::where('nosaukums', $dati['kategorija'])->firstOrFail();

            $produkts = Produkts::firstOrCreate(
                ['nosaukums' => $dati['nosaukums']],
                [
                    'kategorijas_id' => $kategorija->kategorijas_id,
                    'apraksts' => $dati['apraksts'],
                    'cena' => $dati['cena'],
                    'krajums' => $dati['krajums'],
                ]
            );

            if (! empty($dati['zonas'])) {
                $zonuIdi = Zona::whereIn('nosaukums', $dati['zonas'])->pluck('zonas_id');
                $produkts->zonas()->syncWithoutDetaching($zonuIdi);
            }

            if (! empty($dati['problemas'])) {
                $problemuIdi = Problema::whereIn('nosaukums', $dati['problemas'])->pluck('problemas_id');
                $produkts->problemas()->syncWithoutDetaching($problemuIdi);
            }
        }
    }
}
