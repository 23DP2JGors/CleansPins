<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LietotajsRequest;
use App\Models\Lietotajs;
use App\Models\Pasutijums;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LietotajuController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Lietotajs::withCount('pasutijumi')->orderBy('uzvards')->orderBy('vards')->get()
        );
    }

    // forma ir dialogs Dashboard lapā
    public function create(): JsonResponse
    {
        return response()->json(new Lietotajs(['loma' => 0]));
    }

    public function store(LietotajsRequest $request): RedirectResponse
    {
        Lietotajs::create($request->validated()); // parole sahashējas (Lietotajs::casts)

        return back()->with('success', 'Lietotājs pievienots.');
    }

    public function edit(Lietotajs $lietotaji): JsonResponse
    {
        return response()->json($lietotaji);
    }

    public function update(LietotajsRequest $request, Lietotajs $lietotaji): RedirectResponse
    {
        $data = $request->validated();

        if ((int) $data['loma'] !== (int) $lietotaji->loma) {
            if ($request->user()->lietotaja_id === $lietotaji->lietotaja_id) {
                throw ValidationException::withMessages([
                    'loma' => 'Savu lomu mainīt nevar.',
                ]);
            }

            if ($lietotaji->isAdministrators() && $this->irPedejaisAdmins()) {
                throw ValidationException::withMessages([
                    'loma' => 'Nevar noņemt tiesības pēdējam administratoram.',
                ]);
            }
        }

        $lietotaji->update($data);

        return back()->with('success', 'Lietotājs saglabāts.');
    }

    public function destroy(Request $request, Lietotajs $lietotaji): RedirectResponse
    {
        if ($request->user()->lietotaja_id === $lietotaji->lietotaja_id) {
            throw ValidationException::withMessages([
                'dzest' => 'Savu kontu dzēst šeit nevar.',
            ]);
        }

        if ($lietotaji->isAdministrators() && $this->irPedejaisAdmins()) {
            throw ValidationException::withMessages([
                'dzest' => 'Nevar dzēst pēdējo administratoru.',
            ]);
        }

        // dzēst drīkst tikai tad, ja visi pasūtījumi ir atcelti
        if ($lietotaji->pasutijumi()->where('statuss', '!=', Pasutijums::STATUS_ATCELTS)->exists()) {
            throw ValidationException::withMessages([
                'dzest' => 'Lietotāju ar aktīviem pasūtījumiem nevar dzēst.',
            ]);
        }

        // groza rindas dzēšas kaskādē; atceltie pasutijumi paliek vēsturē (lietotaja_id → null)
        $lietotaji->delete();

        return back()->with('success', 'Lietotājs dzēsts.');
    }

    private function irPedejaisAdmins(): bool
    {
        return Lietotajs::where('loma', 1)->count() <= 1;
    }
}
