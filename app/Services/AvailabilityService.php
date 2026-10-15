<?php

namespace App\Services;

use App\Enums\ExceptionType;
use App\Models\Booking;
use App\Models\BusinessHour;
use App\Models\ScheduleException;
use App\Models\StaffSchedule;
use App\Support\Tz;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Aturan ketersediaan slot (agent.md 6.2, kondisi 1-3). Dipakai oleh daftar slot publik
 * DAN oleh BookingService::create() di dalam transaksi, jadi hanya ada satu definisi "slot tersedia".
 */
class AvailabilityService
{
    /** Rumus overlap: existing.start < new.blocked AND existing.blocked > new.start. */
    public static function overlaps(CarbonInterface $aStart, CarbonInterface $aEnd, CarbonInterface $bStart, CarbonInterface $bEnd): bool
    {
        return $bStart < $aEnd && $bEnd > $aStart;
    }

    /**
     * Muat semua data satu hari lokal untuk beberapa staf (query dikelompokkan, bukan per slot).
     *
     * @param  Collection<int,int>  $staffIds
     */
    public function context(CarbonImmutable $localDay, Collection $staffIds, ?int $exceptBookingId = null): array
    {
        $dow = $localDay->dayOfWeek; // 0 = Minggu, sama dengan business_hours
        $date = $localDay->toDateString();
        $hours = BusinessHour::where('day_of_week', $dow)->first();

        $closed = ! $hours || $hours->is_closed || ! $hours->open_time || ! $hours->close_time
            || ScheduleException::where('type', ExceptionType::SalonClosed->value)
                ->whereDate('starts_on', '<=', $date)->whereDate('ends_on', '>=', $date)->exists();

        $ctx = [
            'closed' => $closed,
            'open' => $closed ? null : Tz::at($localDay, $hours->open_time),
            'close' => $closed ? null : Tz::at($localDay, $hours->close_time),
            'staff' => [],
        ];

        if ($closed || $staffIds->isEmpty()) {
            return $ctx;
        }

        $dayStart = $localDay->utc();
        $dayEnd = $localDay->addDay()->utc();

        $shifts = StaffSchedule::whereIn('staff_id', $staffIds)->where('day_of_week', $dow)->get()->groupBy('staff_id');
        $onLeave = ScheduleException::where('type', ExceptionType::StaffLeave->value)
            ->whereIn('staff_id', $staffIds)
            ->whereDate('starts_on', '<=', $date)->whereDate('ends_on', '>=', $date)
            ->pluck('staff_id')->flip();
        $busy = Booking::blocking()
            ->whereIn('staff_id', $staffIds)
            ->where('start_at', '<', $dayEnd)->where('blocked_until', '>', $dayStart)
            ->when($exceptBookingId, fn ($q) => $q->where('id', '!=', $exceptBookingId))
            ->get(['staff_id', 'start_at', 'blocked_until'])->groupBy('staff_id');

        foreach ($staffIds as $id) {
            $ctx['staff'][$id] = [
                'leave' => $onLeave->has($id),
                'shifts' => ($shifts[$id] ?? collect())
                    ->map(fn ($s) => [Tz::at($localDay, $s->start_time), Tz::at($localDay, $s->end_time)])->all(),
                'busy' => ($busy[$id] ?? collect())
                    ->map(fn ($b) => [$b->start_at, $b->blocked_until])->all(),
            ];
        }

        return $ctx;
    }

    /** Apakah staf bisa mengerjakan rentang [start, start+durasi+buffer) pada konteks ini. */
    public function fits(array $ctx, int $staffId, CarbonImmutable $start, int $durationMinutes, int $bufferMinutes): bool
    {
        $staff = $ctx['staff'][$staffId] ?? null;
        if ($ctx['closed'] || ! $staff || $staff['leave']) {
            return false;
        }

        $end = $start->addMinutes($durationMinutes);
        $blockedUntil = $end->addMinutes($bufferMinutes);

        // Seluruh blok (termasuk buffer) harus selesai sebelum salon tutup.
        if ($start < $ctx['open'] || $blockedUntil > $ctx['close']) {
            return false;
        }

        $inShift = false;
        foreach ($staff['shifts'] as [$from, $to]) {
            if ($start >= $from && $end <= $to) {
                $inShift = true;
                break;
            }
        }
        if (! $inShift) {
            return false;
        }

        foreach ($staff['busy'] as [$busyStart, $busyEnd]) {
            if (self::overlaps($start, $blockedUntil, $busyStart, $busyEnd)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Slot per jam mulai.
     *
     * @param  Collection<int,int>  $staffIds
     * @return array<string,list<int>> 'H:i' (lokal) => id staf yang kosong
     */
    public function slots(CarbonImmutable $localDay, Collection $staffIds, int $durationMinutes, int $bufferMinutes): array
    {
        $ctx = $this->context($localDay, $staffIds);
        if ($ctx['closed']) {
            return [];
        }

        $now = Tz::now();
        $step = config('salon.slot_step');
        $out = [];

        for ($t = $ctx['open']; $t->addMinutes($durationMinutes + $bufferMinutes) <= $ctx['close']; $t = $t->addMinutes($step)) {
            if ($t <= $now) {
                continue;
            }
            foreach ($staffIds as $id) {
                if ($this->fits($ctx, $id, $t, $durationMinutes, $bufferMinutes)) {
                    $out[Tz::local($t)->format('H:i')][] = $id;
                }
            }
        }

        return $out;
    }
}
