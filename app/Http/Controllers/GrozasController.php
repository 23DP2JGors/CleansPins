<?php

namespace App\Http\Controllers;

use App\Models\GrozaRinda;
use App\Models\Produkts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GrozasController extends Controller
{
    public function index(Request $request): Response
    {
        $items = GrozaRinda::with('produkts')
            ->where('lietotaja_id', $request->user()->lietotaja_id)
            ->orderBy('rindas_id')
            ->get();

        return Inertia::render('Groza', ['items' => $items]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'produkta_id' => 'required|exists:produkti,produkta_id',
        ]);

        $produkts = Produkts::findOrFail($data['produkta_id']);

        if ($produkts->krajums < 1) {
            return back()->with('error', 'Produkts nav pieejams.');
        }

        $rinda = GrozaRinda::firstOrNew([
            'lietotaja_id' => $request->user()->lietotaja_id,
            'produkta_id' => $produkts->produkta_id,
        ]);

        $rinda->daudzums = min(($rinda->daudzums ?? 0) + 1, $produkts->krajums);
        $rinda->save();

        return back()->with('success', 'Pievienots grozam: ' . $produkts->nosaukums);
    }

    public function update(Request $request, GrozaRinda $rinda): RedirectResponse
    {
        abort_if($rinda->lietotaja_id != $request->user()->lietotaja_id, 403);

        $data = $request->validate([
            'daudzums' => 'required|integer|min:1',
        ]);

        $rinda->daudzums = min($data['daudzums'], max($rinda->produkts->krajums, 1));
        $rinda->save();

        return back();
    }

    public function destroy(Request $request, GrozaRinda $rinda): RedirectResponse
    {
        abort_if($rinda->lietotaja_id != $request->user()->lietotaja_id, 403);

        $rinda->delete();

        return back()->with('success', 'Produkts izņemts no groza.');
    }
}