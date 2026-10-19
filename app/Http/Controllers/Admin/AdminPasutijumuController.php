<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasutijums;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminPasutijumuController extends Controller
{
    // visi pasūtījumi pa 20, ar neobligātu ?statuss= filtru
    public function index(Request $request): JsonResponse
    {
        $pasutijumi = Pasutijums::with([
                'lietotajs:lietotaja_id,vards,uzvards',
                'rindas.produkts:produkta_id,nosaukums',
            ])
            ->when($request->filled('statuss'), fn ($query) =>
                $query->where('statuss', $request->statuss))
            ->orderByDesc('datums')
            ->paginate(20)
            ->withQueryString();

        return response()->json([
            'pasutijumi' => $pasutijumi,
            'statusi' => Pasutijums::STATUSI,
        ]);
    }

    public function updateStatuss(Request $request, Pasutijums $pasutijums): RedirectResponse
    {
        $data = $request->validate([
            'statuss' => ['required', Rule::in([
                Pasutijums::STATUS_JAUNS,
                Pasutijums::STATUS_APSTRADE,
                Pasutijums::STATUS_NOSUTITS,
                Pasutijums::STATUS_PIEGADATS,
                Pasutijums::STATUS_ATCELTS,
            ])],
        ], [
            'statuss.required' => 'Lūdzu, izvēlies statusu.',
            'statuss.in' => 'Nederīgs statuss.',
        ]);

        DB::transaction(function () use ($pasutijums, $data) {
            $pasutijums = Pasutijums::lockForUpdate()->find($pasutijums->pasutijuma_id);

            if ($pasutijums->statuss === $data['statuss']) {
                return;
            }

            // preces jau atgrieztas krājumā — atjaunošana varētu pārdot to, kā vairs nav
            if ($pasutijums->statuss === Pasutijums::STATUS_ATCELTS) {
                throw ValidationException::withMessages([
                    'statuss' => 'Atceltu pasūtījumu nevar atjaunot.',
                ]);
            }

            if ($data['statuss'] === Pasutijums::STATUS_ATCELTS) {
                $pasutijums->atcelt();
            } else {
                $pasutijums->update(['statuss' => $data['statuss']]);
            }
        });

        return back()->with('success', 'Pasūtījuma statuss mainīts.');
    }
}
