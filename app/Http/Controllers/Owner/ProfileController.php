<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\BusinessHour;
use App\Models\GalleryImage;
use App\Models\SalonProfile;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Profil salon, aturan bisnis, jam operasional, dan galeri (PS-01, PS-02, SF-03). */
class ProfileController extends Controller
{
    private const MAX_GALLERY = 12;

    public function __construct(private ImageService $images) {}

    public function edit(): View
    {
        Gate::authorize('viewAny', SalonProfile::class);

        $hours = BusinessHour::orderByRaw('(day_of_week + 6) % 7')->get()->keyBy('day_of_week');

        return view('owner.profile', [
            'profile' => SalonProfile::current(),
            'hours' => $hours,
            'gallery' => GalleryImage::orderBy('sort_order')->orderBy('id')->get(),
            'maxGallery' => self::MAX_GALLERY,
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        Gate::authorize('update', SalonProfile::class);

        DB::transaction(function () use ($request) {
            $profile = SalonProfile::query()->first() ?? new SalonProfile;
            $profile->fill($request->safe()->except('hours'));
            $profile->require_payment_to_complete = $request->boolean('require_payment_to_complete');
            $profile->save();

            foreach ($request->input('hours') as $day => $h) {
                $closed = ! empty($h['is_closed']);
                BusinessHour::updateOrCreate(['day_of_week' => (int) $day], [
                    'is_closed' => $closed,
                    'open_time' => $closed ? null : ($h['open_time'] ?: null),
                    'close_time' => $closed ? null : ($h['close_time'] ?: null),
                ]);
            }
        });

        return back()->with('success', 'Profil salon disimpan.');
    }

    public function storeImage(GalleryRequest $request): RedirectResponse
    {
        Gate::authorize('create', GalleryImage::class);

        if (GalleryImage::count() >= self::MAX_GALLERY) {
            return back()->withErrors(['image' => 'Galeri penuh (maksimal '.self::MAX_GALLERY.' foto). Hapus satu foto dulu.']);
        }

        GalleryImage::create([
            'path' => $this->images->store($request->file('image'), 'gallery'),
            'caption' => $request->caption,
            'sort_order' => (int) GalleryImage::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Foto ditambahkan.');
    }

    public function destroyImage(GalleryImage $image): RedirectResponse
    {
        Gate::authorize('delete', $image);

        $this->images->delete($image->path);
        $image->delete();

        return back()->with('success', 'Foto dihapus.');
    }
}
