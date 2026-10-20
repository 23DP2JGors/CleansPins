<?php

use App\Models\Lietotajs;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = Lietotajs::factory()->create();

    $response = $this->post('/login', [
        'e_pasts' => $user->e_pasts,
        'parole' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('home', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = Lietotajs::factory()->create();

    $this->post('/login', [
        'e_pasts' => $user->e_pasts,
        'parole' => 'wrong-password',
    ])->assertSessionHasErrors('e_pasts');

    $this->assertGuest();
});

test('users can logout', function () {
    $user = Lietotajs::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
