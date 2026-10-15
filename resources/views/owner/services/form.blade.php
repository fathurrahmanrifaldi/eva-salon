<x-layouts.admin :title="$service->exists ? 'Edit Layanan: ' . $service->name : 'Tambah Layanan Baru'">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb & Header -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('owner.services.index') }}" class="text-xs font-semibold text-ink-500 hover:text-sage-700 flex items-center gap-1">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    Kembali ke Daftar Layanan
                </a>
            </div>
            <h1 class="font-heading text-2xl font-bold text-sage-900">
                {{ $service->exists ? 'Edit Layanan: ' . $service->name : 'Tambah Layanan Baru' }}
            </h1>
            <p class="text-xs text-ink-600 mt-0.5">
                {{ $service->exists ? 'Perubahan harga hanya berlaku untuk booking baru; booking lama tetap mempertahankan harga saat transaksi.' : 'Isi rincian treatment dan staf yang berwenang melayani.' }}
            </p>
        </div>

        <!-- Form Layanan -->
        <form action="{{ $service->exists ? route('owner.services.update', $service) : route('owner.services.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($service->exists)
                @method('PUT')
            @endif

            <!-- Informasi Dasar -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-5">
                <h2 class="font-heading text-lg font-bold text-sage-900 border-b border-marble-200 pb-3">Informasi Perawatan</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Kategori Layanan <span class="text-red-600">*</span></label>
                        <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium">
                            <option value="">Pilih Kategori...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Urutan Tampil (Sort Order)</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                        @error('sort_order') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-ink-800 mb-1">Nama Layanan / Treatment <span class="text-red-600">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $service->name) }}" required placeholder="Contoh: Balayage Coloring & Blow"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium text-sm">
                        @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-ink-800 mb-1">Deskripsi Lengkap / Manfaat Perawatan</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan tahapan treatment, produk yang digunakan, atau untuk siapa treatment ini cocok."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">{{ old('description', $service->description) }}</textarea>
                        @error('description') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Harga, Durasi & Buffer Waktu -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-5">
                <h2 class="font-heading text-lg font-bold text-sage-900 border-b border-marble-200 pb-3">Harga, Durasi & Buffer Waktu</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Harga Dasar (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $service->price) }}" required min="0" step="1000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white">
                        <span class="text-[10px] text-ink-400 mt-1 block">Harga flat atau harga mulai dari</span>
                        @error('price') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Harga Maksimal (Rp)</label>
                        <input type="number" name="price_max" value="{{ old('price_max', $service->price_max) }}" min="0" step="1000"
                               placeholder="Opsional (range harga)"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                        <span class="text-[10px] text-ink-400 mt-1 block">Kosongkan jika harga flat</span>
                        @error('price_max') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Durasi Pengerjaan <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?? 60) }}" required min="5" max="600"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white pr-14">
                            <span class="absolute right-3.5 top-2.5 text-ink-400 font-medium">menit</span>
                        </div>
                        @error('duration_minutes') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Buffer Istirahat / Persiapan <span class="text-red-600">*</span></label>
                        <div class="relative">
                            <input type="number" name="buffer_minutes" value="{{ old('buffer_minutes', $service->buffer_minutes ?? 10) }}" required min="0" max="120"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 font-mono font-bold focus:ring-2 focus:ring-sage-700 bg-white pr-14">
                            <span class="absolute right-3.5 top-2.5 text-ink-400 font-medium">menit</span>
                        </div>
                        <span class="text-[10px] text-ink-400 mt-1 block">Waktu jeda sebelum staf menerima tamu baru</span>
                        @error('buffer_minutes') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Foto & Status Layanan -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-5">
                <h2 class="font-heading text-lg font-bold text-sage-900 border-b border-marble-200 pb-3">Foto & Pengaturan Visibilitas</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <!-- Upload Foto -->
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Foto Layanan / Katalog</label>
                        @if ($service->image_path)
                            <div class="mb-3 w-32 h-32 rounded-xl overflow-hidden border border-marble-200">
                                <img src="{{ asset($service->image_path) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-xs text-ink-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sage-100 file:text-sage-800 hover:file:bg-sage-200 cursor-pointer">
                        <span class="text-[10px] text-ink-400 mt-1 block">Format JPG, PNG, atau WEBP, maks 2MB.</span>
                        @error('image') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Checkboxes -->
                    <div class="space-y-3 pt-2">
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-marble-200 bg-cream-50/50 cursor-pointer hover:bg-cream-100/60 transition">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $service->is_available ?? true) ? 'checked' : '' }}
                                   class="mt-0.5 rounded text-sage-700 focus:ring-sage-700">
                            <div>
                                <span class="font-bold text-ink-900 block">Tersedia untuk Booking Online</span>
                                <span class="text-[11px] text-ink-500">Jika dimatikan, layanan ini tidak akan muncul pada daftar pemilihan layanan pelanggan online.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-marble-200 bg-cream-50/50 cursor-pointer hover:bg-cream-100/60 transition">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}
                                   class="mt-0.5 rounded text-sage-700 focus:ring-sage-700">
                            <div>
                                <span class="font-bold text-ink-900 block">Jadikan Paket Unggulan (Spotlight)</span>
                                <span class="text-[11px] text-ink-500">Layanan akan ditampilkan di beranda depan sebagai paket rekomendasi spesial.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-xl border border-marble-200 bg-cream-50/50 cursor-pointer hover:bg-cream-100/60 transition">
                            <input type="checkbox" name="is_sensitive" value="1" {{ old('is_sensitive', $service->is_sensitive) ? 'checked' : '' }}
                                   class="mt-0.5 rounded text-sage-700 focus:ring-sage-700">
                            <div>
                                <span class="font-bold text-ink-900 block">Perawatan Sensitif / Butuh Privasi Ekstra</span>
                                <span class="text-[11px] text-ink-500">Memberikan penandaan khusus dan disclaimer persetujuan privasi tambahan saat booking.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Staf yang Terlatih Melayani -->
            <div class="bg-white rounded-2xl border border-marble-200 p-6 shadow-xs space-y-4">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-lg font-bold text-sage-900">Staf yang Kompeten</h2>
                    <p class="text-xs text-ink-500 mt-0.5">
                        Centang staf yang memiliki keahlian untuk treatment ini. Jika tidak ada yang dicentang, semua staf aktif dianggap dapat melayani.
                    </p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 text-xs">
                    @foreach ($staff as $stf)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-xl border border-marble-200 hover:bg-cream-50 cursor-pointer transition">
                            <input type="checkbox" name="staff_ids[]" value="{{ $stf->id }}"
                                   {{ in_array($stf->id, old('staff_ids', $assigned)) ? 'checked' : '' }}
                                   class="rounded text-sage-700 focus:ring-sage-700">
                            <div class="truncate">
                                <span class="font-bold text-ink-900 block truncate">{{ $stf->name }}</span>
                                <span class="text-[10px] text-ink-500 block truncate">{{ $stf->position ?? 'Staf' }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('staff_ids') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-marble-200 shadow-xs">
                <a href="{{ route('owner.services.index') }}" class="px-5 py-2.5 rounded-xl border border-marble-300 text-xs font-bold text-ink-700 hover:bg-cream-100 transition">
                    Batalkan
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <x-icon name="check" class="w-4 h-4" />
                    <span>Simpan Layanan</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
