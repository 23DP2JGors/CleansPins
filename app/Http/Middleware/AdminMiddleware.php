<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Ielaiž tikai administratorus (lietotaji.loma = 1), pārējiem — 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isAdministrators(), 403);

        return $next($request);
    }
}
