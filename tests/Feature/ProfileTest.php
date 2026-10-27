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
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->vards);
    $this->assertSame('test@example.com', $user->e_pasts);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = Lietotajs::factory()->create([
        'e_pasts' => 'same@example.com',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh());
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
