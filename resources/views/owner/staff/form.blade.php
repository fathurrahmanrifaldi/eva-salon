<x-layouts.admin :title="$staff->exists ? 'Edit Staf: ' . $staff->name : 'Tambah Staf Baru'">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('owner.staff.index') }}" class="text-xs font-semibold text-ink-500 hover:text-sage-700 flex items-center gap-1">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    Kembali ke Daftar Staf
                </a>
            </div>
            <h1 class="font-heading text-2xl font-bold text-sage-900">
                {{ $staff->exists ? 'Edit Staf: ' . $staff->name : 'Tambah Staf Baru' }}
            </h1>
            <p class="text-xs text-ink-600 mt-0.5">
                Atur identitas kapster/terapis, keahlian treatment, dan jadwal giliran kerja mingguan.
            </p>
        </div>

        <!-- Form Staf -->
        <form action="{{ $staff->exists ? route('owner.staff.update', $staff) : route('owner.staff.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($staff->exists)
                @method('PUT')
            @endif

            <!-- Biodata Staf -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-5">
                <h2 class="font-heading text-lg font-bold text-sage-900 border-b border-marble-200 pb-3">Profil & Identitas</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $staff->name) }}" required placeholder="Contoh: Rina Wijaya"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium">
                        @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Jabatan / Spesialisasi</label>
                        <input type="text" name="position" value="{{ old('position', $staff->position) }}" placeholder="Contoh: Senior Hair Stylist, Nail Artist"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium">
                        @error('position') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">
                            <option value="">Pilih Jenis Kelamin...</option>
                            <option value="female" {{ old('gender', $staff->gender) === 'female' ? 'selected' : '' }}>Wanita</option>
                            <option value="male" {{ old('gender', $staff->gender) === 'male' ? 'selected' : '' }}>Pria</option>
                        </select>
                        @error('gender') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Nomor WhatsApp Staf</label>
                        <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" placeholder="08..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                        @error('phone') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Urutan Tampil (Sort Order)</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $staff->sort_order ?? 0) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Status Keaktifan</label>
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-marble-200 bg-cream-50/50 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $staff->is_active ?? true) ? 'checked' : '' }}
                                   class="rounded text-sage-700 focus:ring-sage-700">
                            <span class="font-bold text-ink-900">Staf Aktif (Menerima Booking)</span>
                        </label>
                    </div>

                    <!-- Upload Foto Profil -->
                    <div class="sm:col-span-2 pt-2">
                        <label class="block font-semibold text-ink-800 mb-1">Foto Staf / Avatar</label>
                        @if ($staff->photo_path)
                            <div class="mb-3 w-20 h-20 rounded-full overflow-hidden border border-marble-200">
                                <img src="{{ asset($staff->photo_path) }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-xs text-ink-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sage-100 file:text-sage-800 hover:file:bg-sage-200 cursor-pointer">
                        <span class="text-[10px] text-ink-400 mt-1 block">Format JPG, PNG, atau WEBP, maks 2MB.</span>
                        @error('photo') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Layanan yang Dikuasai -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-4">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-lg font-bold text-sage-900">Keahlian Layanan</h2>
                    <p class="text-xs text-ink-500 mt-0.5">
                        Centang treatment yang dikuasai staf ini. Jika kosong, staf ini dapat dipilih untuk semua treatment.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                    @foreach ($services as $srv)
                        <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-marble-200 hover:bg-cream-50 cursor-pointer transition">
                            <input type="checkbox" name="service_ids[]" value="{{ $srv->id }}"
                                   {{ in_array($srv->id, old('service_ids', $assigned)) ? 'checked' : '' }}
                                   class="mt-0.5 rounded text-sage-700 focus:ring-sage-700">
                            <div>
                                <span class="font-bold text-ink-900 block">{{ $srv->name }}</span>
                                <span class="text-[10px] text-ink-500">{{ $srv->category?->name ?? 'Layanan' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Jadwal Shift Mingguan -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-4">
                <div class="border-b border-marble-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-heading text-lg font-bold text-sage-900">Jadwal Shift Kerja Mingguan</h2>
                        <p class="text-xs text-ink-500 mt-0.5">
                            Atur jam mulai dan selesai untuk masing-masing hari. Kosongkan hari jika staf libur pada hari tersebut.
                        </p>
                    </div>

                    <button type="button" onclick="fillStandardShifts()" class="px-3 py-1.5 rounded-xl border border-marble-300 text-xs font-semibold text-ink-700 hover:bg-cream-100 transition">
                        Isi Jam Standar (09:00 - 18:00)
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-cream-100 text-ink-700 font-bold uppercase text-[10px] border-b border-marble-200">
                            <tr>
                                <th class="p-3 w-32">Hari</th>
                                <th class="p-3">Shift Utama (Mulai &ndash; Selesai)</th>
                                <th class="p-3">Shift Tambahan / Split (Opsional)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marble-200">
                            @foreach ($days as $dayIndex => $dayName)
                                <tr>
                                    <td class="p-3 font-bold text-ink-900">
                                        {{ $dayName }}
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <input type="time" name="schedule[{{ $dayIndex }}][0][start]"
                                                   id="shift_{{ $dayIndex }}_0_start"
                                                   value="{{ old("schedule.$dayIndex.0.start", $schedule[$dayIndex][0]['start'] ?? null) }}"
                                                   class="px-3 py-1.5 rounded-xl border border-marble-200 font-mono text-xs focus:ring-2 focus:ring-sage-700 bg-white">
                                            <span class="text-ink-400">&ndash;</span>
                                            <input type="time" name="schedule[{{ $dayIndex }}][0][end]"
                                                   id="shift_{{ $dayIndex }}_0_end"
                                                   value="{{ old("schedule.$dayIndex.0.end", $schedule[$dayIndex][0]['end'] ?? null) }}"
                                                   class="px-3 py-1.5 rounded-xl border border-marble-200 font-mono text-xs focus:ring-2 focus:ring-sage-700 bg-white">
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <input type="time" name="schedule[{{ $dayIndex }}][1][start]"
                                                   value="{{ old("schedule.$dayIndex.1.start", $schedule[$dayIndex][1]['start'] ?? null) }}"
                                                   class="px-3 py-1.5 rounded-xl border border-marble-200 font-mono text-xs focus:ring-2 focus:ring-sage-700 bg-white">
                                            <span class="text-ink-400">&ndash;</span>
                                            <input type="time" name="schedule[{{ $dayIndex }}][1][end]"
                                                   value="{{ old("schedule.$dayIndex.1.end", $schedule[$dayIndex][1]['end'] ?? null) }}"
                                                   class="px-3 py-1.5 rounded-xl border border-marble-200 font-mono text-xs focus:ring-2 focus:ring-sage-700 bg-white">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-marble-200 shadow-xs">
                <a href="{{ route('owner.staff.index') }}" class="px-5 py-2.5 rounded-xl border border-marble-300 text-xs font-bold text-ink-700 hover:bg-cream-100 transition">
                    Batalkan
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Simpan Data Staf</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function fillStandardShifts() {
            // Isi hari Senin (1) s/d Sabtu (6) dengan 09:00 - 18:00
            for (let d = 0; d <= 6; d++) {
                const startEl = document.getElementById(`shift_${d}_0_start`);
                const endEl = document.getElementById(`shift_${d}_0_end`);
                if (startEl && endEl) {
                    if (d === 0) {
                        // Minggu kosongkan
                        startEl.value = '';
                        endEl.value = '';
                    } else {
                        startEl.value = '09:00';
                        endEl.value = '18:00';
                    }
                }
            }
        }
    </script>
</x-layouts.admin>
