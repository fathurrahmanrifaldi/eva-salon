<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Staff;
use App\Support\Tz;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/** Daftar booking hari ini: tampilan utama kasir (AD-03), juga antrean "Perlu konfirmasi". */
class TodayController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Booking::class);

        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', Rule::enum(BookingStatus::class)],
            'staff' => ['nullable', 'integer'],
        ]);

        $pendingScope = $request->query('scope') === 'pending';
        $day = Tz::day($request->query('date', Tz::local(Tz::now())->toDateString()));

        $query = Booking::with(['items', 'staff', 'payments'])
            ->when($pendingScope,
                fn ($q) => $q->where('status', BookingStatus::Pending->value)->orderBy('created_at'),
                fn ($q) => $q->where('start_at', '>=', $day->utc())->where('start_at', '<', $day->addDay()->utc())->orderBy('start_at'))
            ->when($request->filled('status') && ! $pendingScope, fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('staff'), fn ($q) => $q->where('staff_id', $request->staff));

        return view('admin.today', [
            'bookings' => $query->get(),
            'day' => $day,
            'pendingScope' => $pendingScope,
            'staffFilter' => Staff::active()->orderBy('sort_order')->get(['id', 'name']),
            'statuses' => BookingStatus::cases(),
        ]);
    }
}
