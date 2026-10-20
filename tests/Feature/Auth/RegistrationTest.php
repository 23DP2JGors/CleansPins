<?php

use App\Models\Lietotajs;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'vards' => 'Jānis',
        'uzvards' => 'Bērziņš',
        'e_pasts' => 'janis@piemers.lv',
        'parole' => 'password',
        'parole_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));

    $user = Lietotajs::where('e_pasts', 'janis@piemers.lv')->firstOrFail();
    expect($user->loma)->toBe(0); // jauns lietotājs vienmēr ir pircējs
});

test('registration rejects a taken e-mail', function () {
    $esoss = Lietotajs::factory()->create();

    $this->post('/register', [
        'vards' => 'Jānis',
        'uzvards' => 'Bērziņš',
        'e_pasts' => $esoss->e_pasts,
        'parole' => 'password',
        'parole_confirmation' => 'password',
    ])->assertSessionHasErrors('e_pasts');

    $this->assertGuest();
});
