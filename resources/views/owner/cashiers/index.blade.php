<x-layouts.admin title="Kelola Akun Kasir">
    <div class="space-y-6">
        <!-- Alert Password Sementara (AK-02) -->
        @if (session('temp_password'))
            @php
                [$tempEmail, $tempPass] = session('temp_password');
            @endphp
            <div class="p-5 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-950 shadow-md">
                <div class="flex items-start gap-3">
                    <x-icon name="sparkles" class="w-6 h-6 text-amber-700 shrink-0 mt-0.5" />
                    <div class="flex-1 text-xs">
                        <h3 class="font-heading text-base font-bold text-amber-900 mb-1">Informasi Kredensial Kasir Baru / Reset</h3>
                        <p class="text-amber-800 mb-3">
                            Salin dan berikan kredensial login sementara ini kepada staf kasir yang bersangkutan. Kasir akan diwajibkan mengganti password pada saat pertama kali login.
                        </p>
                        <div class="p-3 bg-white rounded-xl border border-amber-200 font-mono text-sm space-y-1 inline-block">
                            <div>Email: <strong class="text-ink-900">{{ $tempEmail }}</strong></div>
                            <div>Password Sementara: <strong class="text-red-700 bg-red-50 px-2 py-0.5 rounded">{{ $tempPass }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header & Tombol Tambah Kasir -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl font-bold text-sage-900">Kelola Akun Kasir</h1>
                <p class="text-xs text-ink-600 mt-0.5">
                    Buat dan kelola akses akun kasir untuk operasional antrean harian dan pencatatan pembayaran
                </p>
            </div>

            <button type="button" onclick="document.getElementById('createCashierModal').classList.remove('hidden')"
                    class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Tambah Akun Kasir</span>
            </button>
        </div>

        <!-- Tabel Akun Kasir -->
        <div class="bg-white rounded-2xl border border-marble-200 shadow-xs overflow-hidden">
            @if ($cashiers->isEmpty())
                <p class="p-8 text-xs text-ink-400 italic text-center">Belum ada akun kasir yang terdaftar.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-cream-100 text-ink-700 font-bold uppercase text-[10px] border-b border-marble-200">
                            <tr>
                                <th class="p-4">Nama Kasir</th>
                                <th class="p-4">Email Login</th>
                                <th class="p-4 text-center">Status Akun</th>
                                <th class="p-4 text-center">Keamanan</th>
                                <th class="p-4">Terdaftar</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marble-200">
                            @foreach ($cashiers as $c)
                                <tr class="hover:bg-cream-50/50 transition {{ ! $c->is_active ? 'opacity-60 bg-cream-100/30' : '' }}">
                                    <td class="p-4">
                                        <div class="font-bold text-ink-900 text-sm">{{ $c->name }}</div>
                                    </td>
                                    <td class="p-4 font-mono text-ink-700">
                                        {{ $c->email }}
                                    </td>
                                    <td class="p-4 text-center">
                                        @if ($c->is_active)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-900 border border-emerald-300">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-900 border border-rose-200">
                                                Dinonaktifkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center">
                                        @if ($c->must_change_password)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-900 border border-amber-200">
                                                Wajib Ganti Password
                                            </span>
                                        @else
                                            <span class="text-ink-400 text-[11px]">Normal</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-ink-500 text-[11px]">
                                        {{ $c->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Tombol Reset Password -->
                                            <form action="{{ route('owner.cashiers.reset', $c) }}" method="POST"
                                                  onsubmit="return confirm('Reset password akun kasir {{ $c->name }}? Password sementara baru akan dibuat.')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl border border-marble-200 hover:bg-cream-100 text-[11px] font-semibold text-ink-700 transition"
                                                        title="Buat password sementara baru">
                                                    Reset Password
                                                </button>
                                            </form>

                                            <!-- Tombol Toggle Aktif/Nonaktif -->
                                            <form action="{{ route('owner.cashiers.toggle', $c) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl border border-marble-200 hover:bg-cream-100 text-[11px] font-semibold text-ink-700 transition">
                                                    {{ $c->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <!-- Tombol Edit Nama/Email -->
                                            <button type="button" onclick="editCashier({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ addslashes($c->email) }}')"
                                                    class="px-3 py-1.5 rounded-xl border border-marble-200 hover:bg-cream-100 text-sage-900 font-bold transition">
                                                Edit
                                            </button>
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

    <!-- Modal Tambah Kasir Baru -->
    <div id="createCashierModal" class="fixed inset-0 z-50 bg-ink-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-marble-200 animate-in fade-in zoom-in-95 duration-150">
            <h3 class="font-heading text-lg font-bold text-sage-900 mb-2">Tambah Akun Kasir Baru</h3>
            <p class="text-xs text-ink-600 mb-4">
                Password sementara 12 karakter acak akan otomatis dibuatkan dan ditampilkan setelah formulir disimpan.
            </p>
            <form action="{{ route('owner.cashiers.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-ink-800 mb-1">Nama Kasir <span class="text-red-600">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Siti Rahma"
                           class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium">
                </div>
                <div>
                    <label class="block font-semibold text-ink-800 mb-1">Email Login <span class="text-red-600">*</span></label>
                    <input type="email" name="email" required placeholder="kasir@evasalon.id"
                           class="w-full px-3 py-2 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 font-bold">
                    <button type="button" onclick="document.getElementById('createCashierModal').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-marble-200 text-ink-700 hover:bg-cream-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white shadow-xs">
                        Buat Akun Kasir
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kasir -->
    <div id="editCashierModal" class="fixed inset-0 z-50 bg-ink-900/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-marble-200 animate-in fade-in zoom-in-95 duration-150">
            <h3 class="font-heading text-lg font-bold text-sage-900 mb-3">Ubah Data Kasir</h3>
            <form id="editCashierForm" action="" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold text-ink-800 mb-1">Nama Kasir <span class="text-red-600">*</span></label>
                    <input type="text" id="editCashierName" name="name" required
                           class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium">
                </div>
                <div>
                    <label class="block font-semibold text-ink-800 mb-1">Email Login <span class="text-red-600">*</span></label>
                    <input type="email" id="editCashierEmail" name="email" required
                           class="w-full px-3 py-2 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 font-bold">
                    <button type="button" onclick="document.getElementById('editCashierModal').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl border border-marble-200 text-ink-700 hover:bg-cream-100">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white shadow-xs">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCashier(id, name, email) {
            document.getElementById('editCashierForm').action = `/admin/kasir/${id}`;
            document.getElementById('editCashierName').value = name;
            document.getElementById('editCashierEmail').value = email;
            document.getElementById('editCashierModal').classList.remove('hidden');
        }
    </script>
</x-layouts.admin>
