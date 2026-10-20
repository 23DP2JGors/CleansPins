<?php

use App\Models\Lietotajs;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = Lietotajs::factory()->create();

    $this->post('/forgot-password', ['email' => $user->e_pasts])
        ->assertSessionHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = Lietotajs::factory()->create();

    $this->post('/forgot-password', ['email' => $user->e_pasts]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = Lietotajs::factory()->create();

    $this->post('/forgot-password', ['email' => $user->e_pasts]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->e_pasts,
            'password' => 'JaunaParole123',
            'password_confirmation' => 'JaunaParole123',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        expect(Hash::check('JaunaParole123', $user->fresh()->parole))->toBeTrue();

        return true;
    });
});
