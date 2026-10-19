<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProduktsRequest;
use App\Models\Kategorija;
use App\Models\Problema;
use App\Models\Produkts;
use App\Models\Zona;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Administrēšana: produkti. Saraksts tiek ielādēts kā JSON (Dashboard → Administrēšana),
 * izmaiņas nāk caur Inertia formām un atgriežas ar flash paziņojumu.
 */
class ProduktuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'produkti' => Produkts::with(['kategorija:kategorijas_id,nosaukums', 'zonas', 'problemas'])
                ->orderBy('nosaukums')
                ->get(),
            ...$this->izveles(),
        ]);
    }

    // forma ir dialogs Dashboard lapā — šeit tikai izvēļu saraksti
    public function create(): JsonResponse
    {
        return response()->json($this->izveles());
    }

    public function store(ProduktsRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $produkts = Produkts::create($request->safe()->except(['zonas', 'problemas']));

            $produkts->zonas()->sync($request->validated('zonas', []));
            $produkts->problemas()->sync($request->validated('problemas', []));
        });

        return back()->with('success', 'Produkts pievienots.');
    }

    public function edit(Produkts $produkti): JsonResponse
    {
        return response()->json([
            'produkts' => $produkti->load('zonas', 'problemas'),
            ...$this->izveles(),
        ]);
    }

    public function update(ProduktsRequest $request, Produkts $produkti): RedirectResponse
    {
        DB::transaction(function () use ($request, $produkti) {
            $produkti->update($request->safe()->except(['zonas', 'problemas']));

            $produkti->zonas()->sync($request->validated('zonas', []));
            $produkti->problemas()->sync($request->validated('problemas', []));
        });

        return back()->with('success', 'Produkts saglabāts.');
    }

    public function destroy(Produkts $produkti): RedirectResponse
    {
        // pasutijuma_rindas → restrictOnDelete: pasūtīts produkts jāpatur vēsturei
        if ($produkti->pasutijumaRindas()->exists()) {
            throw ValidationException::withMessages([
                'dzest' => 'Produktu nevar dzēst — tas ir jau noformētos pasūtījumos. Iestati krājumu uz 0.',
            ]);
        }

        $produkti->delete(); // zonas, problēmas un groza rindas dzēšas kaskādē

        return back()->with('success', 'Produkts dzēsts.');
    }

    private function izveles(): array
    {
        return [
            'kategorijas' => Kategorija::orderBy('nosaukums')->get(['kategorijas_id', 'nosaukums']),
            'zonas' => Zona::orderBy('nosaukums')->get(),
            'problemas' => Problema::orderBy('nosaukums')->get(),
        ];
    }
}
