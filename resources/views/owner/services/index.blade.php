<x-layouts.admin title="Kelola Layanan Salon">
    <div class="space-y-6">
        <!-- Header & Tombol Tambah Layanan -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl font-bold text-sage-900">Kelola Layanan Salon</h1>
                <p class="text-xs text-ink-600 mt-0.5">
                    Atur menu treatment, paket bundling, harga, durasi, buffer waktu, dan staf yang kompeten
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('categoryModal').classList.remove('hidden')"
                        class="px-4 py-2 rounded-xl border border-marble-300 hover:bg-cream-100 text-ink-800 text-xs font-bold transition">
                    + Tambah Kategori
                </button>
                <a href="{{ route('owner.services.create') }}"
                   class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Tambah Layanan Baru</span>
                </a>
            </div>
        </div>

        <!-- Info Card Kebijakan LY-04 -->
        <div class="p-3.5 rounded-xl bg-sage-50 border border-sage-200 text-xs text-sage-900 flex items-start gap-2.5">
            <x-icon name="sparkles" class="w-4 h-4 text-sage-700 mt-0.5 shrink-0" />
            <div>
                <strong>Ketentuan Data Layanan:</strong> Layanan tidak memiliki tombol hapus agar riwayat transaksi masa lalu tetap utuh. Untuk menyembunyikan layanan dari booking online, ubah statusnya menjadi <em>Tidak Tersedia</em> pada form edit layanan.
            </div>
        </div>

        <!-- Daftar Layanan per Kategori -->
        <div class="space-y-6">
            @foreach ($categories as $cat)
                <div class="bg-white rounded-2xl border border-marble-200 shadow-xs overflow-hidden">
                    <!-- Kategori Header -->
                    <div class="p-4 bg-cream-50 border-b border-marble-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="font-heading text-lg font-bold text-sage-900">{{ $cat->name }}</h2>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-cream-200 text-ink-700">
                                {{ $cat->services->count() }} layanan
                            </span>
                        </div>

                        <!-- Edit Kategori Trigger -->
                        <button type="button" onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', {{ $cat->sort_order }})"
                                class="text-xs font-semibold text-ink-500 hover:text-sage-700">
                            Ubah Nama Kategori
                        </button>
                    </div>

                    @if ($cat->services->isEmpty())
                        <p class="p-6 text-xs text-ink-400 italic text-center">Belum ada layanan dalam kategori ini.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-cream-100/60 text-ink-600 font-bold uppercase text-[10px] border-b border-marble-200">
                                    <tr>
                                        <th class="p-3.5">Layanan</th>
                                        <th class="p-3.5">Durasi & Buffer</th>
                                        <th class="p-3.5 text-right">Harga</th>
                                        <th class="p-3.5 text-center">Ketersediaan</th>
                                        <th class="p-3.5">Staf Kompeten</th>
                                        <th class="p-3.5 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-marble-200">
                                    @foreach ($cat->services as $s)
                                        <tr class="hover:bg-cream-50/50 transition">
                                            <td class="p-3.5">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-10 h-10 rounded-xl bg-cream-100 border border-marble-200 overflow-hidden shrink-0">
                                                        @if ($s->image_path)
                                                            <img src="{{ asset($s->image_path) }}" alt="{{ $s->name }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center text-marble-400">
                                                                <x-icon name="sparkles" class="w-4 h-4" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-ink-900 text-sm flex items-center gap-1.5">
                                                            <span>{{ $s->name }}</span>
                                                            @if ($s->is_featured)
                                                                <span class="px-2 py-0.2 rounded-full text-[9px] font-bold uppercase bg-gold-100 text-gold-900 border border-gold-300">
                                                                    Unggulan
                                                                </span>
                                                            @endif
                                                            @if ($s->is_sensitive)
                                                                <span class="px-2 py-0.2 rounded-full text-[9px] font-bold uppercase bg-wine-100 text-wine-900 border border-wine-300">
                                                                    Sensitif
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if ($s->description)
                                                            <p class="text-[11px] text-ink-500 line-clamp-1 mt-0.5">{{ $s->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3.5 font-mono text-ink-700">
                                                <span class="font-bold">{{ $s->duration_minutes }} menit</span>
                                                <span class="text-ink-400 text-[10px] block">+ buffer {{ $s->buffer_minutes }} menit</span>
                                            </td>
                                            <td class="p-3.5 text-right font-mono">
                                                <span class="font-bold text-ink-900 text-sm block">{{ rupiah($s->price) }}</span>
                                                @if ($s->price_max)
                                                    <span class="text-[10px] text-ink-400 block">s/d {{ rupiah($s->price_max) }}</span>
                                                @endif
                                            </td>
                                            <td class="p-3.5 text-center">
                                                @if ($s->is_available)
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                        Tersedia
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-900 border border-rose-200">
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="p-3.5 text-ink-600">
                                                @if ($s->staff->isEmpty())
                                                    <span class="text-amber-700 font-semibold text-[11px]">Semua Staf Aktif</span>
                                                @else
                                                    <span class="font-medium">{{ $s->staff->count() }} staf terlatih</span>
                                                    <span class="text-[10px] text-ink-400 block truncate max-w-xs">{{ $s->staff->pluck('name')->join(', ') }}</span>
                                                @endif
                                            </td>
                                            <td class="p-3.5 text-right">
                                                <a href="{{ route('owner.services.edit', $s) }}"
                                                   class="px-3 py-1.5 rounded-xl border border-marble-200 hover:bg-cream-100 text-sage-900 font-bold transition">
                                                    Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Tambah/Edit Kategori -->
    <div id="categoryModal" class="fixed inset-0 z-50 bg-ink-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-marble-200 animate-in fade-in zoom-in-95 duration-150">
            <h3 id="catModalTitle" class="font-heading text-lg font-bold text-sage-900 mb-4">Tambah Kategori Baru</h3>
            <form id="categoryForm" action="{{ route('owner.categories.store') }}" method="POST">
                @csrf
                <div id="catMethodField"></div>
                <div class="space-y-3 text-xs mb-5">
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Nama Kategori <span class="text-red-600">*</span></label>
                        <input type="text" id="catName" name="name" required placeholder="Contoh: Paket Bundling, Perawatan Kuku"
                               class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700">
                    </div>
                    <div>
                        <label class="block font-semibold text-ink-800 mb-1">Urutan Tampil (Sort Order)</label>
                        <input type="number" id="catSort" name="sort_order" value="0"
                               class="w-full px-3 py-2 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 text-xs font-bold">
                    <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 rounded-xl border border-marble-200 text-ink-700 hover:bg-cream-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white shadow-xs">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
            document.getElementById('catModalTitle').innerText = 'Tambah Kategori Baru';
            document.getElementById('categoryForm').action = "{{ route('owner.categories.store') }}";
            document.getElementById('catMethodField').innerHTML = '';
            document.getElementById('catName').value = '';
            document.getElementById('catSort').value = '0';
        }

        function editCategory(id, name, sort) {
            document.getElementById('catModalTitle').innerText = 'Ubah Kategori';
            document.getElementById('categoryForm').action = `/admin/kategori/${id}`;
            document.getElementById('catMethodField').innerHTML = '@method("PUT")';
            document.getElementById('catName').value = name;
            document.getElementById('catSort').value = sort;
            document.getElementById('categoryModal').classList.remove('hidden');
        }
    </script>
</x-layouts.admin>
