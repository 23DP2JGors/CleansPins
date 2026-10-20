<?php

use App\Models\Problema;
use App\Models\Zona;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * Katalogs ir publisks; produktus atgriež alfabētiskā secībā,
 * tāpēc pārbaudām nosaukumu sarakstu.
 */
function katalogaNosaukumi($test, string $vaicajums = ''): array
{
    $nosaukumi = [];

    $test->get('/katalogs' . $vaicajums)
        ->assertOk()
        ->assertInertia(function (Assert $page) use (&$nosaukumi) {
            $page->component('Katalogs');
            $nosaukumi = array_column($page->toArray()['props']['products'], 'nosaukums');
        });

    return $nosaukumi;
}

test('katalogs pieejams viesim un rāda visus produktus alfabētiski', function () {
    produkts(5, '1.00', 'Vasks');
    produkts(5, '1.00', 'Auto šampūns');

    expect(katalogaNosaukumi($this))->toBe(['Auto šampūns', 'Vasks']);
});

test('meklēšana pēc nosaukuma un apraksta', function () {
    produkts(5, '1.00', 'Cietais vasks');
    produkts(5, '1.00', 'Šampūns', ['apraksts' => 'Satur vasku, dod spīdumu']);
    produkts(5, '1.00', 'Salona tīrītājs');

    expect(katalogaNosaukumi($this, '?q=vask'))->toBe(['Cietais vasks', 'Šampūns']);
});

test('filtrs pēc kategorijas', function () {
    $a = produkts(5, '1.00', 'A');
    produkts(5, '1.00', 'B'); // cita kategorija (helperis katram produktam veido savu)

    expect(katalogaNosaukumi($this, '?kategorija=' . $a->kategorijas_id))->toBe(['A']);
});

test('filtrs pēc zonas', function () {
    $zona = Zona::create(['nosaukums' => 'Salons']);
    $salonam = produkts(5, '1.00', 'Salonam');
    $salonam->zonas()->attach($zona->zonas_id);
    produkts(5, '1.00', 'Virsbūvei');

    expect(katalogaNosaukumi($this, '?zona=' . $zona->zonas_id))->toBe(['Salonam']);
});

test('filtrs pēc problēmas', function () {
    $problema = Problema::create(['nosaukums' => 'Traipi']);
    $traipiem = produkts(5, '1.00', 'Traipu tīrītājs');
    $traipiem->problemas()->attach($problema->problemas_id);
    produkts(5, '1.00', 'Vasks');

    expect(katalogaNosaukumi($this, '?problema=' . $problema->problemas_id))->toBe(['Traipu tīrītājs']);
});

test('filtri apvienojas un "total" rāda kopējo skaitu', function () {
    $zona = Zona::create(['nosaukums' => 'Salons']);
    $a = produkts(5, '1.00', 'Salona vasks');
    $a->zonas()->attach($zona->zonas_id);
    produkts(5, '1.00', 'Virsbūves vasks');
    $c = produkts(5, '1.00', 'Salona šampūns');
    $c->zonas()->attach($zona->zonas_id);

    $this->get('/katalogs?q=vasks&zona=' . $zona->zonas_id)
        ->assertInertia(fn (Assert $page) => $page
            ->has('products', 1)
            ->where('products.0.nosaukums', 'Salona vasks')
            ->where('total', 3)
            ->where('filters.q', 'vasks')
        );
});
