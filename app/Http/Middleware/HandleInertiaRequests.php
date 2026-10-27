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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user()
                    ? [
                        'lietotaja_id' => $request->user()->lietotaja_id,
                        'vards' => $request->user()->vards,
                        'uzvards' => $request->user()->uzvards,
                        'e_pasts' => $request->user()->e_pasts,
                        'loma' => $request->user()->loma,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                    ]
                    : null,
            ],
            'cart' => [
                'count' => fn () => $request->user()
                    ? (int) \App\Models\GrozaRinda::where('lietotaja_id', $request->user()->lietotaja_id)->sum('daudzums')
                    : 0,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
