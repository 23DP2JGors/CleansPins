<?php

namespace App\Http\Controllers;

use App\Models\Pakomats;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PakomatiController extends Controller
{
    private const LIMITS = 50;
    private const LIMITS_KARTEI = 1000;

    /**
     * GET /pakomati — pakomātu meklēšana (JSON).
     *   ?pakalpojums=omniva|dpd|latvijas_pasts
     *   ?q=teksts            — nosaukums / adrese / pilsēta
     *   ?lat=..&lng=..       — kārto pēc attāluma, pievieno distance_km
     *   ?all=1&pakalpojums=  — visi viena pārvadātāja pakomāti kartei (max 1000)
     */
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pakalpojums' => ['nullable', Rule::in(array_keys(Pakomats::PAKALPOJUMI)), 'required_if:all,1'],
            'q' => 'nullable|string|max:100',
            'lat' => 'nullable|numeric|between:-90,90|required_with:lng',
            'lng' => 'nullable|numeric|between:-180,180|required_with:lat',
            'all' => 'nullable|boolean',
        ], [
            'pakalpojums.in' => 'Nezināms pakalpojums.',
            'pakalpojums.required_if' => 'Lai saņemtu visus pakomātus, jānorāda pakalpojums.',
            'lat.required_with' => 'Jānorāda gan platums, gan garums.',
            'lng.required_with' => 'Jānorāda gan platums, gan garums.',
        ]);

        $visi = $request->boolean('all');

        $query = Pakomats::query()
            ->select([
                'pakomata_id', 'pakalpojums', 'nosaukums', 'adrese',
                'pilseta', 'latitude', 'longitude', 'darba_laiks',
            ])
            ->where('aktivs', true);

        if (! empty($data['pakalpojums'])) {
            $query->where('pakalpojums', $data['pakalpojums']);
        }

        // meklē normalizētajā laukā — strādā ar/bez garumzīmēm un neatkarīgi no reģistra
        $q = Pakomats::normalizet($data['q'] ?? '');
        if ($q !== '') {
            // katram vārdam jāsakrīt; LIKE speciālos simbolus izmetam
            $q = str_replace(['%', '_'], '', $q);
            foreach (preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) as $vards) {
                $query->where('meklesanai', 'like', '%' . $vards . '%');
            }
        }

        $limits = $visi ? self::LIMITS_KARTEI : self::LIMITS;

        // bez koordinātām — vienkārši alfabētiski
        if (! isset($data['lat'], $data['lng'])) {
            return response()->json(
                $query->orderBy('pilseta')->orderBy('nosaukums')->limit($limits)->get()
            );
        }

        // Ar koordinātām: attālumu rēķina PHP (haversine), jo SQLite nav trigonometrijas funkciju.
        // Latvijā kopā ir ~1500 pakomātu, tāpēc kandidātu atlase atmiņā ir pietiekami ātra.
        $lat = (float) $data['lat'];
        $lng = (float) $data['lng'];

        $rezultati = $query->get()
            ->each(function (Pakomats $p) use ($lat, $lng) {
                $p->distance_km = round(Pakomats::attalumsKm($lat, $lng, $p->latitude, $p->longitude), 2);
            })
            ->sortBy('distance_km')
            ->take($limits)
            ->values();

        return response()->json($rezultati);
    }
}
