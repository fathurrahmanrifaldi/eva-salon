<x-layouts.admin title="Kalender Booking">
    <div class="space-y-4">
        <!-- Header & Navigasi Tanggal -->
        <div class="bg-white rounded-2xl border border-marble-200 p-4 sm:p-5 shadow-xs">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Navigasi Tanggal & View Switcher -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <!-- Switcher Harian / Mingguan -->
                    <div class="inline-flex rounded-xl bg-cream-100 p-1 border border-marble-200 text-xs font-semibold">
                        <a href="{{ route('admin.calendar', array_merge(request()->query(), ['view' => 'day'])) }}"
                           class="px-3 py-1.5 rounded-lg transition {{ ! $week ? 'bg-white text-sage-900 shadow-xs' : 'text-ink-600 hover:text-sage-700' }}">
                            Harian (Per Staf)
                        </a>
                        <a href="{{ route('admin.calendar', array_merge(request()->query(), ['view' => 'week'])) }}"
                           class="px-3 py-1.5 rounded-lg transition {{ $week ? 'bg-white text-sage-900 shadow-xs' : 'text-ink-600 hover:text-sage-700' }}">
                            Mingguan (Per Hari)
                        </a>
                    </div>

                    <!-- Tombol Navigasi [<] [Hari Ini] [>] -->
                    <div class="flex items-center gap-1">
                        @php
                            $prevDate = $week ? $day->subWeek()->toDateString() : $day->subDay()->toDateString();
                            $nextDate = $week ? $day->addWeek()->toDateString() : $day->addDay()->toDateString();
                            $todayDate = \App\Support\Tz::local(\App\Support\Tz::now())->toDateString();
                        @endphp
                        <a href="{{ route('admin.calendar', array_merge(request()->query(), ['date' => $prevDate])) }}"
                           class="p-2 rounded-xl border border-marble-200 text-ink-600 hover:bg-cream-100 transition"
                           title="Sebelumnya">
                            <x-icon name="arrow-left" class="w-4 h-4" />
                        </a>

                        <a href="{{ route('admin.calendar', array_merge(request()->query(), ['date' => $todayDate])) }}"
                           class="px-3 py-1.5 rounded-xl border border-marble-200 text-xs font-semibold text-ink-700 hover:bg-cream-100 transition">
                            Hari Ini
                        </a>

                        <a href="{{ route('admin.calendar', array_merge(request()->query(), ['date' => $nextDate])) }}"
                           class="p-2 rounded-xl border border-marble-200 text-ink-600 hover:bg-cream-100 transition"
                           title="Selanjutnya">
                            <x-icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    </div>

                    <form action="{{ route('admin.calendar') }}" method="GET" class="flex items-center gap-2">
                        @if ($week)
                            <input type="hidden" name="view" value="week">
                            @if ($staff)
                                <input type="hidden" name="staff" value="{{ $staff->id }}">
                            @endif
                        @endif
                        @if (request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        <input type="date" name="date" value="{{ $day->toDateString() }}" onchange="this.form.submit()"
                               class="px-3 py-1.5 rounded-xl border border-marble-200 text-xs font-semibold text-ink-800 focus:ring-2 focus:ring-sage-700">
                    </form>

                    <h1 class="font-heading text-lg sm:text-xl font-bold text-sage-900 ml-1">
                        @if ($week)
                            Pekan {{ $columns->first()['date']->translatedFormat('j M') }} &ndash; {{ $columns->last()['date']->translatedFormat('j M Y') }}
                        @else
                            {{ tanggal($day) }}
                        @endif
                    </h1>
                </div>

                <!-- Dropdown Filter & Pemilihan Staf (Mingguan) -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    @if ($week)
                        <form action="{{ route('admin.calendar') }}" method="GET" class="flex items-center gap-1.5">
                            <input type="hidden" name="view" value="week">
                            <input type="hidden" name="date" value="{{ $day->toDateString() }}">
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <label for="select-staff" class="font-medium text-ink-600">Staf:</label>
                            <select id="select-staff" name="staff" onchange="this.form.submit()"
                                    class="px-3 py-1.5 rounded-xl border border-marble-200 font-semibold focus:ring-2 focus:ring-sage-700 bg-white">
                                @foreach ($allStaff as $s)
                                    <option value="{{ $s->id }}" {{ $staff?->id === $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} ({{ $s->position ?? 'Staf' }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                    <form action="{{ route('admin.calendar') }}" method="GET" class="flex items-center gap-1.5">
                        @if ($week)
                            <input type="hidden" name="view" value="week">
                            @if ($staff)
                                <input type="hidden" name="staff" value="{{ $staff->id }}">
                            @endif
                        @endif
                        <input type="hidden" name="date" value="{{ $day->toDateString() }}">
                        <select name="status" onchange="this.form.submit()"
                                class="px-3 py-1.5 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700 bg-white">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $st)
                                <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <!-- Bar Jam Sibuk (Khusus Tampilan Harian) -->
            @if (! $week && ! empty($busy))
                <div class="mt-4 pt-4 border-t border-marble-200">
                    <div class="flex items-center justify-between text-xs text-ink-600 mb-1.5">
                        <span class="font-semibold text-ink-800 flex items-center gap-1.5">
                            <x-icon name="sparkles" class="w-3.5 h-3.5 text-gold-600" />
                            Kepadatan Slot Booking Hari Ini:
                        </span>
                        <span class="text-[11px] text-ink-500">Makin tinggi angka, makin sedikit staf yang tersisa</span>
                    </div>
                    @php
                        $startHour = (int) floor($rangeStart / 60);
                        $endHour = (int) ceil($rangeEnd / 60);
                        $maxStaff = max(1, $allStaff->count());
                    @endphp
                    <div class="grid grid-cols-6 sm:grid-cols-12 gap-1.5">
                        @for ($h = $startHour; $h < $endHour; $h++)
                            @php
                                $cnt = $busy[$h] ?? 0;
                                $pct = min(100, round(($cnt / $maxStaff) * 100));
                                $color = $cnt >= ($maxStaff * 0.7)
                                    ? 'bg-red-100 text-red-900 border-red-300'
                                    : ($cnt >= ($maxStaff * 0.3)
                                        ? 'bg-amber-100 text-amber-900 border-amber-300'
                                        : ($cnt > 0 ? 'bg-sage-100 text-sage-900 border-sage-300' : 'bg-cream-100 text-ink-500 border-marble-200'));
                            @endphp
                            <div class="p-1.5 rounded-lg border {{ $color }} text-center transition">
                                <div class="text-[10px] font-mono font-medium">{{ sprintf('%02d:00', $h) }}</div>
                                <div class="text-xs font-bold">{{ $cnt }} <span class="text-[9px] font-normal opacity-80">slot</span></div>
                            </div>
                        @endfor
                    </div>
                </div>
            @endif
        </div>

        <!-- Grid Kalender -->
        @php
            $startHour = (int) floor($rangeStart / 60);
            $endHour = (int) ceil($rangeEnd / 60);
            $totalMinutes = ($endHour * 60) - ($startHour * 60);
            $totalHeight = $totalMinutes * $ppm;
        @endphp

        <div class="bg-white rounded-2xl border border-marble-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <div class="min-w-[800px]">
                    <!-- Header Kolom -->
                    <div class="flex border-b border-marble-200 bg-cream-50 sticky top-0 z-20">
                        <!-- Sudut Kiri (Waktu) -->
                        <div class="w-16 shrink-0 p-3 text-center border-r border-marble-200 text-xs font-bold text-ink-500 uppercase">
                            Waktu
                        </div>

                        <!-- Kolom Header -->
                        <div class="flex-1 flex">
                            @foreach ($columns as $col)
                                <div class="flex-1 min-w-[160px] p-3 text-center border-r border-marble-200 last:border-r-0">
                                    <div class="font-heading font-bold text-sm text-sage-900 truncate">
                                        {{ $col['label'] }}
                                    </div>
                                    @if ($week)
                                        <div class="text-[11px] text-ink-500 font-medium">
                                            {{ $col['date']->translatedFormat('l') }}
                                        </div>
                                    @else
                                        @php
                                            $stf = $allStaff->firstWhere('id', $col['staff_id']);
                                        @endphp
                                        <div class="text-[11px] text-ink-500 font-medium truncate">
                                            {{ $stf?->position ?? 'Staf' }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Body Kalender (Sumbu Waktu & Jalur Kolom) -->
                    <div class="flex relative" style="height: {{ $totalHeight }}px;">
                        <!-- Sumbu Jam di Kiri -->
                        <div class="w-16 shrink-0 border-r border-marble-200 relative bg-cream-50/40 select-none">
                            @for ($h = $startHour; $h < $endHour; $h++)
                                @php
                                    $topPos = ($h * 60 - ($startHour * 60)) * $ppm;
                                @endphp
                                <div class="absolute w-full text-right pr-2 text-[11px] font-mono font-medium text-ink-500 -mt-2.5"
                                     style="top: {{ $topPos }}px;">
                                    {{ sprintf('%02d:00', $h) }}
                                </div>
                            @endfor
                        </div>

                        <!-- Garis Horizontal Tiap Jam -->
                        <div class="absolute inset-0 pointer-events-none left-16">
                            @for ($h = $startHour; $h < $endHour; $h++)
                                @php
                                    $topPos = ($h * 60 - ($startHour * 60)) * $ppm;
                                @endphp
                                <div class="absolute w-full border-b border-marble-200" style="top: {{ $topPos }}px;"></div>
                            @endfor
                        </div>

                        <!-- Area Kolom-Kolom -->
                        <div class="flex-1 flex relative">
                            @foreach ($columns as $col)
                                <div class="flex-1 min-w-[160px] border-r border-marble-200 last:border-r-0 relative">
                                    <!-- Daftar Blok Booking pada Kolom Ini -->
                                    @foreach ($col['blocks'] as $blk)
                                        @php
                                            $b = $blk['booking'];
                                            $status = $b->status;
                                            $statusClass = match ($status) {
                                                \App\Enums\BookingStatus::Pending => 'bg-amber-50 border-amber-300 text-amber-950 hover:bg-amber-100',
                                                \App\Enums\BookingStatus::Confirmed => 'bg-sage-50 border-sage-400 text-sage-950 hover:bg-sage-100',
                                                \App\Enums\BookingStatus::Hold => 'bg-purple-50 border-purple-300 text-purple-950 hover:bg-purple-100',
                                                \App\Enums\BookingStatus::Completed => 'bg-emerald-50 border-emerald-300 text-emerald-950 hover:bg-emerald-100',
                                                \App\Enums\BookingStatus::Cancelled, \App\Enums\BookingStatus::NoShow => 'bg-rose-50 border-rose-200 text-rose-900 opacity-60',
                                            };
                                            // Koreksi posisi top terhadap startHour yang digambar
                                            $offsetMinutes = ($startHour * 60) - $rangeStart;
                                            $actualTop = $blk['top'] - ($offsetMinutes * $ppm);
                                        @endphp

                                        <!-- Blok Booking -->
                                        <a href="{{ route('admin.bookings.show', $b) }}"
                                           class="absolute left-1 right-1 rounded-xl border p-2 text-xs shadow-xs transition hover:shadow-md hover:z-30 overflow-hidden flex flex-col justify-between {{ $statusClass }}"
                                           style="top: {{ $actualTop }}px; height: {{ $blk['height'] }}px;"
                                           title="{{ $b->booking_code }} - {{ $b->customer_name }} ({{ $status->label() }})">
                                            <div>
                                                <div class="flex items-center justify-between gap-1 leading-tight">
                                                    <span class="font-bold truncate text-[11px]">{{ $b->customer_name }}</span>
                                                    <span class="font-mono text-[10px] font-semibold opacity-75 shrink-0">{{ jam($b->start_at) }}</span>
                                                </div>
                                                <div class="text-[10px] text-ink-600 truncate mt-0.5">
                                                    {{ $b->items->pluck('service_name')->join(', ') }}
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between gap-1 mt-1 text-[9px]">
                                                <span class="font-semibold uppercase px-1.5 py-0.5 rounded {{ $status->badgeClass() }}">
                                                    {{ $status->label() }}
                                                </span>
                                                <span class="font-mono font-medium opacity-75">
                                                    {{ $b->start_at->diffInMinutes($b->end_at) }}m
                                                </span>
                                            </div>
                                        </a>

                                        <!-- Blok Garis Buffer Waktu Istirahat/Persiapan -->
                                        @if ($blk['buffer'] > 0)
                                            <div class="absolute left-1 right-1 rounded-lg border border-dashed border-marble-300 flex items-center justify-center text-[9px] font-mono text-ink-400 select-none overflow-hidden"
                                                 style="top: {{ $actualTop + $blk['height'] }}px; height: {{ $blk['buffer'] }}px; background-image: repeating-linear-gradient(45deg, #f3f4f6, #f3f4f6 5px, #ffffff 5px, #ffffff 10px);"
                                                 title="Buffer persiapan/istirahat {{ $b->end_at->diffInMinutes($b->blocked_until) }} menit">
                                                <span>buffer</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
