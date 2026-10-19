<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Problema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

// problēmām nav timestamps — kārto pēc nosaukuma
class ProblemuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Problema::withCount('produkti')->orderBy('nosaukums')->get()
        );
    }

    // forma ir dialogs Dashboard lapā
    public function create(): JsonResponse
    {
        return response()->json(new Problema());
    }

    public function store(Request $request): RedirectResponse
    {
        Problema::create($this->validet($request));

        return back()->with('success', 'Problēma pievienota.');
    }

    public function edit(Problema $problema): JsonResponse
    {
        return response()->json($problema);
    }

    public function update(Request $request, Problema $problema): RedirectResponse
    {
        $problema->update($this->validet($request));

        return back()->with('success', 'Problēma saglabāta.');
    }

    public function destroy(Problema $problema): RedirectResponse
    {
        $problema->delete(); // produktu_problemas dzēšas kaskādē

        return back()->with('success', 'Problēma dzēsta.');
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
