<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

// zonām nav timestamps — kārto pēc ID (tā pati secība kā sākumlapas shēmā)
class ZonuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Zona::withCount('produkti')->orderBy('zonas_id')->get()
        );
    }

    // forma ir dialogs Dashboard lapā
    public function create(): JsonResponse
    {
        return response()->json(new Zona());
    }

    public function store(Request $request): RedirectResponse
    {
        Zona::create($this->validet($request));

        return back()->with('success', 'Zona pievienota.');
    }

    public function edit(Zona $zona): JsonResponse
    {
        return response()->json($zona);
    }

    public function update(Request $request, Zona $zona): RedirectResponse
    {
        $zona->update($this->validet($request));

        return back()->with('success', 'Zona saglabāta.');
    }

    public function destroy(Zona $zona): RedirectResponse
    {
        $zona->delete(); // produktu_zonas dzēšas kaskādē

        return back()->with('success', 'Zona dzēsta.');
    }

    private function validet(Request $request): array
    {
        return $request->validate([
            'nosaukums' => ['required', 'string', 'max:100'],
        ], [
            'nosaukums.required' => 'Lūdzu, ievadi nosaukumu.',
            'nosaukums.max' => 'Nosaukums nedrīkst būt garāks par 100 rakstzīmēm.',
        ]);
    }
}
