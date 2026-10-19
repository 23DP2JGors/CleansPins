<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategorija;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KategorijuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Kategorija::withCount('produkti')->orderBy('nosaukums')->get()
        );
    }

    // forma ir dialogs Dashboard lapā
    public function create(): JsonResponse
    {
        return response()->json(new Kategorija());
    }

    public function store(Request $request): RedirectResponse
    {
        Kategorija::create($this->validet($request));

        return back()->with('success', 'Kategorija pievienota.');
    }

    public function edit(Kategorija $kategorija): JsonResponse
    {
        return response()->json($kategorija);
    }

    public function update(Request $request, Kategorija $kategorija): RedirectResponse
    {
        $kategorija->update($this->validet($request));

        return back()->with('success', 'Kategorija saglabāta.');
    }

    public function destroy(Kategorija $kategorija): RedirectResponse
    {
        // produkti → restrictOnDelete
        if ($kategorija->produkti()->exists()) {
            throw ValidationException::withMessages([
                'dzest' => 'Kategoriju nevar dzēst — tajā ir produkti.',
            ]);
        }

        $kategorija->delete();

        return back()->with('success', 'Kategorija dzēsta.');
    }

    private function validet(Request $request): array
    {
        return $request->validate([
            'nosaukums' => ['required', 'string', 'max:255'],
            'apraksts' => ['nullable', 'string'],
        ], [
            'nosaukums.required' => 'Lūdzu, ievadi nosaukumu.',
            'nosaukums.max' => 'Nosaukums nedrīkst būt garāks par 255 rakstzīmēm.',
        ]);
    }
}
