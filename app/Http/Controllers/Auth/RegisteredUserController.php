<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lietotajs;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'vards' => 'required|string|max:30',
            'uzvards' => 'required|string|max:30',
            'e_pasts' => 'required|string|lowercase|email|max:30|unique:lietotaji,e_pasts',
            'parole' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 'parole' is cast as 'hashed' in the model, so no Hash::make() here.
        $user = Lietotajs::create([
            'vards' => $request->vards,
            'uzvards' => $request->uzvards,
            'e_pasts' => $request->e_pasts,
            'parole' => $request->parole,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
