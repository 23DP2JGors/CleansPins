<?php

use App\Models\Pakomats;

test('atgriež tikai aktīvos pakomātus', function () {
    pakomats(['nosaukums' => 'Aktīvs']);
    pakomats(['nosaukums' => 'Slēgts', 'aktivs' => false]);

    $this->getJson('/pakomati')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.nosaukums', 'Aktīvs');
});

test('filtrs pēc pakalpojuma', function () {
    pakomats(['nosaukums' => 'Omniva 1']);
    pakomats(['nosaukums' => 'DPD 1', 'pakalpojums' => Pakomats::DPD]);

    $this->getJson('/pakomati?pakalpojums=dpd')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.nosaukums', 'DPD 1');
});

test('meklēšana strādā bez garumzīmēm un pēc vairākiem vārdiem', function () {
    pakomats(['nosaukums' => 'Rimi Ķengarags', 'pilseta' => 'Rīga']);
    pakomats(['nosaukums' => 'Maxima', 'pilseta' => 'Liepāja']);

    $this->getJson('/pakomati?q=kengarags')->assertJsonCount(1)->assertJsonPath('0.nosaukums', 'Rimi Ķengarags');
    $this->getJson('/pakomati?q=RIGA rimi')->assertJsonCount(1);
    $this->getJson('/pakomati?q=liepaja')->assertJsonCount(1)->assertJsonPath('0.nosaukums', 'Maxima');
    $this->getJson('/pakomati?q=ventspils')->assertJsonCount(0);
});

test('ar koordinātām kārto pēc attāluma un pievieno distance_km', function () {
    pakomats(['nosaukums' => 'Liepāja', 'latitude' => 56.5047, 'longitude' => 21.0108]);
    pakomats(['nosaukums' => 'Rīga', 'latitude' => 56.9496, 'longitude' => 24.1052]);
    pakomats(['nosaukums' => 'Jūrmala', 'latitude' => 56.9680, 'longitude' => 23.7704]);

    $atbilde = $this->getJson('/pakomati?lat=56.95&lng=24.10')->assertOk()->json();

    expect(array_column($atbilde, 'nosaukums'))->toBe(['Rīga', 'Jūrmala', 'Liepāja'])
        ->and($atbilde[0]['distance_km'])->toBeLessThan(1)
        ->and($atbilde[2]['distance_km'])->toBeGreaterThan(150);
});

test('meklēšanas lauks netiek atklāts JSON atbildē', function () {
    pakomats();

    $this->getJson('/pakomati')->assertJsonMissingPath('0.meklesanai');
});

test('validācija', function (string $vaicajums, string $lauks) {
    $this->getJson('/pakomati' . $vaicajums)
        ->assertStatus(422)
        ->assertJsonValidationErrors($lauks);
})->with([
    'nezināms pakalpojums' => ['?pakalpojums=fedex', 'pakalpojums'],
    'lat bez lng' => ['?lat=56.9', 'lng'],
    'lng bez lat' => ['?lng=24.1', 'lat'],
    'lat ārpus robežām' => ['?lat=91&lng=24', 'lat'],
    'all bez pakalpojuma' => ['?all=1', 'pakalpojums'],
]);
