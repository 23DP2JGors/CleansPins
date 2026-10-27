<?php

use App\Models\Kategorija;
use App\Models\Lietotajs;
use App\Models\Pasutijums;
use App\Models\Produkts;
use App\Models\Zona;

// --- Piekļuve ---

test('pircējs (loma=0) saņem 403 uz visiem /admin maršrutiem', function (string $method, string $uri) {
    $this->actingAs(lietotajs(0))->json($method, $uri)->assertForbidden();
})->with([
    ['GET', '/admin/produkti'],
    ['POST', '/admin/produkti'],
    ['GET', '/admin/kategorijas'],
    ['GET', '/admin/zonas'],
    ['GET', '/admin/problemas'],
    ['GET', '/admin/pasutijumi'],
    ['PATCH', '/admin/pasutijumi/999/statuss'], // neesošs ID — tik un tā 403, ne 404
    ['GET', '/admin/lietotaji'],
    ['DELETE', '/admin/lietotaji/1'],
    ['DELETE', '/admin/produkti/999'],
]);

test('viesis tiek pāradresēts uz login', function () {
    $this->get('/admin/produkti')->assertRedirect('/login');
});

test('administrators redz sarakstus', function () {
    $admin = lietotajs(1);

    foreach (['produkti', 'kategorijas', 'zonas', 'problemas', 'pasutijumi', 'lietotaji'] as $sadala) {
        $this->actingAs($admin)->getJson("/admin/{$sadala}")->assertOk();
    }
});

// --- Produkti ---

test('administrators izveido produktu ar zonām (sync)', function () {
    $admin = lietotajs(1);
    $kategorija = Kategorija::create(['nosaukums' => 'Vaski']);
    $zona = Zona::create(['nosaukums' => 'Virsbūve']);

    $this->actingAs($admin)->post('/admin/produkti', [
        'kategorijas_id' => $kategorija->kategorijas_id,
        'nosaukums' => 'Cietais vasks',
        'cena' => '19.50',
        'krajums' => 5,
        'zonas' => [$zona->zonas_id],
    ])->assertSessionHasNoErrors()->assertSessionHas('success');

    $produkts = Produkts::where('nosaukums', 'Cietais vasks')->firstOrFail();
    expect($produkts->zonas->pluck('zonas_id')->all())->toBe([$zona->zonas_id]);

    $this->actingAs($admin)->put("/admin/produkti/{$produkts->produkta_id}", [
        'kategorijas_id' => $kategorija->kategorijas_id,
        'nosaukums' => 'Cietais vasks',
        'cena' => '19.50',
        'krajums' => 5,
        'zonas' => [],
    ])->assertSessionHasNoErrors();

    expect($produkts->fresh()->zonas)->toHaveCount(0);
});

test('produkta validācija noraida negatīvu cenu', function () {
    $this->actingAs(lietotajs(1))
        ->postJson('/admin/produkti', ['nosaukums' => 'X', 'cena' => -1, 'krajums' => 0, 'kategorijas_id' => 999])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['cena', 'kategorijas_id']);
});

// --- Lietotāji ---

test('administrators nevar dzēst sevi', function () {
    $admin = lietotajs(1);
    lietotajs(1); // otrs admins, lai pārbaudītu tieši "sevi", nevis "pēdējo"

    $this->actingAs($admin)
        ->deleteJson("/admin/lietotaji/{$admin->lietotaja_id}")
        ->assertStatus(422)
        ->assertJsonValidationErrors(['dzest' => 'Savu kontu dzēst šeit nevar.']);

    expect(Lietotajs::find($admin->lietotaja_id))->not->toBeNull();
});

test('administrators nevar mainīt savu lomu', function () {
    $admin = lietotajs(1);
    lietotajs(1);

    $this->actingAs($admin)
        ->putJson("/admin/lietotaji/{$admin->lietotaja_id}", [
            'vards' => $admin->vards,
            'uzvards' => $admin->uzvards,
            'e_pasts' => $admin->e_pasts,
            'loma' => 0,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['loma' => 'Savu lomu mainīt nevar.']);

    expect($admin->fresh()->loma)->toBe(1);
});

test('administrators var labot savus datus, nemainot lomu', function () {
    $admin = lietotajs(1);

    $this->actingAs($admin)
        ->put("/admin/lietotaji/{$admin->lietotaja_id}", [
            'vards' => 'Jauns',
            'uzvards' => $admin->uzvards,
            'e_pasts' => $admin->e_pasts,
            'loma' => 1,
        ])
        ->assertSessionHasNoErrors();

    expect($admin->fresh()->vards)->toBe('Jauns');
});

test('administrators var dzēst citu pircēju un padarīt citu par adminu', function () {
    $admin = lietotajs(1);
    $pircejs = lietotajs(0);
    $cits = lietotajs(0);

    $this->actingAs($admin)
        ->put("/admin/lietotaji/{$cits->lietotaja_id}", [
            'vards' => $cits->vards,
            'uzvards' => $cits->uzvards,
            'e_pasts' => $cits->e_pasts,
            'loma' => 1,
        ])->assertSessionHasNoErrors();

    expect($cits->fresh()->loma)->toBe(1);

    $this->actingAs($admin)->delete("/admin/lietotaji/{$pircejs->lietotaja_id}")->assertSessionHasNoErrors();

    expect(Lietotajs::find($pircejs->lietotaja_id))->toBeNull();
});

test('lietotāju ar aktīviem pasūtījumiem nevar dzēst', function () {
    $admin = lietotajs(1);
    $pircejs = lietotajs(0);
    pasutijums($pircejs, Pasutijums::STATUS_PIEGADATS);

    $this->actingAs($admin)
        ->deleteJson("/admin/lietotaji/{$pircejs->lietotaja_id}")
        ->assertStatus(422)
        ->assertJsonValidationErrors('dzest');
});

test('lietotāju ar atceltiem un aktīviem pasūtījumiem nevar dzēst', function () {
    $admin = lietotajs(1);
    $pircejs = lietotajs(0);
    pasutijums($pircejs, Pasutijums::STATUS_ATCELTS);
    pasutijums($pircejs, Pasutijums::STATUS_JAUNS);

    $this->actingAs($admin)
        ->deleteJson("/admin/lietotaji/{$pircejs->lietotaja_id}")
        ->assertStatus(422)
        ->assertJsonValidationErrors('dzest');

    expect($pircejs->fresh())->not->toBeNull()
        ->and($pircejs->pasutijumi()->count())->toBe(2);
});

test('lietotāju ar tikai atceltiem pasūtījumiem var dzēst, pasūtījumi paliek vēsturē', function () {
    $admin = lietotajs(1);
    $pircejs = lietotajs(0);
    $p = pasutijums($pircejs, Pasutijums::STATUS_ATCELTS, produkts(), 2);

    $this->actingAs($admin)
        ->delete("/admin/lietotaji/{$pircejs->lietotaja_id}")
        ->assertSessionHasNoErrors();

    expect(Lietotajs::find($pircejs->lietotaja_id))->toBeNull()
        ->and($p->fresh())->not->toBeNull()
        ->and($p->fresh()->lietotaja_id)->toBeNull()
        ->and($p->rindas()->count())->toBe(1);

    // admina pasūtījumu sarakstā redzams ar lietotajs = null
    $this->actingAs($admin)->getJson('/admin/pasutijumi')
        ->assertOk()
        ->assertJsonPath('pasutijumi.data.0.pasutijuma_id', $p->pasutijuma_id)
        ->assertJsonPath('pasutijumi.data.0.lietotajs', null);
});

// --- Pasūtījumu statuss (admins) ---

test('administrators maina statusu, filtrē un atceļ ar krājuma atgriešanu', function () {
    $admin = lietotajs(1);
    $produkts = produkts(10);
    $p = pasutijums(lietotajs(0), Pasutijums::STATUS_JAUNS, $produkts, 3);
    pasutijums(lietotajs(0), Pasutijums::STATUS_PIEGADATS);

    $this->actingAs($admin)->getJson('/admin/pasutijumi?statuss=jauns')
        ->assertOk()
        ->assertJsonPath('pasutijumi.total', 1)
        ->assertJsonPath('pasutijumi.data.0.pasutijuma_id', $p->pasutijuma_id);

    $this->actingAs($admin)
        ->patchJson("/admin/pasutijumi/{$p->pasutijuma_id}/statuss", ['statuss' => 'nezināms'])
        ->assertStatus(422);

    $this->actingAs($admin)
        ->patch("/admin/pasutijumi/{$p->pasutijuma_id}/statuss", ['statuss' => Pasutijums::STATUS_ATCELTS])
        ->assertSessionHasNoErrors();

    expect($p->fresh()->statuss)->toBe(Pasutijums::STATUS_ATCELTS)
        ->and($produkts->fresh()->krajums)->toBe(13);

    // atceltu nevar atjaunot
    $this->actingAs($admin)
        ->patchJson("/admin/pasutijumi/{$p->pasutijuma_id}/statuss", ['statuss' => Pasutijums::STATUS_JAUNS])
        ->assertStatus(422);
});

// --- Pircēja atcelšana ---

test('svešu pasūtījumu atcelt nevar (403)', function () {
    $p = pasutijums(lietotajs(0), Pasutijums::STATUS_JAUNS);

    $this->actingAs(lietotajs(0))
        ->post("/pasutijums/{$p->pasutijuma_id}/atcelt")
        ->assertForbidden();

    expect($p->fresh()->statuss)->toBe(Pasutijums::STATUS_JAUNS);
});

test('nosūtītu pasūtījumu atcelt nevar', function () {
    $pircejs = lietotajs(0);
    $p = pasutijums($pircejs, Pasutijums::STATUS_NOSUTITS);

    $this->actingAs($pircejs)
        ->from('/dashboard')
        ->post("/pasutijums/{$p->pasutijuma_id}/atcelt")
        ->assertRedirect('/dashboard')
        ->assertSessionHas('error', 'Šo pasūtījumu vairs nevar atcelt.');

    expect($p->fresh()->statuss)->toBe(Pasutijums::STATUS_NOSUTITS);
});

test('savu jaunu pasūtījumu var atcelt, krājums atgriežas', function () {
    $pircejs = lietotajs(0);
    $produkts = produkts(4);
    $p = pasutijums($pircejs, Pasutijums::STATUS_JAUNS, $produkts, 2);

    $this->actingAs($pircejs)
        ->from('/dashboard')
        ->post("/pasutijums/{$p->pasutijuma_id}/atcelt")
        ->assertRedirect('/dashboard')
        ->assertSessionHas('success', 'Pasūtījums atcelts.');

    expect($p->fresh()->statuss)->toBe(Pasutijums::STATUS_ATCELTS)
        ->and($produkts->fresh()->krajums)->toBe(6);
});
