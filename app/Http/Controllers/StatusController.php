<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingRuleException;
use App\Http\Requests\CheckStatusRequest;
use App\Models\Booking;
use App\Models\SalonProfile;
use App\Services\BookingService;
use Illuminate\Contracts\View\View;

/**
 * Cek status dan pembatalan oleh pelanggan. Kode booking DAN nomor telepon harus cocok;
 * pesan gagal selalu generik agar tidak membocorkan keberadaan kode (agent.md 6.5).
 * Hasil dirender langsung dari POST, jadi kode tidak muncul di URL.
 */
class StatusController extends Controller
{
    public function __construct(private BookingService $bookings) {}

    public function form(): View
    {
        return view('public.status', ['booking' => null, 'profile' => SalonProfile::current()]);
    }

    public function check(CheckStatusRequest $request): View
    {
        return $this->render($this->find($request));
    }

    public function cancel(CheckStatusRequest $request): View
    {
        $booking = $this->find($request);

        if (! $booking) {
            return $this->render(null);
        }

        try {
            $this->bookings->cancelByCustomer($booking);
            $message = ['success' => 'Booking berhasil dibatalkan.'];
        } catch (BookingRuleException $e) {
            $message = ['error' => $e->getMessage()];
        }

        return $this->render($this->find($request), $message);
    }

    private function find(CheckStatusRequest $request): ?Booking
    {
        return Booking::with(['items', 'staff'])
            ->where('booking_code', $request->code)
            ->where('customer_phone', $request->phone)
            ->first();
    }

    private function render(?Booking $booking, array $flash = []): View
    {
        $profile = SalonProfile::current();
        $canCancel = $booking
            && in_array($booking->status->value, ['pending', 'confirmed'], true)
            && now()->lt($booking->start_at->copy()->subMinutes($profile->cancel_limit_minutes));

        return view('public.status', [
            'booking' => $booking,
            'canCancel' => $canCancel,
            'profile' => $profile,
            'notFound' => ! $booking,
            ...$flash,
        ]);
    }
}
