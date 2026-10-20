<?php

use App\Models\Zona;
use Inertia\Testing\AssertableInertia as Assert;

test('publiskās lapas atveras viesim', function (string $uri, string $komponente) {
    $this->get($uri)
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($komponente));
})->with([
    ['/', 'Welcome'],
    ['/ka-tas-darbojas', 'HowItWorks'],
    ['/padomi', 'Padomi'],
]);

test('sākumlapa un padomi saņem zonas', function (string $uri) {
    Zona::create(['nosaukums' => 'Virsbūve']);
    Zona::create(['nosaukums' => 'Salons']);

    $this->get($uri)->assertInertia(fn (Assert $page) => $page
        ->has('zones', 2)
        ->where('zones.0.nosaukums', 'Virsbūve')
    );
})->with(['/', '/padomi']);
