<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BusinessHour;
use App\Models\Staff;
use App\Support\Tz;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Kalender harian (kolom per staf) dan mingguan (kolom per hari, satu staf). */
class CalendarController extends Controller
{
    public const PX_PER_MINUTE = 1.1;

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Booking::class);

        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'view' => ['nullable', 'in:day,week'],
            'staff' => ['nullable', 'integer'],
        ]);

        $day = Tz::day($request->query('date', Tz::local(Tz::now())->toDateString()));
        $week = $request->query('view') === 'week';
        $allStaff = Staff::active()->orderBy('sort_order')->get();

        if ($week) {
            $staff = $allStaff->firstWhere('id', (int) $request->query('staff')) ?? $allStaff->first();
            $from = $day->startOfWeek(); // Senin
            $to = $from->addDays(7);
            $days = collect(range(0, 6))->map(fn ($i) => $from->addDays($i));
            $columns = $days->map(fn ($d) => ['label' => $d->translatedFormat('D j M'), 'key' => $d->toDateString(), 'date' => $d, 'staff_id' => $staff?->id]);
            $hours = BusinessHour::whereIn('day_of_week', $days->map->dayOfWeek)->where('is_closed', false)->get();
        } else {
            $staff = null;
            $from = $day;
            $to = $day->addDay();
            $columns = $allStaff->map(fn ($s) => ['label' => $s->name, 'key' => $s->id, 'date' => $day, 'staff_id' => $s->id]);
            $hours = BusinessHour::where('day_of_week', $day->dayOfWeek)->where('is_closed', false)->get();
        }

        // Rentang jam yang digambar (menit sejak 00:00 lokal).
        $rangeStart = $hours->isEmpty() ? 540 : $hours->map(fn ($h) => $this->minutes($h->open_time))->min();
        $rangeEnd = $hours->isEmpty() ? 1230 : $hours->map(fn ($h) => $this->minutes($h->close_time))->max();

        $bookings = Booking::with(['items', 'staff'])
            ->where('start_at', '>=', $from->utc())->where('start_at', '<', $to->utc())
            ->where('status', '!=', BookingStatus::Cancelled->value)
            ->when($week, fn ($q) => $q->where('staff_id', $staff?->id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->get();

        $columns = $columns->map(function ($col) use ($bookings, $week, $rangeStart) {
            $col['blocks'] = $bookings
                ->filter(fn ($b) => $week
                    ? Tz::local($b->start_at)->toDateString() === $col['key']
                    : $b->staff_id === $col['staff_id'])
                ->map(fn ($b) => $this->block($b, $rangeStart))->values();

            return $col;
        });

        return view('admin.calendar', [
            'day' => $day,
            'week' => $week,
            'staff' => $staff,
            'allStaff' => $allStaff,
            'columns' => $columns,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'ppm' => self::PX_PER_MINUTE,
            'busy' => $week ? [] : $this->busyPerHour($bookings),
            'statuses' => BookingStatus::cases(),
        ]);
    }

    private function minutes(string $time): int
    {
        [$h, $m] = array_map('intval', explode(':', $time));

        return $h * 60 + $m;
    }

    private function block(Booking $b, int $rangeStart): array
    {
        $start = Tz::local($b->start_at);

        return [
            'booking' => $b,
            'top' => max(0, ($start->hour * 60 + $start->minute - $rangeStart)) * self::PX_PER_MINUTE,
            'height' => max(24, $b->start_at->diffInMinutes($b->end_at) * self::PX_PER_MINUTE),
            'buffer' => $b->end_at->diffInMinutes($b->blocked_until) * self::PX_PER_MINUTE,
        ];
    }

    /** Booking per jam mulai (hanya yang mengunci slot), untuk indikator jam sibuk. */
    private function busyPerHour($bookings): array
    {
        return $bookings
            ->filter(fn ($b) => in_array($b->status, BookingStatus::blocking(), true) || ($b->status === BookingStatus::Hold && ! $b->holdExpired()))
            ->countBy(fn ($b) => Tz::local($b->start_at)->hour)
            ->all();
    }
}
