<?php

use App\Models\GrozaRinda;
use App\Models\Pasutijums;
use Inertia\Testing\AssertableInertia as Assert;

function pasutijumaDati(array $papildus = []): array
{
    return [
        'vards' => 'Jānis',
        'uzvards' => 'Bērziņš',
        'talrunis' => '+37120000000',
        'pakomata_id' => pakomats()->pakomata_id,
        ...$papildus,
    ];
}

// --- Noformēšanas lapa ---

test('ar tukšu grozu noformēšanas lapa pāradresē uz grozu', function () {
    $this->actingAs(lietotajs())
        ->get('/pasutijums')
        ->assertRedirect(route('groza.index'))
        ->assertSessionHas('error', 'Tavs grozs ir tukšs.');
});

test('noformēšanas lapa rāda groza preces un profila vārdu', function () {
    $pircejs = lietotajs();
    grozaRinda($pircejs, produkts());

    $this->actingAs($pircejs)
        ->get('/pasutijums')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Checkout')
            ->has('items', 1)
            ->where('defaults.vards', $pircejs->vards)
            ->where('defaults.uzvards', $pircejs->uzvards)
        );
});

// --- Veiksmīgs pasūtījums ---

test('pasūtījums tiek izveidots: summa, cenas kopija, krājums, tukšs grozs', function () {
    $pircejs = lietotajs();
    $samp = produkts(10, '9.99', 'Šampūns');
    $vasks = produkts(3, '5.50', 'Vasks');
    grozaRinda($pircejs, $samp, 2);
    grozaRinda($pircejs, $vasks, 1);
    $pakomats = pakomats(['nosaukums' => 'Rimi Teika', 'adrese' => 'Brīvības gatve 372']);

    $response = $this->actingAs($pircejs)->post('/pasutijums', pasutijumaDati([
        'pakomata_id' => $pakomats->pakomata_id,
    ]));

    $pasutijums = Pasutijums::firstOrFail();

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('pasutijums.show', $pasutijums->pasutijuma_id))
        ->assertSessionHas('success', 'Pasūtījums pieņemts!');

    // 2 × 9.99 + 1 × 5.50
    expect($pasutijums->kopsumma)->toBe('25.48')
        ->and($pasutijums->statuss)->toBe(Pasutijums::STATUS_JAUNS)
        ->and($pasutijums->lietotaja_id)->toBe($pircejs->lietotaja_id)
        ->and($pasutijums->talrunis)->toBe('+37120000000')
        ->and($pasutijums->rindas)->toHaveCount(2);

    // pakomāta kopija pasūtīšanas brīdī
    expect($pasutijums->pakomata_id)->toBe($pakomats->pakomata_id)
        ->and($pasutijums->pakomata_nosaukums)->toBe('Rimi Teika')
        ->and($pasutijums->piegades_adrese)->toBe('Omniva pakomāts: Rimi Teika, Brīvības gatve 372');

    // krājums samazināts, grozs iztukšots
    expect($samp->fresh()->krajums)->toBe(8)
        ->and($vasks->fresh()->krajums)->toBe(2)
        ->and(GrozaRinda::where('lietotaja_id', $pircejs->lietotaja_id)->count())->toBe(0);
});

test('vēlāka cenas maiņa neietekmē jau noformētu pasūtījumu', function () {
    $pircejs = lietotajs();
    $produkts = produkts(10, '9.99');
    grozaRinda($pircejs, $produkts, 1);

    $this->actingAs($pircejs)->post('/pasutijums', pasutijumaDati());
    $produkts->update(['cena' => '19.99']);

    $pasutijums = Pasutijums::firstOrFail();
    expect($pasutijums->rindas->first()->cena_bridi)->toEqual('9.99')
        ->and($pasutijums->fresh()->kopsumma)->toBe('9.99');
});

// --- Kļūdas ---

test('ja krājuma nepietiek, nekas netiek saglabāts (transakcija atceļas)', function () {
    $pircejs = lietotajs();
    $pietiek = produkts(10, '1.00', 'Pietiek');
    $nepietiek = produkts(1, '2.00', 'Nepietiek');
    grozaRinda($pircejs, $pietiek, 2);
    grozaRinda($pircejs, $nepietiek, 3); // piem., krājums samazinājās, kamēr prece bija grozā

    $this->actingAs($pircejs)
        ->post('/pasutijums', pasutijumaDati())
        ->assertRedirect(route('groza.index'))
        ->assertSessionHas('error', 'Nepietiek noliktavā: Nepietiek');

    expect(Pasutijums::count())->toBe(0)
        ->and($pietiek->fresh()->krajums)->toBe(10)
        ->and($nepietiek->fresh()->krajums)->toBe(1)
        ->and(GrozaRinda::where('lietotaja_id', $pircejs->lietotaja_id)->count())->toBe(2);
});

test('ar tukšu grozu pasūtījumu noformēt nevar', function () {
    $this->actingAs(lietotajs())
        ->post('/pasutijums', pasutijumaDati())
        ->assertRedirect(route('groza.index'))
        ->assertSessionHas('error', 'Tavs grozs ir tukšs.');

    expect(Pasutijums::count())->toBe(0);
});

test('pasūtījuma validācija', function (array $dati, string $lauks) {
    $pircejs = lietotajs();
    grozaRinda($pircejs, produkts());

    $this->actingAs($pircejs)
        ->post('/pasutijums', pasutijumaDati($dati))
        ->assertSessionHasErrors($lauks);

    expect(Pasutijums::count())->toBe(0);
})->with([
    'bez vārda' => [['vards' => ''], 'vards'],
    'bez uzvārda' => [['uzvards' => ''], 'uzvards'],
    'tālrunis bez +371' => [['talrunis' => '20000000'], 'talrunis'],
    'tālrunis par īsu' => [['talrunis' => '+3712000000'], 'talrunis'],
    'neesošs pakomāts' => [['pakomata_id' => 999], 'pakomata_id'],
]);

test('neaktīvu pakomātu izvēlēties nevar', function () {
    $pircejs = lietotajs();
    grozaRinda($pircejs, produkts());
    $neaktivs = pakomats(['aktivs' => false]);

    $this->actingAs($pircejs)
        ->post('/pasutijums', pasutijumaDati(['pakomata_id' => $neaktivs->pakomata_id]))
        ->assertSessionHasErrors('pakomata_id');

    expect(Pasutijums::count())->toBe(0);
});

// --- Pasūtījuma skatīšana ---

test('savu pasūtījumu var apskatīt', function () {
    $pircejs = lietotajs();
    $p = pasutijums($pircejs, Pasutijums::STATUS_JAUNS, produkts());

    $this->actingAs($pircejs)
        ->get("/pasutijums/{$p->pasutijuma_id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PasutijumsShow')
            ->where('order.pasutijuma_id', $p->pasutijuma_id)
            ->has('order.rindas', 1)
        );
});

test('svešu pasūtījumu apskatīt nevar (403)', function () {
    $p = pasutijums(lietotajs(), Pasutijums::STATUS_JAUNS);

    $this->actingAs(lietotajs())
        ->get("/pasutijums/{$p->pasutijuma_id}")
        ->assertForbidden();
});

test('viesis netiek pie pasūtījumiem', function () {
    $this->get('/pasutijums')->assertRedirect('/login');
    $this->post('/pasutijums', [])->assertRedirect('/login');
});
