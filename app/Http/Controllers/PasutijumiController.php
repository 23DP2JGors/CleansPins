<?php

namespace App\Http\Controllers;

use App\Models\GrozaRinda;
use App\Models\Pakomats;
use App\Models\PasutijumaRinda;
use App\Models\Pasutijums;
use App\Models\Produkts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PasutijumiController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        $items = GrozaRinda::with('produkts')
            ->where('lietotaja_id', $user->lietotaja_id)
            ->orderBy('rindas_id')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('groza.index')->with('error', 'Tavs grozs ir tukšs.');
        }

        return Inertia::render('Checkout', [
            'items' => $items,
            // kontaktformas noklusējuma vērtības no profila
            'defaults' => [
                'vards' => $user->vards,
                'uzvards' => $user->uzvards,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vards' => 'required|string|max:30',
            'uzvards' => 'required|string|max:30',
            'talrunis' => ['required', 'regex:/^\+371\d{8}$/'],
            'pakomata_id' => [
                'required',
                'integer',
                Rule::exists('pakomati', 'pakomata_id')->where('aktivs', true),
            ],
        ], [
            'vards.required' => 'Lūdzu, ievadi vārdu.',
            'vards.max' => 'Vārds nedrīkst būt garāks par 30 rakstzīmēm.',
            'uzvards.required' => 'Lūdzu, ievadi uzvārdu.',
            'uzvards.max' => 'Uzvārds nedrīkst būt garāks par 30 rakstzīmēm.',
            'talrunis.required' => 'Lūdzu, ievadi tālruņa numuru.',
            'talrunis.regex' => 'Tālrunim jābūt formātā +371XXXXXXXX (8 cipari pēc +371).',
            'pakomata_id.required' => 'Lūdzu, izvēlies pakomātu.',
            'pakomata_id.integer' => 'Nederīgs pakomāts.',
            'pakomata_id.exists' => 'Izvēlētais pakomāts nav pieejams.',
        ]);

        $user = $request->user();

        try {
            $pasutijums = DB::transaction(function () use ($data, $user) {
                $items = GrozaRinda::where('lietotaja_id', $user->lietotaja_id)->get();

                if ($items->isEmpty()) {
                    throw new \RuntimeException('Tavs grozs ir tukšs.');
                }

                $pakomats = Pakomats::where('aktivs', true)->find($data['pakomata_id']);

                if (! $pakomats) {
                    throw new \RuntimeException('Izvēlētais pakomāts vairs nav pieejams.');
                }

                $pasutijums = Pasutijums::create([
                    'lietotaja_id' => $user->lietotaja_id,
                    'vards' => $data['vards'],
                    'uzvards' => $data['uzvards'],
                    'talrunis' => $data['talrunis'],
                    'datums' => now(),
                    'statuss' => Pasutijums::STATUS_JAUNS,
                    // "Omniva pakomāts: <nosaukums>, <adrese>"
                    'piegades_adrese' => Str::limit($pakomats->pilnaAdrese(), 255, ''),
                    // pakomāta kopija pasūtīšanas brīdī (paliek pareiza, ja saraksts mainās)
                    'pakomata_id' => $pakomats->pakomata_id,
                    'pakomata_pakalpojums' => $pakomats->pakalpojums,
                    'pakomata_nosaukums' => $pakomats->nosaukums,
                    'pakomata_adrese' => Str::limit($pakomats->adrese . ', ' . $pakomats->pilseta, 255, ''),
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

        $pasutijums->load('rindas.produkts', 'pakomats');

        return Inertia::render('PasutijumsShow', ['order' => $pasutijums]);
    }
}
