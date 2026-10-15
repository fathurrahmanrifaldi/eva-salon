<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use App\Models\GalleryImage;
use App\Models\SalonProfile;
use App\Models\Service;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'profile' => SalonProfile::current(),
            // Layanan sensitif boleh disembunyikan dari beranda (agent.md 6A).
            'featured' => Service::with('category')->where('is_available', true)->where('is_featured', true)
                ->where('is_sensitive', false)->orderBy('sort_order')->limit(6)->get(),
            'packages' => Service::whereHas('category', fn ($q) => $q->where('name', 'Paket Khusus'))
                ->where('is_available', true)->orderBy('sort_order')->get(),
            'gallery' => GalleryImage::orderBy('sort_order')->orderBy('id')->limit(12)->get(),
            'hours' => BusinessHour::orderByRaw('(day_of_week + 6) % 7')->get(), // Senin dulu
        ]);
    }

    public function privacy(): View
    {
        return view('public.privacy', ['profile' => SalonProfile::current()]);
    }
}
