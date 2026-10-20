<?php

use App\Models\Pasutijums;
use Inertia\Testing\AssertableInertia as Assert;

test('viesis tiek pāradresēts uz login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('"Mans konts" rāda tikai paša pasūtījumus, jaunākos pirmos', function () {
    $pircejs = lietotajs();
    $vecs = pasutijums($pircejs, Pasutijums::STATUS_PIEGADATS, produkts());
    $vecs->update(['datums' => now()->subDays(5)]);
    $jauns = pasutijums($pircejs, Pasutijums::STATUS_JAUNS);
    pasutijums(lietotajs(), Pasutijums::STATUS_JAUNS); // svešs

    $this->actingAs($pircejs)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('pasutijumi', 2)
            ->where('pasutijumi.0.pasutijuma_id', $jauns->pasutijuma_id)
            ->where('pasutijumi.1.pasutijuma_id', $vecs->pasutijuma_id)
            ->has('pasutijumi.1.rindas', 1)
        );
});
