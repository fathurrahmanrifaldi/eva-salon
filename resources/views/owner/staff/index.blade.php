<x-layouts.admin title="Kelola Staf & Jadwal">
    <div class="space-y-8">
        <!-- Header & Tombol Tambah Staf -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl font-bold text-sage-900">Staf & Jadwal Kerja</h1>
                <p class="text-xs text-ink-600 mt-0.5">
                    Atur profil kapster/terapis, keahlian treatment, jam shift mingguan, serta cuti dan libur operasional
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('exceptionModal').classList.remove('hidden')"
                        class="px-4 py-2 rounded-xl border border-marble-300 hover:bg-cream-100 text-ink-800 text-xs font-bold transition">
                    + Catat Cuti / Libur
                </button>
                <a href="{{ route('owner.staff.create') }}"
                   class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Tambah Staf Baru</span>
                </a>
            </div>
        </div>

        <!-- Bagian 1: Daftar Staf Salon -->
        <div class="bg-white rounded-2xl border border-marble-200 shadow-xs overflow-hidden">
            <div class="p-4 bg-cream-50 border-b border-marble-200 flex items-center justify-between">
                <h2 class="font-heading text-lg font-bold text-sage-900">Daftar Kapster & Terapis</h2>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-cream-200 text-ink-700">
                    Total {{ $staff->count() }} orang ({{ $staff->where('is_active', true)->count() }} aktif)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-cream-100 text-ink-700 font-bold uppercase text-[10px] border-b border-marble-200">
                        <tr>
                            <th class="p-4">Staf</th>
                            <th class="p-4">Kontak</th>
                            <th class="p-4">Keahlian Layanan</th>
                            <th class="p-4">Jadwal Shift</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-marble-200">
                        @foreach ($staff as $stf)
                            <tr class="hover:bg-cream-50/50 transition {{ ! $stf->is_active ? 'opacity-60 bg-cream-100/30' : '' }}">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-sage-200 text-sage-800 font-bold flex items-center justify-center text-xs overflow-hidden shrink-0 border border-marble-200">
                                            @if ($stf->photo_path)
                                                <img src="{{ asset($stf->photo_path) }}" alt="{{ $stf->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ substr($stf->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-bold text-ink-900 text-sm block">{{ $stf->name }}</span>
                                            <span class="text-[11px] text-ink-500">{{ $stf->position ?? 'Staf' }} &bull; {{ $stf->gender === 'female' ? 'Wanita' : ($stf->gender === 'male' ? 'Pria' : '-') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono text-ink-700">
                                    @if ($stf->phone)
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ \App\Support\Phone::format($stf->phone) }}</span>
                                            <a href="{{ \App\Support\Wa::directLink($stf->phone) }}" target="_blank" rel="noopener"
                                               class="p-1 rounded text-emerald-700 hover:bg-emerald-50 transition" title="WhatsApp">
                                                <x-icon name="whatsapp" class="w-3 h-3" />
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-ink-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="p-4 text-ink-600">
                                    @if ($stf->services->isEmpty())
                                        <span class="text-amber-800 font-semibold text-[11px]">Semua Treatment</span>
                                    @else
                                        <span class="font-bold text-ink-900">{{ $stf->services->count() }} treatment</span>
                                        <span class="text-[10px] text-ink-500 block truncate max-w-xs">{{ $stf->services->pluck('name')->join(', ') }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-ink-600 text-[11px]">
                                    @php
                                        $workingDays = $stf->schedules->pluck('day_of_week')->unique()->count();
                                    @endphp
                                    <span class="font-semibold text-ink-800">{{ $workingDays }} hari kerja / minggu</span>
                                </td>
                                <td class="p-4 text-center">
                                    @if ($stf->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-900 border border-emerald-300">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-900 border border-rose-200">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form action="{{ route('owner.staff.toggle', $stf) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl border border-marble-200 hover:bg-cream-100 text-[11px] font-semibold text-ink-700 transition"
                                                    title="{{ $stf->is_active ? 'Nonaktifkan sementara staf ini' : 'Aktifkan kembali staf ini' }}">
                                                {{ $stf->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('owner.staff.edit', $stf) }}"
                                           class="px-3 py-1.5 rounded-xl border border-marble-200 hover:bg-cream-100 text-sage-900 font-bold transition">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bagian 2: Cuti & Libur Operasional (Schedule Exceptions) -->
        <div class="bg-white rounded-2xl border border-marble-200 shadow-xs overflow-hidden">
            <div class="p-4 bg-cream-50 border-b border-marble-200 flex items-center justify-between">
                <div>
                    <h2 class="font-heading text-lg font-bold text-sage-900">Jadwal Cuti & Libur Salon</h2>
                    <p class="text-xs text-ink-500 mt-0.5">Sistem otomatis memblokir pemesanan slot pada tanggal-tanggal di bawah ini.</p>
                </div>

                <button type="button" onclick="document.getElementById('exceptionModal').classList.remove('hidden')"
                        class="px-3 py-1.5 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs">
                    + Tambah Cuti / Libur
                </button>
            </div>

            @if ($exceptions->isEmpty())
                <p class="p-6 text-xs text-ink-400 italic text-center">Tidak ada jadwal cuti atau libur aktif yang tercatat saat ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-cream-100 text-ink-700 font-bold uppercase text-[10px] border-b border-marble-200">
                            <tr>
                                <th class="p-4">Jenis</th>
                                <th class="p-4">Staf Terkait</th>
                                <th class="p-4">Rentang Waktu</th>
                                <th class="p-4">Keterangan / Alasan</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marble-200">
                            @foreach ($exceptions as $ex)
                                <tr class="hover:bg-cream-50/50 transition">
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $ex->type === \App\Enums\ExceptionType::SalonClosed ? 'bg-rose-100 text-rose-900 border border-rose-300' : 'bg-amber-100 text-amber-900 border border-amber-300' }}">
                                            {{ $ex->type->label() }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-bold text-ink-900">
                                        {{ $ex->staff?->name ?? 'Seluruh Salon (Tutup)' }}
                                    </td>
                                    <td class="p-4 font-mono text-ink-700">
                                        <span class="font-bold">{{ tanggal($ex->starts_on) }}</span>
                                        @if ($ex->starts_on->ne($ex->ends_on))
                                            <span> s/d </span>
                                            <span class="font-bold">{{ tanggal($ex->ends_on) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-ink-600">
                                        {{ $ex->note ?: '-' }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <form action="{{ route('owner.exceptions.destroy', $ex) }}" method="POST" onsubmit="return confirm('Hapus jadwal cuti/libur ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 font-semibold transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Tambah Cuti / Libur -->
    <div id="exceptionModal" class="fixed inset-0 z-50 bg-ink-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-marble-200 animate-in fade-in zoom-in-95 duration-150">
            <h3 class="font-heading text-lg font-bold text-sage-900 mb-3">Catat Cuti / Libur Salon</h3>
            <form action="{{ route('owner.exceptions.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-ink-800 mb-1">Jenis Pengecualian <span class="text-red-600">*</span></label>
                    <select id="exceptionType" name="type" required onchange="toggleStaffField(this.value)"
                            class="w-full px-3 py-2 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700 bg-white">
                        @foreach ($types as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="staffSelectGroup">
                    <label class="block font-semibold text-ink-800 mb-1">Pilih Staf <span class="text-red-600">*</span></label>
                    <select name="staff_id" class="w-full px-3 py-2 rounded-xl border border-marble-200 font-medium focus:ring-2 focus:ring-sage-700 bg-white">
                        <option value="">Pilih Staf yang Cuti...</option>
                        @foreach ($activeStaff as $astf)
                            <option value="{{ $astf->id }}">{{ $astf->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Tanggal Mulai <span class="text-red-600">*</span></label>
                        <input type="date" name="starts_on" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Tanggal Selesai <span class="text-red-600">*</span></label>
                        <input type="date" name="ends_on" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-ink-800 mb-1">Alasan / Catatan</label>
                    <input type="text" name="note" placeholder="Contoh: Cuti tahunan, Libur Hari Raya Idul Fitri"
                           class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 font-bold">
                    <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-marble-200 text-ink-700 hover:bg-cream-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white shadow-xs">
                        Simpan Cuti
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleStaffField(type) {
            const grp = document.getElementById('staffSelectGroup');
            if (type === 'salon_closed') {
                grp.classList.add('hidden');
            } else {
                grp.classList.remove('hidden');
            }
        }
    </script>
</x-layouts.admin>
