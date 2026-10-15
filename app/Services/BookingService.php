<?php

namespace App\Services;

use App\Enums\ActorType;
use App\Enums\BookingSource;
use App\Enums\BookingStatus;
use App\Enums\PaymentKind;
use App\Enums\PaymentMethod;
use App\Exceptions\BookingRuleException;
use App\Exceptions\SlotUnavailableException;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SalonProfile;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Support\Tz;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Pintu tunggal untuk membuat booking/hold dan mengubah statusnya (agent.md 6.1-6.4).
 * Jangan menulis logika overlap atau transisi status di tempat lain.
 */
class BookingService
{
    private const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa 0/O/1/I

    public function __construct(private AvailabilityService $availability) {}

    /**
     * Buat booking (online, walk-in) atau hold.
     *
     * Kunci data: service_ids, staff_id (null = siapa saja), start_at (CarbonImmutable UTC),
     * name, phone (+62 atau null), notes, source, status, hold_until, consent, require_future.
     *
     * @throws SlotUnavailableException|BookingRuleException
     */
    public function create(array $d, ?User $actor = null): Booking
    {
        $services = $this->services($d['service_ids']);
        $duration = $services->sum('duration_minutes');
        $buffer = $services->sum('buffer_minutes');
        $start = $d['start_at'];

        if (($d['require_future'] ?? false) && $start <= Tz::now()) {
            throw new SlotUnavailableException('Jam yang dipilih sudah lewat.');
        }

        $candidates = $this->candidateStaffIds($services);
        if (! empty($d['staff_id'])) {
            if (! $candidates->contains((int) $d['staff_id'])) {
                throw new BookingRuleException('Staf tersebut tidak dapat mengerjakan layanan yang dipilih.');
            }
            $candidates = collect([(int) $d['staff_id']]);
        }
        if ($candidates->isEmpty()) {
            throw new BookingRuleException('Belum ada staf untuk layanan yang dipilih.');
        }

        return DB::transaction(function () use ($d, $services, $duration, $buffer, $start, $candidates, $actor) {
            // Kunci baris staf (urut id agar tidak deadlock) sebelum cek ulang.
            Staff::whereIn('id', $candidates)->orderBy('id')->lockForUpdate()->get(['id']);

            $ctx = $this->availability->context(Tz::day($start), $candidates);
            $free = $candidates->filter(fn ($id) => $this->availability->fits($ctx, $id, $start, $duration, $buffer));

            if ($free->isEmpty()) {
                throw new SlotUnavailableException;
            }

            // "Siapa saja": staf dengan booking paling sedikit hari itu (PRD 7.2).
            $staffId = $free->sortBy(fn ($id) => count($ctx['staff'][$id]['busy']))->first();

            $end = $start->addMinutes($duration);
            $status = $d['status'] ?? BookingStatus::Pending;
            $customer = $this->customer($d);

            $booking = Booking::create([
                'booking_code' => $this->newCode(),
                'customer_id' => $customer?->id,
                'customer_name' => $d['name'],
                'customer_phone' => $d['phone'] ?? null,
                'staff_id' => $staffId,
                'start_at' => $start,
                'end_at' => $end,
                'blocked_until' => $end->addMinutes($buffer),
                'status' => $status,
                'source' => $d['source'] ?? BookingSource::Online,
                'hold_until' => $status === BookingStatus::Hold ? $d['hold_until'] : null,
                'total_price' => $services->sum('price'),
                'total_duration' => $duration,
                'notes' => $d['notes'] ?? null,
                'created_by' => $actor?->id,
                'confirmed_by' => $status === BookingStatus::Confirmed ? $actor?->id : null,
                'consent_at' => ! empty($d['consent']) ? now() : null,
            ]);

            foreach ($services->values() as $i => $s) {
                $booking->items()->create([
                    'service_id' => $s->id,
                    'service_name' => $s->name,
                    'price_snapshot' => $s->price, // harga minimum jika berentang (agent.md 5)
                    'duration_snapshot' => $s->duration_minutes,
                    'buffer_snapshot' => $s->buffer_minutes,
                    'sequence' => $i + 1,
                ]);
            }

            $this->log($booking, null, $status, $actor, $this->actorType($actor), null);

            return $booking;
        });
    }

    /** @throws BookingRuleException */
    public function transition(Booking $booking, BookingStatus $to, ?User $actor, ?string $reason = null, ?ActorType $as = null): Booking
    {
        return DB::transaction(function () use ($booking, $to, $actor, $reason, $as) {
            $b = Booking::with('payments')->lockForUpdate()->findOrFail($booking->id);
            $from = $b->status;

            if (! $from->canGoTo($to)) {
                throw new BookingRuleException('Perubahan status tersebut tidak diizinkan.');
            }
            if ($to->needsReason() && blank($reason)) {
                throw new BookingRuleException('Alasan wajib diisi.');
            }
            if ($to === BookingStatus::NoShow && $b->start_at->isFuture()) {
                throw new BookingRuleException('Tidak datang hanya bisa ditandai setelah jadwal lewat.');
            }
            if ($from === BookingStatus::Hold && $to === BookingStatus::Pending && $b->holdExpired()) {
                throw new BookingRuleException('Waktu tahan sudah habis. Perpanjang atau lepas slot ini.');
            }
            if ($to === BookingStatus::Completed && SalonProfile::current()->require_payment_to_complete && $b->paidTotal() <= 0) {
                throw new BookingRuleException('Catat pembayaran sebelum menandai selesai.');
            }

            $b->status = $to;
            if ($to === BookingStatus::Pending) {
                $b->hold_until = null;
            }
            if ($to === BookingStatus::Confirmed) {
                $b->confirmed_by = $actor?->id;
            }
            $b->save();

            if ($to === BookingStatus::Completed && $b->customer_id) {
                Customer::whereKey($b->customer_id)->update(['last_visit_at' => now()]);
            }

            $this->log($b, $from, $to, $actor, $as ?? $this->actorType($actor), $reason);

            return $b;
        });
    }

    /** Perpanjang hold; jika sudah kedaluwarsa, cek ulang slotnya masih kosong. */
    public function extendHold(Booking $booking): Booking
    {
        return DB::transaction(function () use ($booking) {
            Staff::whereKey($booking->staff_id)->lockForUpdate()->firstOrFail();
            $b = Booking::lockForUpdate()->findOrFail($booking->id);

            if ($b->status !== BookingStatus::Hold) {
                throw new BookingRuleException('Hanya booking berstatus ditahan yang bisa diperpanjang.');
            }

            $profile = SalonProfile::current();
            $until = collect([
                now()->addMinutes($profile->hold_default_minutes),
                $b->created_at->copy()->addMinutes($profile->hold_max_minutes),
            ])->min();

            if ($until->lte(now())) {
                throw new BookingRuleException('Batas maksimal waktu tahan sudah tercapai.');
            }

            if ($b->holdExpired()) {
                $start = CarbonImmutable::instance($b->start_at);
                $ctx = $this->availability->context(Tz::day($start), collect([$b->staff_id]), $b->id);
                $duration = $b->start_at->diffInMinutes($b->end_at);
                $buffer = $b->end_at->diffInMinutes($b->blocked_until);
                if (! $this->availability->fits($ctx, $b->staff_id, $start, $duration, $buffer)) {
                    throw new SlotUnavailableException('Slot ini sudah diambil orang lain, tidak bisa diperpanjang.');
                }
            }

            $b->update(['hold_until' => $until]);

            return $b;
        });
    }

    public function cancelByCustomer(Booking $booking): Booking
    {
        $limit = SalonProfile::current()->cancel_limit_minutes;

        if (! in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)) {
            throw new BookingRuleException('Booking ini tidak bisa dibatalkan.');
        }
        if (now()->gte($booking->start_at->copy()->subMinutes($limit))) {
            throw new BookingRuleException("Pembatalan online hanya bisa dilakukan hingga {$limit} menit sebelum jadwal. Silakan hubungi kami via WhatsApp.");
        }

        return $this->transition($booking, BookingStatus::Cancelled, null, 'Dibatalkan oleh pelanggan', ActorType::Customer);
    }

    public function recordPayment(Booking $booking, User $actor, int $amount, PaymentMethod $method, PaymentKind $kind, ?string $note): Payment
    {
        return DB::transaction(function () use ($booking, $actor, $amount, $method, $kind, $note) {
            $b = Booking::with('payments')->lockForUpdate()->findOrFail($booking->id);

            if (! in_array($b->status, [BookingStatus::Confirmed, BookingStatus::Completed], true)) {
                throw new BookingRuleException('Pembayaran hanya bisa dicatat untuk booking terkonfirmasi atau selesai.');
            }

            // Koreksi selalu bernilai negatif; payments tidak pernah diubah atau dihapus.
            $signed = $kind === PaymentKind::Correction ? -abs($amount) : abs($amount);
            if ($b->paidTotal() + $signed < 0) {
                throw new BookingRuleException('Koreksi melebihi total pembayaran yang tercatat.');
            }

            return $b->payments()->create([
                'amount' => $signed,
                'method' => $method,
                'kind' => $kind,
                'paid_at' => now(),
                'recorded_by' => $actor->id,
                'note' => $note,
            ]);
        });
    }

    /** Staf aktif yang bisa mengerjakan SEMUA layanan terpilih. */
    public function candidateStaffIds(Collection $services): Collection
    {
        return Staff::active()
            ->whereHas('services', fn ($q) => $q->whereIn('services.id', $services->pluck('id')), '=', $services->count())
            ->orderBy('sort_order')->orderBy('id')
            ->pluck('id');
    }

    /** Staf yang kosong pada jam tertentu (untuk saran saat walk-in bentrok). */
    public function freeStaff(array $serviceIds, CarbonImmutable $start): Collection
    {
        $services = $this->services($serviceIds);
        $ids = $this->candidateStaffIds($services);
        $ctx = $this->availability->context(Tz::day($start), $ids);
        $free = $ids->filter(fn ($id) => $this->availability->fits($ctx, $id, $start, $services->sum('duration_minutes'), $services->sum('buffer_minutes')));

        return Staff::whereIn('id', $free)->orderBy('sort_order')->get();
    }

    /** @return Collection<int,Service> sesuai urutan id yang dikirim */
    public function services(array $ids): Collection
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $found = Service::whereIn('id', $ids)->where('is_available', true)->get()->keyBy('id');

        if ($found->count() !== count($ids) || $found->isEmpty()) {
            throw new BookingRuleException('Ada layanan yang sedang tidak tersedia.');
        }

        return collect($ids)->map(fn ($id) => $found[$id]);
    }

    private function customer(array $d): ?Customer
    {
        if (empty($d['phone'])) {
            return null;
        }

        $customer = Customer::firstOrCreate(['phone' => $d['phone']], ['name' => $d['name']]);
        if (! empty($d['consent']) && ! $customer->consent_at) {
            $customer->consent_at = now();
            $customer->save();
        }

        return $customer;
    }

    private function newCode(): string
    {
        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)];
            }
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    private function actorType(?User $actor): ActorType
    {
        return match (true) {
            $actor === null => ActorType::Customer,
            $actor->isOwner() => ActorType::Owner,
            default => ActorType::Kasir,
        };
    }

    private function log(Booking $b, ?BookingStatus $from, BookingStatus $to, ?User $actor, ActorType $as, ?string $reason): void
    {
        BookingStatusLog::create([
            'booking_id' => $b->id,
            'from_status' => $from,
            'to_status' => $to,
            'actor_type' => $as,
            'changed_by' => $actor?->id,
            'reason' => $reason,
        ]);
    }
}
