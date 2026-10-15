<x-layouts.admin title="Daftar Booking Hari Ini">
    <div class="space-y-6">
        <!-- Header & Filter Bar -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="font-heading text-2xl font-bold text-sage-900">
                        {{ $pendingScope ? 'Antrean Perlu Konfirmasi' : 'Daftar Booking: ' . tanggal($day) }}
                    </h1>
                    @if ($pendingScope)
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-bold text-xs">
                            {{ $bookings->count() }} antrean
                        </span>
                    @endif
                </div>
                <p class="text-xs text-ink-600 mt-0.5">
                    {{ $pendingScope ? 'Booking online yang masuk dan memerlukan validasi kasir' : 'Diurutkan berdasarkan jam mulai layanan' }}
                </p>
            </div>

            <!-- Form Filter -->
            <form action="{{ route('admin.today') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
                @if ($pendingScope)
                    <input type="hidden" name="scope" value="pending">
                @else
                    <input type="date" name="date" value="{{ $day->toDateString() }}" onchange="this.form.submit()"
                           class="px-3 py-1.5 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700">

                    <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <select name="staff" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700">
                    <option value="">Semua Staf</option>
                    @foreach ($staffFilter as $stf)
                        <option value="{{ $stf->id }}" {{ (string)request('staff') === (string)$stf->id ? 'selected' : '' }}>
                            {{ $stf->name }}
                        </option>
                    @endforeach
                </select>

                @if ($pendingScope)
                    <a href="{{ route('admin.today') }}" class="px-3 py-1.5 rounded-xl border border-marble-200 text-ink-600 hover:bg-cream-100 font-semibold">
                        Lihat Jadwal Hari Ini
                    </a>
                @else
                    <a href="{{ route('admin.today', ['scope' => 'pending']) }}" class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-900 border border-amber-300 font-semibold hover:bg-amber-200">
                        Hanya Perlu Konfirmasi
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Daftar Booking -->
        <div class="bg-white rounded-2xl border border-marble-200 overflow-hidden shadow-xs">
            @if ($bookings->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-sage-50 text-sage-700 flex items-center justify-center">
                        <x-icon name="calendar" class="w-6 h-6" />
                    </div>
                    <p class="font-heading text-xl font-bold text-sage-900">Tidak ada booking</p>
                    <p class="text-xs text-ink-600 mt-1 max-w-sm mx-auto">
                        {{ $pendingScope ? 'Tidak ada booking yang menunggu konfirmasi saat ini.' : 'Belum ada jadwal booking yang tercatat untuk filter ini.' }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs divide-y divide-marble-200">
                        <thead class="bg-cream-50 text-ink-600 uppercase font-semibold text-[11px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Jam</th>
                                <th class="px-4 py-3">Kode / Tamu</th>
                                <th class="px-4 py-3">Layanan</th>
                                <th class="px-4 py-3">Staf</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Total Biaya</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marble-200">
                            @foreach ($bookings as $b)
                                <tr class="hover:bg-cream-50/60 transition">
                                    <!-- Jam -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="font-bold text-sage-900">{{ jam($b->start_at) }} – {{ jam($b->end_at) }}</div>
                                        <div class="text-[10px] text-ink-600">{{ tanggal($b->start_at, 'D, j M') }}</div>
                                    </td>

                                    <!-- Pelanggan & Kode -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('admin.bookings.show', $b) }}" class="font-bold text-sage-900 hover:text-sage-700 underline text-sm">
                                                {{ $b->customer_name }}
                                            </a>
                                            <span class="text-[10px] uppercase font-mono px-1.5 py-0.2 rounded bg-sage-100 text-sage-700">
                                                {{ $b->source->label() }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] font-mono text-ink-600">
                                            #{{ $b->booking_code }}
                                            @if ($b->customer_phone)
                                                &bull; {{ $b->customer_phone }}
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Layanan -->
                                    <td class="px-4 py-3.5 max-w-xs">
                                        <div class="font-medium text-ink-900 truncate">
                                            {{ $b->items->pluck('service_name')->join(', ') }}
                                        </div>
                                        <div class="text-[11px] text-ink-600">
                                            Durasi: {{ $b->total_duration }} mnt
                                        </div>
                                    </td>

                                    <!-- Staf -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="font-semibold text-ink-900">{{ $b->staff->name }}</span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="{{ $b->status->badge() }}">
                                            <x-icon :name="$b->status->icon()" class="w-3.5 h-3.5" />
                                            <span>{{ $b->status->label() }}</span>
                                        </span>
                                        @if ($b->status === \App\Enums\BookingStatus::Hold)
                                            <div class="text-[10px] text-ink-600 mt-1">
                                                s/d {{ jam($b->hold_until) }} WIB
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Total Biaya & Pembayaran -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="font-bold text-sage-900">{{ rupiah($b->total_price) }}</div>
                                        @if ($b->paidTotal() > 0)
                                            <div class="text-[10px] text-green-700 font-semibold">
                                                Terbayar {{ rupiah($b->paidTotal()) }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Tombol Aksi Cepat -->
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <!-- Tombol WhatsApp Cepat (PRD ST-06) -->
                                            @php
                                                $waCustomer = \App\Support\Wa::toCustomer($b);
                                            @endphp
                                            @if ($waCustomer)
                                                <a href="{{ $waCustomer }}" target="_blank" rel="noopener noreferrer"
                                                   class="p-1.5 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition"
                                                   title="Kirim pesan WhatsApp ke pelanggan">
                                                    <x-icon name="whatsapp" class="w-4 h-4 text-[#25D366]" />
                                                </a>
                                            @endif

                                            @if ($b->status === \App\Enums\BookingStatus::Pending)
                                                <form action="{{ route('admin.bookings.confirm', $b) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-sage-700 hover:bg-sage-900 text-white font-bold text-xs transition" title="Konfirmasi booking">
                                                        Konfirmasi
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('admin.bookings.show', $b) }}" class="px-2.5 py-1 rounded-lg border border-marble-200 text-ink-600 hover:bg-white hover:text-sage-700 font-semibold text-xs transition">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
