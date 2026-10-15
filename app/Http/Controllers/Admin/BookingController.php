<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentKind;
use App\Enums\PaymentMethod;
use App\Exceptions\BookingRuleException;
use App\Exceptions\SlotUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\HoldRequest;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\TransitionRequest;
use App\Http\Requests\WalkInRequest;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookings) {}

    public function show(Booking $booking): View
    {
        Gate::authorize('view', $booking);

        return view('admin.booking', [
            'booking' => $booking->load(['items', 'staff', 'customer', 'logs.user', 'payments.recorder']),
            'methods' => PaymentMethod::cases(),
            'kinds' => PaymentKind::cases(),
        ]);
    }

    public function walkIn(WalkInRequest $request): RedirectResponse
    {
        Gate::authorize('create', Booking::class);

        $data = [
            'service_ids' => $request->service_ids,
            'staff_id' => $request->staff_id,
            'start_at' => $request->startAt(),
            'name' => $request->name ?: 'Walk-in', // nama tidak wajib untuk walk-in (wireframe B)
            'phone' => $request->phone,
            'notes' => $request->notes,
            'source' => BookingSource::WalkIn,
            'status' => BookingStatus::Confirmed, // walk-in langsung confirmed (WI-04)
        ];

        try {
            $booking = $this->bookings->create($data, $request->user());
        } catch (SlotUnavailableException $e) {
            // Pesan harus membantu: sebut staf yang masih kosong, bukan hanya "gagal".
            $free = $this->bookings->freeStaff($request->service_ids, $request->startAt())->pluck('name');
            $hint = $free->isNotEmpty()
                ? ' Staf yang kosong pada jam ini: '.$free->join(', ').'.'
                : ' Tidak ada staf kosong pada jam ini, coba jam lain.';

            return back()->withInput()->withErrors(['start_time' => $e->getMessage().$hint], 'walkin');
        } catch (BookingRuleException $e) {
            return back()->withInput()->withErrors(['service_ids' => $e->getMessage()], 'walkin');
        }

        return redirect()->route('admin.today')->with('success', "Walk-in tercatat: {$booking->customer_name} ({$booking->booking_code}).");
    }

    public function hold(HoldRequest $request): RedirectResponse
    {
        Gate::authorize('create', Booking::class);

        $holdUntil = $request->holdUntil();
        if ($holdUntil->lte(now()) || $holdUntil->gt(now()->addMinutes(\App\Models\SalonProfile::current()->hold_max_minutes))) {
            return back()->withInput()->withErrors(['hold_until_time' => 'Waktu tahan harus di masa depan dan tidak melebihi batas maksimal.'], 'hold');
        }

        try {
            $booking = $this->bookings->create([
                'service_ids' => $request->service_ids,
                'staff_id' => $request->staff_id,
                'start_at' => $request->startAt(),
                'name' => $request->name,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'source' => BookingSource::PhoneHold,
                'status' => BookingStatus::Hold,
                'hold_until' => $holdUntil,
            ], $request->user());
        } catch (SlotUnavailableException|BookingRuleException $e) {
            return back()->withInput()->withErrors(['time' => $e->getMessage()], 'hold');
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Slot ditahan.');
    }

    /** Hold -> pending, pending -> confirmed. */
    public function confirm(Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        $to = $booking->status === BookingStatus::Hold ? BookingStatus::Pending : BookingStatus::Confirmed;

        return $this->attempt(fn () => $this->bookings->transition($booking, $to, request()->user()), $booking, $to === BookingStatus::Pending ? 'Hold diubah menjadi booking menunggu konfirmasi.' : 'Booking dikonfirmasi.');
    }

    /** Cancel, complete, no-show: status tujuan dari defaults route. */
    public function transition(TransitionRequest $request, Booking $booking, string $to): RedirectResponse
    {
        Gate::authorize('update', $booking);

        $status = BookingStatus::from($to);

        return $this->attempt(
            fn () => $this->bookings->transition($booking, $status, $request->user(), $request->reason),
            $booking,
            'Status diubah menjadi: '.$status->label().'.',
        );
    }

    public function extend(Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        return $this->attempt(fn () => $this->bookings->extendHold($booking), $booking, 'Waktu tahan diperpanjang.');
    }

    public function release(Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        return $this->attempt(
            fn () => $this->bookings->transition($booking, BookingStatus::Cancelled, request()->user(), 'Hold dilepas'),
            $booking,
            'Slot dilepas.',
        );
    }

    public function pay(PaymentRequest $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        return $this->attempt(fn () => $this->bookings->recordPayment(
            $booking, $request->user(), (int) $request->amount,
            PaymentMethod::from($request->method), PaymentKind::from($request->kind), $request->note,
        ), $booking, 'Pembayaran dicatat.');
    }

    private function attempt(callable $action, Booking $booking, string $success): RedirectResponse
    {
        try {
            $action();
        } catch (BookingRuleException|SlotUnavailableException $e) {
            return redirect()->route('admin.bookings.show', $booking)->with('error', $e->getMessage());
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', $success);
    }
}
