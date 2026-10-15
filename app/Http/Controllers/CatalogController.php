<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        return view('public.catalog', [
            'categories' => ServiceCategory::with(['services' => fn ($q) => $q->orderBy('sort_order')->orderBy('name')])
                ->orderBy('sort_order')->get(),
        ]);
    }
}
