<?php

namespace App\Http\Controllers;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Exceptions\BookingRuleException;
use App\Exceptions\SlotUnavailableException;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\SalonProfile;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Support\Tz;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Alur booking online 5 langkah. Langkah 2-3 memakai fragmen HTML agar JS tetap minimal. */
class BookingController extends Controller
{
    public function __construct(private BookingService $bookings, private AvailabilityService $availability) {}

    public function create(Request $request): View
    {
        $today = Tz::local(Tz::now())->toDateString();

        return view('public.booking', [
            'categories' => ServiceCategory::with(['services' => fn ($q) => $q->orderBy('sort_order')->orderBy('name')])
                ->orderBy('sort_order')->get(),
            'preselected' => array_map('intval', (array) $request->query('layanan', [])),
            'min' => $today,
            'max' => Tz::local(Tz::now())->addDays(config('salon.booking_window_days'))->toDateString(),
            'profile' => SalonProfile::current(),
        ]);
    }

    /** Fragmen: pilihan staf yang bisa mengerjakan semua layanan terpilih. */
    public function staff(Request $request): View
    {
        $services = $this->bookings->services((array) $request->query('services', []));

        return view('public.partials.staff-options', [
            'staff' => Staff::whereIn('id', $this->bookings->candidateStaffIds($services))->orderBy('sort_order')->get(),
            'selected' => $request->query('selected', 'any'),
        ]);
    }

    /** Fragmen: jam yang tersedia pada tanggal dan staf terpilih. */
    public function slots(Request $request): View
    {
        $request->validate(['date' => ['required', 'date_format:Y-m-d']]);

        $services = $this->bookings->services((array) $request->query('services', []));
        $candidates = $this->bookings->candidateStaffIds($services);
        $staffId = $request->query('staff', 'any');
        $ids = $staffId === 'any' ? $candidates : $candidates->filter(fn ($id) => $id === (int) $staffId);

        $slots = $this->availability->slots(
            Tz::day($request->query('date')), $ids, $services->sum('duration_minutes'), $services->sum('buffer_minutes'),
        );

        return view('public.partials.slot-options', ['slots' => array_keys($slots), 'selected' => $request->query('selected')]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        try {
            $booking = $this->bookings->create([
                'service_ids' => $request->service_ids,
                'staff_id' => $request->staff_id,
                'start_at' => $request->startAt(),
                'name' => $request->name,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'source' => BookingSource::Online,
                'status' => BookingStatus::Pending, // selalu menunggu konfirmasi kasir (agent.md 6.1)
                'consent' => true,
                'require_future' => true,
            ]);
        } catch (SlotUnavailableException|BookingRuleException $e) {
            return back()->withInput()->withErrors(['time' => $e->getMessage()]);
        }

        $request->session()->put('last_booking_id', $booking->id);

        return redirect()->route('booking.done');
    }

    public function done(Request $request): View|RedirectResponse
    {
        $booking = Booking::with(['items', 'staff'])->find($request->session()->get('last_booking_id'));

        return $booking
            ? view('public.booking-done', ['booking' => $booking, 'profile' => SalonProfile::current()])
            : redirect()->route('booking.create');
    }
}
