<x-layouts.admin title="Profil Salon & Jam Operasional">
    <div class="space-y-8">
        <!-- Header -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
            <h1 class="font-heading text-2xl font-bold text-sage-900">Profil Salon & Pengaturan Bisnis</h1>
            <p class="text-xs text-ink-600 mt-0.5">
                Kelola informasi identitas salon, jam buka mingguan, aturan booking dan pembatalan, serta galeri portofolio
            </p>
        </div>

        <form action="{{ route('owner.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Identitas & Kontak Salon -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-4">
                <h2 class="font-heading text-lg font-bold text-sage-900 border-b border-marble-200 pb-3">Identitas & Kontak Salon</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Nama Salon <span class="text-red-600">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $profile->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium text-sm">
                        @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Nomor WhatsApp Resmi <span class="text-red-600">*</span></label>
                        <input type="text" name="wa_number" value="{{ old('wa_number', $profile->wa_number) }}" required placeholder="08..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white">
                        <span class="text-[10px] text-ink-400 mt-1 block">Digunakan pelanggan untuk chat dan kirim bukti transfer.</span>
                        @error('wa_number') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Nomor Telepon Salon</label>
                        <input type="text" name="phone" value="{{ old('phone', $profile->phone) }}" placeholder="021... atau 08..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                        @error('phone') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Catatan Operasional Singkat</label>
                        <input type="text" name="operating_note" value="{{ old('operating_note', $profile->operating_note) }}" placeholder="Contoh: Khusus Hari Jumat buka pukul 13.00 WIB"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">
                        @error('operating_note') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-ink-800 mb-1">Alamat Lengkap <span class="text-red-600">*</span></label>
                        <textarea name="address" rows="2" required placeholder="Jl. Raya Utama No. 123..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">{{ old('address', $profile->address) }}</textarea>
                        @error('address') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-ink-800 mb-1">Google Maps Embed URL</label>
                        <input type="url" name="map_embed" value="{{ old('map_embed', $profile->map_embed) }}" placeholder="https://www.google.com/maps/embed?pb=..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono text-[11px] focus:ring-2 focus:ring-sage-700 bg-white">
                        <span class="text-[10px] text-ink-400 mt-1 block">Salin atribut <code>src</code> dari menu "Embed a map" di Google Maps.</span>
                        @error('map_embed') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-ink-800 mb-1">Deskripsi / Cerita Salon</label>
                        <textarea name="description" rows="3" placeholder="Deskripsikan konsep salon, kenyamanan, produk organik yang digunakan, dll."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">{{ old('description', $profile->description) }}</textarea>
                        @error('description') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Jam Buka Mingguan (Business Hours) -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-4">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-lg font-bold text-sage-900">Jam Operasional Salon</h2>
                    <p class="text-xs text-ink-500 mt-0.5">
                        Menentukan rentang waktu ketersediaan slot booking di halaman publik.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-cream-100 text-ink-700 font-bold uppercase text-[10px] border-b border-marble-200">
                            <tr>
                                <th class="p-3 w-32">Hari</th>
                                <th class="p-3 w-28 text-center">Tutup</th>
                                <th class="p-3">Jam Buka</th>
                                <th class="p-3">Jam Tutup</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marble-200">
                            @php
                                $orderedDays = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu'];
                            @endphp
                            @foreach ($orderedDays as $dayIndex => $dayName)
                                @php
                                    $bh = $hours->get($dayIndex);
                                    $isClosed = old("hours.$dayIndex.is_closed", $bh?->is_closed ?? false);
                                    $openTime = old("hours.$dayIndex.open_time", $bh?->open_time ? substr($bh->open_time, 0, 5) : '09:00');
                                    $closeTime = old("hours.$dayIndex.close_time", $bh?->close_time ? substr($bh->close_time, 0, 5) : '20:30');
                                @endphp
                                <tr>
                                    <td class="p-3 font-bold text-ink-900">
                                        {{ $dayName }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="hours[{{ $dayIndex }}][is_closed]" value="1"
                                                   id="closed_{{ $dayIndex }}"
                                                   {{ $isClosed ? 'checked' : '' }}
                                                   onchange="toggleHoursRow({{ $dayIndex }})"
                                                   class="rounded text-rose-600 focus:ring-rose-500">
                                            <span class="ml-1.5 text-[11px] text-ink-600">Tutup</span>
                                        </label>
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="hours[{{ $dayIndex }}][open_time]"
                                               id="open_{{ $dayIndex }}"
                                               value="{{ $openTime }}"
                                               {{ $isClosed ? 'disabled' : '' }}
                                               class="px-3 py-1.5 rounded-xl border border-marble-200 font-mono text-xs focus:ring-2 focus:ring-sage-700 bg-white disabled:bg-cream-100 disabled:text-ink-400">
                                    </td>
                                    <td class="p-3">
                                        <input type="time" name="hours[{{ $dayIndex }}][close_time]"
                                               id="close_{{ $dayIndex }}"
                                               value="{{ $closeTime }}"
                                               {{ $isClosed ? 'disabled' : '' }}
                                               class="px-3 py-1.5 rounded-xl border border-marble-200 font-mono text-xs focus:ring-2 focus:ring-sage-700 bg-white disabled:bg-cream-100 disabled:text-ink-400">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Aturan Bisnis & Booking -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-4">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-lg font-bold text-sage-900">Aturan Booking & Pembatalan</h2>
                    <p class="text-xs text-ink-500 mt-0.5">
                        Parameter pembatalan mandiri pelanggan online dan durasi penahanan slot (hold) kasir.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Batas Batal Online (Menit) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="number" name="cancel_limit_minutes" value="{{ old('cancel_limit_minutes', $profile->cancel_limit_minutes ?? 30) }}" required min="0" max="1440"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white pr-14">
                            <span class="absolute right-3.5 top-2.5 text-ink-400 font-medium">menit</span>
                        </div>
                        <span class="text-[10px] text-ink-400 mt-1 block">Maksimal selisih sebelum jadwal mulai</span>
                        @error('cancel_limit_minutes') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Durasi Standar Hold (Menit) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="number" name="hold_default_minutes" value="{{ old('hold_default_minutes', $profile->hold_default_minutes ?? 15) }}" required min="5" max="1440"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white pr-14">
                            <span class="absolute right-3.5 top-2.5 text-ink-400 font-medium">menit</span>
                        </div>
                        <span class="text-[10px] text-ink-400 mt-1 block">Waktu awal penahanan slot telepon</span>
                        @error('hold_default_minutes') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Batas Maksimal Hold (Menit) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="number" name="hold_max_minutes" value="{{ old('hold_max_minutes', $profile->hold_max_minutes ?? 120) }}" required min="5" max="1440"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white pr-14">
                            <span class="absolute right-3.5 top-2.5 text-ink-400 font-medium">menit</span>
                        </div>
                        <span class="text-[10px] text-ink-400 mt-1 block">Batas maksimal setelah perpanjangan</span>
                        @error('hold_max_minutes') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Peringatan Pending (Jam) <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="number" name="pending_alert_hours" value="{{ old('pending_alert_hours', $profile->pending_alert_hours ?? 2) }}" required min="1" max="168"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white pr-12">
                            <span class="absolute right-3.5 top-2.5 text-ink-400 font-medium">jam</span>
                        </div>
                        <span class="text-[10px] text-ink-400 mt-1 block">Waktu sebelum jadwal untuk notifikasi kasir</span>
                        @error('pending_alert_hours') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4 pt-2">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-marble-200 bg-cream-50/50 cursor-pointer hover:bg-cream-100/60 transition">
                            <input type="checkbox" name="require_payment_to_complete" value="1"
                                   {{ old('require_payment_to_complete', $profile->require_payment_to_complete) ? 'checked' : '' }}
                                   class="rounded text-sage-700 focus:ring-sage-700">
                            <div>
                                <span class="font-bold text-ink-900 block">Wajibkan Pelunasan Penuh Sebelum Menandai Booking Selesai</span>
                                <span class="text-[11px] text-ink-500">Mencegah kasir menandai status "Selesai" jika masih terdapat sisa tagihan yang belum dibayar.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan Profil -->
            <div class="flex items-center justify-end p-4 bg-white rounded-2xl border border-marble-200 shadow-xs">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Simpan Profil & Aturan</span>
                </button>
            </div>
        </form>

        <!-- Galeri Portofolio Salon (Foto Tempat & Karya) -->
        <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-5">
            <div class="border-b border-marble-200 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="font-heading text-lg font-bold text-sage-900">Galeri Suasana & Portofolio</h2>
                    <p class="text-xs text-ink-500 mt-0.5">
                        Ditampilkan di beranda publik untuk menarik minat calon tamu. Maksimal {{ $maxGallery }} foto.
                    </p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-cream-200 text-ink-800">
                    {{ $gallery->count() }} / {{ $maxGallery }} Foto
                </span>
            </div>

            <!-- Form Upload Foto Baru -->
            @if ($gallery->count() < $maxGallery)
                <form action="{{ route('owner.gallery.store') }}" method="POST" enctype="multipart/form-data"
                      class="p-4 rounded-xl bg-cream-50 border border-marble-200 flex flex-col sm:flex-row items-end gap-3 text-xs">
                    @csrf
                    <div class="flex-1 w-full">
                        <label class="block font-semibold text-ink-800 mb-1">Pilih Foto Galeri</label>
                        <input type="file" name="image" required accept="image/jpeg,image/png,image/webp"
                               class="w-full text-xs text-ink-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sage-100 file:text-sage-800 hover:file:bg-sage-200 cursor-pointer">
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block font-semibold text-ink-800 mb-1">Keterangan / Caption Foto</label>
                        <input type="text" name="caption" placeholder="Contoh: Ruang Hair Styling VIP, Suasana Spa Nyaman"
                               class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">
                    </div>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-sage-700 hover:bg-sage-900 text-white font-bold transition shrink-0 shadow-xs">
                        + Unggah Foto
                    </button>
                </form>
            @endif

            <!-- Grid Foto Galeri -->
            @if ($gallery->isEmpty())
                <p class="text-xs text-ink-400 italic text-center p-6 bg-cream-50 rounded-xl">Belum ada foto galeri yang diunggah.</p>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach ($gallery as $img)
                        <div class="relative group rounded-xl overflow-hidden border border-marble-200 bg-cream-100 aspect-square">
                            <img src="{{ asset($img->path) }}" alt="{{ $img->caption }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-ink-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-3 text-white">
                                <p class="text-[11px] line-clamp-2">{{ $img->caption ?: 'Tanpa keterangan' }}</p>
                                <form action="{{ route('owner.gallery.destroy', $img) }}" method="POST" onsubmit="return confirm('Hapus foto ini dari galeri?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full py-1.5 rounded-lg bg-red-600/90 hover:bg-red-700 text-white text-[11px] font-bold transition">
                                        Hapus Foto
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleHoursRow(day) {
            const isClosed = document.getElementById(`closed_${day}`).checked;
            document.getElementById(`open_${day}`).disabled = isClosed;
            document.getElementById(`close_${day}`).disabled = isClosed;
        }
    </script>
</x-layouts.admin>
