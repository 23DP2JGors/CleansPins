<?php

namespace App\Http\Controllers;

use App\Models\Produkts;
use Inertia\Inertia;
use Inertia\Response;

class KatalogsController extends Controller
{
    public function index(): Response
    {
        $products = Produkts::with(['kategorija', 'zonas', 'problemas'])
            ->orderBy('nosaukums')
            ->get();

        return Inertia::render('Katalogs', [
            'products' => $products,
        ]);
    }
}