<?php

use App\Models\Lietotajs;

test('profile page is displayed', function () {
    $user = Lietotajs::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = Lietotajs::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'vards' => 'Jānis',
            'uzvards' => 'Bērziņš',
            'e_pasts' => 'janis@piemers.lv',
            'adrese' => 'Brīvības iela 1, Rīga',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Jānis', $user->vards);
    $this->assertSame('Bērziņš', $user->uzvards);
    $this->assertSame('janis@piemers.lv', $user->e_pasts);
    $this->assertSame('Brīvības iela 1, Rīga', $user->adrese);
});

test('profile keeps its own e-mail but cannot take another users e-mail', function () {
    $user = Lietotajs::factory()->create();
    $cits = Lietotajs::factory()->create();

    $this->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'vards' => $user->vards,
            'uzvards' => $user->uzvards,
            'e_pasts' => $user->e_pasts,
        ])
        ->assertSessionHasNoErrors();

    $this->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'vards' => $user->vards,
            'uzvards' => $user->uzvards,
            'e_pasts' => $cits->e_pasts,
        ])
        ->assertSessionHasErrors('e_pasts');
});

test('user can delete their account', function () {
    $user = Lietotajs::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = Lietotajs::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
