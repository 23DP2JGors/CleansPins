<?php

use App\Models\GrozaRinda;
use App\Models\Kategorija;
use App\Models\Pasutijums;
use App\Models\Problema;
use App\Models\Produkts;
use App\Models\Zona;

// --- Kategorijas, zonas, problēmas: kopīgs CRUD ---

dataset('vardnicas', [
    'kategorijas' => ['kategorijas', Kategorija::class],
    'zonas' => ['zonas', Zona::class],
    'problemas' => ['problemas', Problema::class],
]);

test('administrators izveido, labo un dzēš ierakstu', function (string $sadala, string $modelis) {
    $admin = lietotajs(1);

    $this->actingAs($admin)
        ->post("/admin/{$sadala}", ['nosaukums' => 'Jauns'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success');

    $ieraksts = $modelis::where('nosaukums', 'Jauns')->firstOrFail();

    $this->actingAs($admin)
        ->getJson("/admin/{$sadala}/{$ieraksts->getKey()}/edit")
        ->assertOk()
        ->assertJsonPath('nosaukums', 'Jauns');

    $this->actingAs($admin)
        ->put("/admin/{$sadala}/{$ieraksts->getKey()}", ['nosaukums' => 'Labots'])
        ->assertSessionHasNoErrors();

    expect($ieraksts->fresh()->nosaukums)->toBe('Labots');

    $this->actingAs($admin)
        ->delete("/admin/{$sadala}/{$ieraksts->getKey()}")
        ->assertSessionHasNoErrors();

    expect($ieraksts->fresh())->toBeNull();
})->with('vardnicas');

test('nosaukums ir obligāts', function (string $sadala, string $modelis) {
    $this->actingAs(lietotajs(1))
        ->postJson("/admin/{$sadala}", ['nosaukums' => ''])
        ->assertStatus(422)
        ->assertJsonValidationErrors('nosaukums');

    expect($modelis::count())->toBe(0);
})->with('vardnicas');

test('zonas un problēmas nosaukums max 100 rakstzīmes', function (string $sadala) {
    $this->actingAs(lietotajs(1))
        ->postJson("/admin/{$sadala}", ['nosaukums' => str_repeat('a', 101)])
        ->assertStatus(422)
        ->assertJsonValidationErrors('nosaukums');
})->with(['zonas', 'problemas']);

test('saraksts rāda produktu skaitu', function () {
    $produkts = produkts();

    $this->actingAs(lietotajs(1))
        ->getJson('/admin/kategorijas')
        ->assertOk()
        ->assertJsonPath('0.produkti_count', 1)
        ->assertJsonPath('0.kategorijas_id', $produkts->kategorijas_id);
});

test('kategoriju ar produktiem dzēst nevar', function () {
    $produkts = produkts();

    $this->actingAs(lietotajs(1))
        ->deleteJson("/admin/kategorijas/{$produkts->kategorijas_id}")
        ->assertStatus(422)
        ->assertJsonValidationErrors('dzest');

    expect(Kategorija::find($produkts->kategorijas_id))->not->toBeNull();
});

test('zonas dzēšana noņem tikai saiti, produkts paliek', function () {
    $zona = Zona::create(['nosaukums' => 'Diski']);
    $produkts = produkts();
    $produkts->zonas()->attach($zona->zonas_id);

    $this->actingAs(lietotajs(1))->delete("/admin/zonas/{$zona->zonas_id}");

    expect($produkts->fresh())->not->toBeNull()
        ->and($produkts->fresh()->zonas)->toHaveCount(0);
});

test('problēmas dzēšana noņem tikai saiti, produkts paliek', function () {
    $problema = Problema::create(['nosaukums' => 'Traipi']);
    $produkts = produkts();
    $produkts->problemas()->attach($problema->problemas_id);

    $this->actingAs(lietotajs(1))->delete("/admin/problemas/{$problema->problemas_id}");

    expect($produkts->fresh())->not->toBeNull()
        ->and($produkts->fresh()->problemas)->toHaveCount(0);
});

// --- Produkta dzēšana ---

test('produktu var dzēst; tā groza rindas un saites dzēšas līdzi', function () {
    $produkts = produkts();
    $zona = Zona::create(['nosaukums' => 'Salons']);
    $produkts->zonas()->attach($zona->zonas_id);
    grozaRinda(lietotajs(), $produkts);

    $this->actingAs(lietotajs(1))
        ->delete("/admin/produkti/{$produkts->produkta_id}")
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success');

    expect(Produkts::find($produkts->produkta_id))->toBeNull()
        ->and(GrozaRinda::count())->toBe(0)
        ->and(Zona::find($zona->zonas_id))->not->toBeNull();
});

test('pasūtītu produktu dzēst nevar', function () {
    $produkts = produkts();
    pasutijums(lietotajs(), Pasutijums::STATUS_PIEGADATS, $produkts);

    $this->actingAs(lietotajs(1))
        ->deleteJson("/admin/produkti/{$produkts->produkta_id}")
        ->assertStatus(422)
        ->assertJsonValidationErrors('dzest');

    expect(Produkts::find($produkts->produkta_id))->not->toBeNull();
});

test('pircējs nevar mainīt vārdnīcas (403)', function (string $sadala, string $modelis) {
    $this->actingAs(lietotajs(0))
        ->postJson("/admin/{$sadala}", ['nosaukums' => 'Hack'])
        ->assertForbidden();

    expect($modelis::count())->toBe(0);
})->with('vardnicas');
