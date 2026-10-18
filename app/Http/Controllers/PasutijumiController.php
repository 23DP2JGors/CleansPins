<?php

namespace App\Http\Controllers;

use App\Models\GrozaRinda;
use App\Models\PasutijumaRinda;
use App\Models\Pasutijums;
use App\Models\Produkts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PasutijumiController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $items = GrozaRinda::with('produkts')
            ->where('lietotaja_id', $request->user()->lietotaja_id)
            ->orderBy('rindas_id')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('groza.index')->with('error', 'Tavs grozs ir tukšs.');
        }

        return Inertia::render('Checkout', ['items' => $items]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'piegades_adrese' => 'required|string|min:5|max:255',
        ]);

        $user = $request->user();

        try {
            $pasutijums = DB::transaction(function () use ($data, $user) {
                $items = GrozaRinda::where('lietotaja_id', $user->lietotaja_id)->get();

                if ($items->isEmpty()) {
                    throw new \RuntimeException('Tavs grozs ir tukšs.');
                }

                $pasutijums = Pasutijums::create([
                    'lietotaja_id' => $user->lietotaja_id,
                    'datums' => now(),
                    'statuss' => Pasutijums::STATUS_JAUNS,
                    'piegades_adrese' => $data['piegades_adrese'],
                    'kopsumma' => 0,
                ]);

                foreach ($items as $item) {
                    // lock the product row so two orders can't take the same stock
                    $produkts = Produkts::lockForUpdate()->find($item->produkta_id);

                    if (! $produkts || $produkts->krajums < $item->daudzums) {
                        throw new \RuntimeException(
                            'Nepietiek noliktavā: ' . ($produkts->nosaukums ?? 'produkts')
                        );
                    }

                    PasutijumaRinda::create([
                        'pasutijuma_id' => $pasutijums->pasutijuma_id,
                        'produkta_id' => $produkts->produkta_id,
                        'daudzums' => $item->daudzums,
                        'cena_bridi' => $produkts->cena, // price snapshot
                    ]);

                    $produkts->decrement('krajums', $item->daudzums);
                }

                $pasutijums->parreikinatKopsummu();
                $pasutijums->save();

                GrozaRinda::where('lietotaja_id', $user->lietotaja_id)->delete();

                return $pasutijums;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('groza.index')->with('error', $e->getMessage());
        }

        return redirect()
            ->route('pasutijums.show', $pasutijums->pasutijuma_id)
            ->with('success', 'Pasūtījums pieņemts!');
    }

    public function show(Request $request, Pasutijums $pasutijums): Response
    {
        abort_if($pasutijums->lietotaja_id != $request->user()->lietotaja_id, 403);

        $pasutijums->load('rindas.produkts');

        return Inertia::render('PasutijumsShow', ['order' => $pasutijums]);
    }
}