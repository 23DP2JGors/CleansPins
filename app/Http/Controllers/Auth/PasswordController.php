<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Lūdzu, ievadi pašreizējo paroli.',
            'current_password.current_password' => 'Pašreizējā parole nav pareiza.',
            'password.required' => 'Lūdzu, ievadi jauno paroli.',
            'password.confirmed' => 'Paroles nesakrīt.',
            'password.min' => 'Parolei jābūt vismaz :min rakstzīmes garai.',
        ]);

        // kolonna ir "parole" (nevis "password"); Lietotajs to automātiski šifrē ar 'hashed' cast
        $request->user()->update([
            'parole' => $validated['password'],
        ]);

        return back()->with('success', 'Parole nomainīta.');
    }
}
