<?php

namespace App\Http\Controllers;

use App\Models\Kategorija;
use App\Models\Problema;
use App\Models\Produkts;
use App\Models\Zona;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KatalogsController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Produkts::with(['kategorija', 'zonas', 'problemas'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%' . $request->q . '%';
                $query->where(function ($sub) use ($q) {
                    $sub->where('nosaukums', 'like', $q)
                        ->orWhere('apraksts', 'like', $q);
                });
            })
            ->when($request->filled('kategorija'), fn ($query) =>
                $query->where('kategorijas_id', $request->kategorija))
            ->when($request->filled('zona'), fn ($query) =>
                $query->whereHas('zonas', fn ($z) => $z->where('zonas.zonas_id', $request->zona)))
            ->when($request->filled('problema'), fn ($query) =>
                $query->whereHas('problemas', fn ($p) => $p->where('problemas.problemas_id', $request->problema)))
            ->orderBy('nosaukums')
            ->get();

        return Inertia::render('Katalogs', [
            'products' => $products,
            'total' => Produkts::count(),
            'categories' => Kategorija::orderBy('nosaukums')->get(),
            'zones' => Zona::orderBy('nosaukums')->get(),
            'problems' => Problema::orderBy('nosaukums')->get(),
            'filters' => $request->only(['q', 'kategorija', 'zona', 'problema']),
        ]);
    }
}