<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'isAuthenticated' => $user !== null,
                'user' => $user
                    ? [
                        'lietotaja_id' => $user->lietotaja_id,
                        'vards' => $user->vards,
                        'uzvards' => $user->uzvards,
                        'e_pasts' => $user->e_pasts,
                        'loma' => $user->loma,
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                    : null,
            ],
            'cart' => [
                'count' => $user
                    ? (int) \App\Models\GrozaRinda::where('lietotaja_id', $user->lietotaja_id)->sum('daudzums')
                    : 0,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
