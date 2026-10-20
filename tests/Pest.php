<?php

use App\Models\GrozaRinda;
use App\Models\Kategorija;
use App\Models\Lietotajs;
use App\Models\Pakomats;
use App\Models\PasutijumaRinda;
use App\Models\Pasutijums;
use App\Models\Produkts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function lietotajs(int $loma = 0): Lietotajs
{
    static $n = 0;
    $n++;

    return Lietotajs::create([
        'vards' => 'Testa',
        'uzvards' => 'Lietotājs' . $n,
        'e_pasts' => "test{$n}@piemers.lv",
        'parole' => 'Parole123!',
        'loma' => $loma,
    ]);
}

function produkts(int $krajums = 10, string $cena = '9.99', string $nosaukums = 'Auto šampūns', array $papildus = []): Produkts
{
    $kategorija = Kategorija::create(['nosaukums' => 'Šampūni']);

    return Produkts::create([
        'kategorijas_id' => $kategorija->kategorijas_id,
        'nosaukums' => $nosaukums,
        'cena' => $cena,
        'krajums' => $krajums,
        ...$papildus,
    ]);
}

function pasutijums(Lietotajs $lietotajs, string $statuss, ?Produkts $produkts = null, int $daudzums = 2): Pasutijums
{
    $pasutijums = Pasutijums::create([
        'lietotaja_id' => $lietotajs->lietotaja_id,
        'datums' => now(),
        'statuss' => $statuss,
        'piegades_adrese' => 'Omniva pakomāts: Tests, Rīga',
        'kopsumma' => 0,
    ]);

    if ($produkts) {
        PasutijumaRinda::create([
            'pasutijuma_id' => $pasutijums->pasutijuma_id,
            'produkta_id' => $produkts->produkta_id,
            'daudzums' => $daudzums,
            'cena_bridi' => $produkts->cena,
        ]);
    }

    return $pasutijums;
}

function grozaRinda(Lietotajs $lietotajs, Produkts $produkts, int $daudzums = 1): GrozaRinda
{
    return GrozaRinda::create([
        'lietotaja_id' => $lietotajs->lietotaja_id,
        'produkta_id' => $produkts->produkta_id,
        'daudzums' => $daudzums,
    ]);
}

function pakomats(array $atributi = []): Pakomats
{
    static $n = 0;
    $n++;

    return Pakomats::create([
        'pakalpojums' => Pakomats::OMNIVA,
        'ext_id' => "ext{$n}",
        'nosaukums' => "Rimi Pakomāts {$n}",
        'adrese' => 'Brīvības iela 1',
        'pilseta' => 'Rīga',
        'latitude' => 56.95,
        'longitude' => 24.11,
        'aktivs' => true,
        ...$atributi,
    ]);
}
