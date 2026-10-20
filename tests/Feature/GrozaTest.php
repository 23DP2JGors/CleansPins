<?php

use App\Models\GrozaRinda;
use Inertia\Testing\AssertableInertia as Assert;

// --- Piekļuve ---

test('viesis netiek pie groza', function (string $method, string $uri) {
    $rinda = grozaRinda(lietotajs(), produkts(), 1);

    $this->call($method, str_replace('{rinda}', $rinda->rindas_id, $uri), ['daudzums' => 3])
        ->assertRedirect('/login');

    expect($rinda->fresh()->daudzums)->toBe(1);
})->with([
    ['GET', '/groza'],
    ['POST', '/groza'],
    ['PATCH', '/groza/{rinda}'],
    ['DELETE', '/groza/{rinda}'],
]);

// --- Skatīšana ---

test('grozā redzamas tikai paša rindas ar produkta datiem', function () {
    $pircejs = lietotajs();
    $produkts = produkts(7, '9.99', 'Auto šampūns');
    grozaRinda($pircejs, $produkts, 2);
    grozaRinda(lietotajs(), produkts(5, '1.00', 'Svešs'));

    $this->actingAs($pircejs)
        ->get('/groza')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Groza')
            ->has('items', 1)
            ->where('items.0.produkta_id', $produkts->produkta_id)
            ->where('items.0.daudzums', 2)
            // Groza.vue rāda nosaukumu, cenu un bloķē "+" pēc krājuma
            ->where('items.0.produkts.nosaukums', 'Auto šampūns')
            ->where('items.0.produkts.cena', '9.99')
            ->where('items.0.produkts.krajums', 7)
        );
});

// --- Pievienošana ---

test('produkta pievienošana izveido rindu, atkārtota palielina daudzumu', function () {
    $pircejs = lietotajs();
    $produkts = produkts(10);

    $this->actingAs($pircejs)
        ->post('/groza', ['produkta_id' => $produkts->produkta_id])
        ->assertSessionHas('success');

    $this->actingAs($pircejs)->post('/groza', ['produkta_id' => $produkts->produkta_id]);

    $rindas = GrozaRinda::where('lietotaja_id', $pircejs->lietotaja_id)->get();
    expect($rindas)->toHaveCount(1)
        ->and($rindas->first()->daudzums)->toBe(2);
});

test('daudzums grozā nepārsniedz krājumu', function () {
    $pircejs = lietotajs();
    $produkts = produkts(2);

    foreach (range(1, 4) as $_) {
        $this->actingAs($pircejs)->post('/groza', ['produkta_id' => $produkts->produkta_id]);
    }

    expect(GrozaRinda::firstWhere('lietotaja_id', $pircejs->lietotaja_id)->daudzums)->toBe(2);
});

test('produktu bez krājuma pievienot nevar', function () {
    $pircejs = lietotajs();
    $produkts = produkts(0);

    $this->actingAs($pircejs)
        ->from('/katalogs')
        ->post('/groza', ['produkta_id' => $produkts->produkta_id])
        ->assertRedirect('/katalogs')
        ->assertSessionHas('error', 'Produkts nav pieejams.');

    expect(GrozaRinda::count())->toBe(0);
});

test('neesošu produktu pievienot nevar', function () {
    $this->actingAs(lietotajs())
        ->post('/groza', ['produkta_id' => 999])
        ->assertSessionHasErrors('produkta_id');

    expect(GrozaRinda::count())->toBe(0);
});

// --- Daudzuma maiņa ---

test('daudzumu var mainīt, bet ne virs krājuma', function () {
    $pircejs = lietotajs();
    $rinda = grozaRinda($pircejs, produkts(5));

    $this->actingAs($pircejs)->patch("/groza/{$rinda->rindas_id}", ['daudzums' => 3])
        ->assertSessionHasNoErrors();
    expect($rinda->fresh()->daudzums)->toBe(3);

    $this->actingAs($pircejs)->patch("/groza/{$rinda->rindas_id}", ['daudzums' => 50]);
    expect($rinda->fresh()->daudzums)->toBe(5);
});

test('daudzums nevar būt mazāks par 1', function () {
    $pircejs = lietotajs();
    $rinda = grozaRinda($pircejs, produkts(5), 2);

    $this->actingAs($pircejs)
        ->patch("/groza/{$rinda->rindas_id}", ['daudzums' => 0])
        ->assertSessionHasErrors('daudzums');

    expect($rinda->fresh()->daudzums)->toBe(2);
});

// --- Svešas rindas ---

test('svešu groza rindu mainīt vai dzēst nevar (403)', function () {
    $rinda = grozaRinda(lietotajs(), produkts(5), 1);
    $cits = lietotajs();

    $this->actingAs($cits)->patch("/groza/{$rinda->rindas_id}", ['daudzums' => 3])->assertForbidden();
    $this->actingAs($cits)->delete("/groza/{$rinda->rindas_id}")->assertForbidden();

    expect($rinda->fresh())->not->toBeNull()
        ->and($rinda->fresh()->daudzums)->toBe(1);
});

// --- Dzēšana ---

test('savu rindu var izņemt no groza', function () {
    $pircejs = lietotajs();
    $rinda = grozaRinda($pircejs, produkts());

    $this->actingAs($pircejs)
        ->delete("/groza/{$rinda->rindas_id}")
        ->assertSessionHas('success');

    expect($rinda->fresh())->toBeNull();
});
