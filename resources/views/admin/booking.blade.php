<x-layouts.admin title="Detail Booking {{ $booking->booking_code }}">
    <div class="space-y-6">
        <!-- Top Navigation / Breadcrumb & Status Header -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.today') }}" class="text-xs font-semibold text-ink-500 hover:text-sage-700 flex items-center gap-1">
                            <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                            Kembali ke Hari Ini
                        </a>
                        <span class="text-marble-300">&bull;</span>
                        <a href="{{ route('admin.calendar') }}" class="text-xs font-semibold text-ink-500 hover:text-sage-700">
                            Kalender
                        </a>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        <h1 class="font-heading text-2xl font-bold text-sage-900 font-mono tracking-tight">
                            {{ $booking->booking_code }}
                        </h1>
                        <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase {{ $booking->status->badgeClass() }}">
                            {{ $booking->status->label() }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-cream-100 text-ink-700 border border-marble-200">
                            Sumber: {{ $booking->source->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-ink-500 mt-1">
                        Dibuat pada {{ $booking->created_at->translatedFormat('l, j F Y H:i') }} WIB
                    </p>
                </div>

                <!-- Tombol Transisi Status -->
                <div class="flex flex-wrap items-center gap-2">
                    @if ($booking->status === \App\Enums\BookingStatus::Hold)
                        @if (! $booking->holdExpired())
                            <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs">
                                    Konfirmasi Jadi Pending
                                </button>
                            </form>
                            <form action="{{ route('admin.bookings.extend', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-2 rounded-xl border border-marble-300 hover:bg-cream-100 text-ink-800 text-xs font-semibold transition"
                                        title="Tambah 15 menit waktu tahan">
                                    +15 Menit Tahan
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.bookings.release', $booking) }}" method="POST" onsubmit="return confirm('Lepas slot ini sekarang?')">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold transition">
                                Lepas Slot
                            </button>
                        </form>
                    @elseif ($booking->status === \App\Enums\BookingStatus::Pending)
                        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                                <x-icon name="check" class="w-4 h-4" />
                                Konfirmasi Booking
                            </button>
                        </form>
                        <button type="button" onclick="openCancelModal()"
                                class="px-3 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold transition">
                            Tolak / Batalkan
                        </button>
                    @elseif ($booking->status === \App\Enums\BookingStatus::Confirmed)
                        <form action="{{ route('admin.bookings.transition', [$booking, 'completed']) }}" method="POST"
                              onsubmit="return confirm('Tandai booking ini selesai?')">
                            @csrf
                            <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                                <x-icon name="check" class="w-4 h-4" />
                                Layanan Selesai
                            </button>
                        </form>
                        <button type="button" onclick="openNoShowModal()"
                                class="px-3 py-2 rounded-xl border border-amber-300 text-amber-800 hover:bg-amber-50 text-xs font-semibold transition">
                            No-Show
                        </button>
                        <button type="button" onclick="openCancelModal()"
                                class="px-3 py-2 rounded-xl border border-red-200 text-red-700 hover:bg-red-50 text-xs font-semibold transition">
                            Batalkan
                        </button>
                    @endif
                </div>
            </div>

            <!-- Notice Hold Expired -->
            @if ($booking->status === \App\Enums\BookingStatus::Hold)
                <div class="mt-4 p-3 rounded-xl {{ $booking->holdExpired() ? 'bg-red-50 border border-red-200 text-red-900' : 'bg-purple-50 border border-purple-200 text-purple-900' }} text-xs flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-icon name="clock" class="w-4 h-4" />
                        @if ($booking->holdExpired())
                            <span>Batas waktu tahan telah <strong>KADALUARSA</strong> pada {{ jam($booking->hold_until) }}. Slot ini sudah bebas dipakai yang lain.</span>
                        @else
                            <span>Slot ini ditahan hingga <strong>{{ jam($booking->hold_until) }}</strong> (tersisa {{ now()->diffInMinutes($booking->hold_until, false) }} menit).</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Grid 2 Kolom Konten -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Rincian Layanan, Jadwal, dan Pembayaran (2 Kolom) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Rincian Layanan & Staf -->
                <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                    <h2 class="font-heading text-lg font-bold text-sage-900 mb-4">Layanan & Jadwal</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-cream-50/70 border border-marble-200 mb-5 text-xs">
                        <div>
                            <span class="text-ink-500 block mb-0.5">Tanggal & Waktu Layanan:</span>
                            <span class="font-bold text-ink-900 text-sm block">{{ tanggal($booking->start_at) }}</span>
                            <span class="font-mono text-sage-800 font-semibold mt-0.5 block">
                                {{ jam($booking->start_at) }} &ndash; {{ jam($booking->end_at) }}
                                <span class="text-ink-400 font-normal">({{ $booking->start_at->diffInMinutes($booking->end_at) }} menit)</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-ink-500 block mb-0.5">Staf yang Melayani:</span>
                            <div class="flex items-center gap-2.5 mt-1">
                                <div class="w-8 h-8 rounded-full bg-sage-200 text-sage-800 font-bold flex items-center justify-center text-xs overflow-hidden">
                                    @if ($booking->staff?->photo_path)
                                        <img src="{{ asset($booking->staff->photo_path) }}" alt="{{ $booking->staff->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($booking->staff?->name ?? '?', 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold text-ink-900 block">{{ $booking->staff?->name ?? 'Belum Ditentukan' }}</span>
                                    <span class="text-[11px] text-ink-500">{{ $booking->staff?->position ?? 'Staf' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Layanan -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-cream-100 text-ink-700 font-bold uppercase tracking-wider text-[10px] border-b border-marble-200">
                                <tr>
                                    <th class="p-3">Layanan</th>
                                    <th class="p-3 text-center">Durasi</th>
                                    <th class="p-3 text-right">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-marble-200">
                                @foreach ($booking->items as $item)
                                    <tr>
                                        <td class="p-3 font-semibold text-ink-900">
                                            {{ $item->service_name }}
                                        </td>
                                        <td class="p-3 text-center font-mono text-ink-600">
                                            {{ $item->duration_minutes }}m
                                        </td>
                                        <td class="p-3 text-right font-mono font-medium text-ink-900">
                                            {{ rupiah($item->price) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-marble-300 font-semibold bg-cream-50/50">
                                <tr>
                                    <td colspan="2" class="p-3 text-right text-ink-600">Total Biaya:</td>
                                    <td class="p-3 text-right font-mono text-base font-bold text-sage-900">
                                        {{ rupiah($booking->total_price) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($booking->notes)
                        <div class="mt-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                            <span class="font-bold text-amber-900 block mb-0.5">Catatan Khusus Pelanggan:</span>
                            <p class="text-amber-800">{{ $booking->notes }}</p>
                        </div>
                    @endif
                </div>

                <!-- Bagian Pembayaran -->
                <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-heading text-lg font-bold text-sage-900">Status Pembayaran</h2>
                        @if ($booking->isPaid())
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-bold">
                                LUNAS
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold">
                                BELUM LUNAS
                            </span>
                        @endif
                    </div>

                    <!-- Indikator 3 Kartu: Total, Terbayar, Sisa -->
                    <div class="grid grid-cols-3 gap-3 mb-5">
                        <div class="p-3 rounded-xl bg-cream-50 border border-marble-200">
                            <span class="text-[11px] text-ink-500 block">Total Tagihan</span>
                            <span class="text-sm sm:text-base font-mono font-bold text-ink-900">{{ rupiah($booking->total_price) }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                            <span class="text-[11px] text-emerald-700 block">Sudah Dibayar</span>
                            <span class="text-sm sm:text-base font-mono font-bold text-emerald-900">{{ rupiah($booking->paidTotal()) }}</span>
                        </div>
                        <div class="p-3 rounded-xl {{ $booking->remaining() > 0 ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-cream-50 border-marble-200 text-ink-400' }}">
                            <span class="text-[11px] block opacity-80">Sisa Tagihan</span>
                            <span class="text-sm sm:text-base font-mono font-bold">{{ rupiah($booking->remaining()) }}</span>
                        </div>
                    </div>

                    <!-- Form Catat Pembayaran Baru (Jika Belum Lunas & Status Masih Aktif) -->
                    @if ($booking->remaining() > 0 && ! in_array($booking->status, [\App\Enums\BookingStatus::Cancelled, \App\Enums\BookingStatus::NoShow]))
                        <div class="p-4 rounded-xl bg-cream-100/60 border border-marble-200 mb-5">
                            <h3 class="font-bold text-xs text-ink-800 uppercase tracking-wider mb-3">Catat Pembayaran Masuk</h3>
                            <form action="{{ route('admin.bookings.pay', $booking) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                                @csrf
                                <div>
                                    <label class="block font-medium text-ink-700 mb-1">Jumlah (Rp)</label>
                                    <input type="number" name="amount" value="{{ $booking->remaining() }}" min="1" max="{{ $booking->remaining() }}" required
                                           class="w-full px-3 py-2 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white">
                                </div>
                                <div>
                                    <label class="block font-medium text-ink-700 mb-1">Metode</label>
                                    <select name="method" required class="w-full px-3 py-2 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700 bg-white">
                                        @foreach ($methods as $m)
                                            <option value="{{ $m->value }}">{{ $m->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-medium text-ink-700 mb-1">Jenis</label>
                                    <select name="kind" required class="w-full px-3 py-2 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700 bg-white">
                                        @foreach ($kinds as $k)
                                            <option value="{{ $k->value }}">{{ $k->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-sage-700 hover:bg-sage-900 text-white font-bold transition shadow-xs">
                                        Simpan
                                    </button>
                                </div>
                                <div class="sm:col-span-4">
                                    <input type="text" name="note" placeholder="Catatan pembayaran / no. referensi QRIS / transfer (opsional)"
                                           class="w-full px-3 py-1.5 rounded-xl border border-marble-200 text-xs focus:ring-2 focus:ring-sage-700 bg-white">
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Tabel Riwayat Pembayaran -->
                    <h3 class="font-bold text-xs text-ink-700 uppercase tracking-wider mb-2">Riwayat Pembayaran</h3>
                    @if ($booking->payments->isEmpty())
                        <p class="text-xs text-ink-500 italic p-3 bg-cream-50 rounded-xl text-center">Belum ada pembayaran yang dicatat.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-cream-100 text-ink-600 font-bold uppercase text-[10px] border-b border-marble-200">
                                    <tr>
                                        <th class="p-2.5">Waktu</th>
                                        <th class="p-2.5">Jenis</th>
                                        <th class="p-2.5">Metode</th>
                                        <th class="p-2.5 text-right">Jumlah</th>
                                        <th class="p-2.5">Dicatat Oleh</th>
                                        <th class="p-2.5">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-marble-200">
                                    @foreach ($booking->payments as $p)
                                        <tr>
                                            <td class="p-2.5 font-mono text-ink-600">{{ $p->created_at->translatedFormat('d M H:i') }}</td>
                                            <td class="p-2.5"><span class="px-2 py-0.5 rounded-full bg-cream-200 text-ink-800 text-[10px] font-semibold">{{ $p->kind->label() }}</span></td>
                                            <td class="p-2.5 font-semibold text-ink-800">{{ $p->method->label() }}</td>
                                            <td class="p-2.5 text-right font-mono font-bold text-emerald-800">{{ rupiah($p->amount) }}</td>
                                            <td class="p-2.5 text-ink-600">{{ $p->recorder?->name ?? 'Sistem' }}</td>
                                            <td class="p-2.5 text-ink-500 text-[11px]">{{ $p->note ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Info Pelanggan & Timeline Audit (1 Kolom) -->
            <div class="space-y-6">
                <!-- Info Pelanggan -->
                <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                    <h2 class="font-heading text-lg font-bold text-sage-900 mb-3">Data Pelanggan</h2>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-ink-500 block mb-0.5">Nama:</span>
                            <span class="font-bold text-ink-900 text-sm">{{ $booking->customer_name }}</span>
                        </div>

                        <div>
                            <span class="text-ink-500 block mb-0.5">Nomor WhatsApp:</span>
                            @if ($booking->customer_phone)
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-ink-800">{{ \App\Support\Phone::format($booking->customer_phone) }}</span>
                                    <a href="{{ \App\Support\Wa::bookingLink($booking) }}" target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-[11px] transition">
                                        <x-icon name="whatsapp" class="w-3.5 h-3.5" />
                                        <span>Chat WA</span>
                                    </a>
                                </div>
                            @else
                                <span class="text-ink-400 italic">Tidak ada nomor</span>
                            @endif
                        </div>

                        @if ($booking->customer)
                            <div class="pt-3 border-t border-marble-200">
                                <a href="{{ route('admin.customers.show', $booking->customer) }}"
                                   class="text-sage-700 hover:text-sage-900 font-bold flex items-center justify-between p-2.5 rounded-xl bg-cream-50 hover:bg-cream-100 transition">
                                    <span>Lihat Profil & Riwayat Kunjungan</span>
                                    <x-icon name="arrow-right" class="w-4 h-4" />
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline Log Riwayat Status (Audit Trail) -->
                <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                    <h2 class="font-heading text-lg font-bold text-sage-900 mb-4">Riwayat Perubahan</h2>

                    <ol class="relative border-l border-marble-200 ml-2 space-y-4 text-xs">
                        @foreach ($booking->logs as $log)
                            <li class="ml-4">
                                <div class="absolute -left-1.5 mt-1.5 w-3 h-3 rounded-full border-2 border-white bg-sage-600"></div>
                                <time class="font-mono text-[10px] text-ink-400 block">
                                    {{ $log->created_at->translatedFormat('d M Y, H:i:s') }} WIB
                                </time>
                                <div class="font-semibold text-ink-800 mt-0.5 flex flex-wrap items-center gap-1">
                                    @if ($log->from_status)
                                        <span class="px-1.5 py-0.2 rounded text-[10px] {{ $log->from_status->badgeClass() }}">{{ $log->from_status->label() }}</span>
                                        <span class="text-ink-400">&rarr;</span>
                                    @endif
                                    <span class="px-1.5 py-0.2 rounded text-[10px] {{ $log->to_status->badgeClass() }}">{{ $log->to_status->label() }}</span>
                                </div>
                                <p class="text-ink-500 text-[11px] mt-0.5">
                                    Oleh: <strong>{{ $log->user?->name ?? 'Sistem / Pelanggan' }}</strong>
                                </p>
                                @if ($log->reason)
                                    <p class="mt-1 p-2 rounded-lg bg-cream-100 text-ink-700 text-[11px] italic">
                                        "{{ $log->reason }}"
                                    </p>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Batalkan Booking (Wajib Alasan) -->
    <div id="cancelModal" class="fixed inset-0 z-50 bg-ink-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-marble-200 animate-in fade-in zoom-in-95 duration-150">
            <h3 class="font-heading text-lg font-bold text-red-700 mb-2">Batalkan Booking</h3>
            <p class="text-xs text-ink-600 mb-4">
                Slot akan dilepas dan pelanggan akan mengetahui status pembatalan ini jika mengecek kode booking.
            </p>
            <form action="{{ route('admin.bookings.transition', [$booking, 'cancelled']) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-ink-800 mb-1">Alasan Pembatalan <span class="text-red-600">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Contoh: Permintaan pelanggan via telepon, staf berhalangan, dll."
                              class="w-full px-3 py-2 rounded-xl border border-marble-200 text-xs focus:ring-2 focus:ring-red-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 text-xs font-bold">
                    <button type="button" onclick="closeCancelModal()" class="px-4 py-2 rounded-xl border border-marble-200 text-ink-700 hover:bg-cream-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-700 hover:bg-red-800 text-white shadow-xs">
                        Konfirmasi Pembatalan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal No-Show (Wajib Alasan) -->
    <div id="noShowModal" class="fixed inset-0 z-50 bg-ink-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-marble-200 animate-in fade-in zoom-in-95 duration-150">
            <h3 class="font-heading text-lg font-bold text-amber-900 mb-2">Tandai No-Show (Tidak Hadir)</h3>
            <p class="text-xs text-ink-600 mb-4">
                Pelanggan tidak datang pada jam booking yang ditentukan dan tidak ada pemberitahuan.
            </p>
            <form action="{{ route('admin.bookings.transition', [$booking, 'no_show']) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-ink-800 mb-1">Catatan / Alasan No-Show <span class="text-amber-800">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Contoh: Sudah ditelepon 3x tidak diangkat setelah 15 menit lewat jadwal."
                              class="w-full px-3 py-2 rounded-xl border border-marble-200 text-xs focus:ring-2 focus:ring-amber-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 text-xs font-bold">
                    <button type="button" onclick="closeNoShowModal()" class="px-4 py-2 rounded-xl border border-marble-200 text-ink-700 hover:bg-cream-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white shadow-xs">
                        Tandai Tidak Hadir
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }
        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }
        function openNoShowModal() {
            document.getElementById('noShowModal').classList.remove('hidden');
        }
        function closeNoShowModal() {
            document.getElementById('noShowModal').classList.add('hidden');
        }
    </script>
</x-layouts.admin>
