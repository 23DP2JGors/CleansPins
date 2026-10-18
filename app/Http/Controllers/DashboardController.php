<?php

namespace App\Http\Controllers;

use App\Models\Pasutijums;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * "Mans konts" — profila dati (no auth.user) un TIKAI paša lietotāja pasūtījumi.
     */
    public function index(Request $request): Response
    {
        $pasutijumi = Pasutijums::where('lietotaja_id', $request->user()->lietotaja_id)
            ->with('rindas.produkts:produkta_id,nosaukums,attels')
            ->orderByDesc('datums')
            ->get();

        return Inertia::render('Dashboard', [
            'pasutijumi' => $pasutijumi,
        ]);
    }
}
