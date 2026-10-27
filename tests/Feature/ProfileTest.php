<?php

use App\Models\Lietotajs;
use App\Models\Pasutijums;
use Illuminate\Support\Facades\Hash;

test('profile page is displayed', function () {
    $user = Lietotajs::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = Lietotajs::factory()->create([
        'vards' => 'Original',
        'uzvards' => 'User',
        'e_pasts' => 'original@example.com',
    ]);

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
    $user = Lietotajs::factory()->create([
        'parole' => Hash::make('password'),
    ]);

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

test('deleting account also deletes own orders', function () {
    $user = Lietotajs::factory()->create([
        'parole' => Hash::make('password'),
    ]);
    $order = pasutijums($user, Pasutijums::STATUS_JAUNS, produkts(), 1);

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
    $this->assertDatabaseMissing('pasutijumi', ['pasutijuma_id' => $order->pasutijuma_id]);
    $this->assertDatabaseMissing('pasutijuma_rindas', ['pasutijuma_id' => $order->pasutijuma_id]);
});

test('correct password must be provided to delete account', function () {
    $user = Lietotajs::factory()->create([
        'parole' => Hash::make('password'),
    ]);

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

test('user can delete their own orders before deleting account', function () {
    $user = Lietotajs::factory()->create([
        'parole' => Hash::make('password'),
    ]);

    $order = Pasutijums::create([
        'lietotaja_id' => $user->lietotaja_id,
        'piegades_adrese' => 'Test street 1',
        'kopsumma' => 19.99,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile/orders', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertDatabaseMissing('pasutijumi', ['pasutijuma_id' => $order->pasutijuma_id]);
    $this->assertNotNull($user->fresh());
});
