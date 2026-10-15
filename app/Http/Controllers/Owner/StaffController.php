<?php

namespace App\Http\Controllers\Owner;

use App\Enums\ExceptionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExceptionRequest;
use App\Http\Requests\StaffRequest;
use App\Models\BusinessHour;
use App\Models\ScheduleException;
use App\Models\Service;
use App\Models\Staff;
use App\Services\ImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** Kasir hanya melihat; owner mengelola staf, jadwal mingguan, dan cuti/libur (SF-01..04). */
class StaffController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Staff::class);

        return view('owner.staff.index', [
            'staff' => Staff::with(['schedules', 'services'])->orderBy('sort_order')->orderBy('name')->get(),
            'exceptions' => ScheduleException::with('staff')
                ->whereDate('ends_on', '>=', now()->subDay())->orderBy('starts_on')->get(),
            'types' => ExceptionType::cases(),
            'activeStaff' => Staff::active()->orderBy('sort_order')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Staff::class);

        return $this->form(new Staff(['is_active' => true]));
    }

    public function edit(Staff $staff): View
    {
        Gate::authorize('update', $staff);

        return $this->form($staff->load(['schedules', 'services']));
    }

    public function store(StaffRequest $request): RedirectResponse
    {
        Gate::authorize('create', Staff::class);

        $staff = DB::transaction(function () use ($request) {
            $staff = Staff::create($this->data($request));
            $this->sync($staff, $request);

            return $staff;
        });

        return redirect()->route('owner.staff.index')->with('success', "Staf \"{$staff->name}\" ditambahkan.");
    }

    public function update(StaffRequest $request, Staff $staff): RedirectResponse
    {
        Gate::authorize('update', $staff);

        DB::transaction(function () use ($request, $staff) {
            $data = $this->data($request);
            if (isset($data['photo_path'])) {
                $this->images->delete($staff->photo_path);
            }
            $staff->update($data);
            $this->sync($staff, $request);
        });

        return redirect()->route('owner.staff.index')->with('success', "Staf \"{$staff->name}\" diperbarui.");
    }

    /** Nonaktifkan sebagai pengganti hapus (SF-01). */
    public function toggle(Staff $staff): RedirectResponse
    {
        Gate::authorize('update', $staff);

        $staff->update(['is_active' => ! $staff->is_active]);

        return back()->with('success', $staff->is_active ? 'Staf diaktifkan.' : 'Staf dinonaktifkan.');
    }

    public function storeException(ExceptionRequest $request): RedirectResponse
    {
        Gate::authorize('create', ScheduleException::class);

        $data = $request->validated();
        // salon_closed berlaku untuk seluruh salon, jadi tanpa staf.
        if ($data['type'] === ExceptionType::SalonClosed->value) {
            $data['staff_id'] = null;
        }
        ScheduleException::create($data + ['created_by' => $request->user()->id]);

        return back()->with('success', 'Cuti/libur ditambahkan.');
    }

    public function destroyException(ScheduleException $exception): RedirectResponse
    {
        Gate::authorize('delete', $exception);

        $exception->delete();

        return back()->with('success', 'Cuti/libur dihapus.');
    }

    private function form(Staff $staff): View
    {
        return view('owner.staff.form', [
            'staff' => $staff,
            'services' => Service::with('category')->orderBy('category_id')->orderBy('sort_order')->get(),
            'assigned' => $staff->exists ? $staff->services->pluck('id')->all() : [],
            'schedule' => $this->scheduleGrid($staff),
            'days' => BusinessHour::DAYS,
        ]);
    }

    /** @return array<int,array<int,array{start:?string,end:?string}>> hari => 2 slot shift */
    private function scheduleGrid(Staff $staff): array
    {
        $grid = [];
        foreach (range(0, 6) as $d) {
            $rows = $staff->exists ? $staff->schedules->where('day_of_week', $d)->values() : collect();
            foreach ([0, 1] as $i) {
                $grid[$d][$i] = [
                    'start' => isset($rows[$i]) ? substr($rows[$i]->start_time, 0, 5) : null,
                    'end' => isset($rows[$i]) ? substr($rows[$i]->end_time, 0, 5) : null,
                ];
            }
        }

        return $grid;
    }

    private function data(StaffRequest $request): array
    {
        $data = $request->safe()->only(['name', 'position', 'gender', 'phone', 'sort_order']);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $this->images->store($request->file('photo'), 'staff');
        }

        return $data;
    }

    private function sync(Staff $staff, StaffRequest $request): void
    {
        $staff->services()->sync($request->service_ids ?? []);

        // Jadwal diganti utuh: staff_schedules bukan data riwayat.
        $staff->schedules()->delete();
        foreach ((array) $request->input('schedule', []) as $day => $shifts) {
            foreach ($shifts as $shift) {
                if (filled($shift['start'] ?? null) && filled($shift['end'] ?? null)) {
                    $staff->schedules()->create(['day_of_week' => (int) $day, 'start_time' => $shift['start'], 'end_time' => $shift['end']]);
                }
            }
        }
    }
}
