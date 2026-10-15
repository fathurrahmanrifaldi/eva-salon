<x-layouts.admin title="Data Pelanggan">
    <div class="space-y-5">
        <!-- Header & Pencarian -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl font-bold text-sage-900">Database Pelanggan</h1>
                <p class="text-xs text-ink-600 mt-0.5">
                    Total {{ $customers->total() }} pelanggan terdaftar dari booking online dan walk-in
                </p>
            </div>

            <!-- Form Cari -->
            <form action="{{ route('admin.customers.index') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau nomor HP..."
                           class="w-64 sm:w-80 pl-9 pr-4 py-2 rounded-xl border border-marble-200 text-xs font-medium focus:ring-2 focus:ring-sage-700 bg-white">
                    <div class="absolute left-3 top-2.5 text-ink-400">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold transition shadow-xs">
                    Cari
                </button>
                @if ($q)
                    <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 rounded-xl border border-marble-200 text-xs font-semibold text-ink-600 hover:bg-cream-100 transition">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabel Pelanggan -->
        <div class="bg-white rounded-2xl border border-marble-200 shadow-xs overflow-hidden">
            @if ($customers->isEmpty())
                <div class="p-12 text-center text-ink-500">
                    <x-icon name="users" class="w-10 h-10 mx-auto text-marble-300 mb-2" />
                    <p class="font-semibold text-sm">Tidak ada data pelanggan yang cocok.</p>
                    <p class="text-xs text-ink-400 mt-1">Coba kata kunci pencarian yang berbeda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-cream-100 text-ink-700 font-bold uppercase tracking-wider text-[10px] border-b border-marble-200">
                            <tr>
                                <th class="p-4">Nama Pelanggan</th>
                                <th class="p-4">Nomor WhatsApp</th>
                                <th class="p-4">Email</th>
                                <th class="p-4 text-center">Kunjungan Selesai</th>
                                <th class="p-4">Terdaftar</th>
                                <th class="p-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-marble-200">
                            @foreach ($customers as $c)
                                <tr class="hover:bg-cream-50/60 transition">
                                    <td class="p-4">
                                        <div class="font-bold text-ink-900 text-sm">{{ $c->name }}</div>
                                        @if ($c->notes)
                                            <div class="text-[11px] text-amber-700 truncate max-w-xs mt-0.5" title="{{ $c->notes }}">
                                                Catatan: {{ $c->notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        @if ($c->phone)
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-medium text-ink-800">{{ \App\Support\Phone::format($c->phone) }}</span>
                                                <a href="{{ \App\Support\Wa::directLink($c->phone) }}" target="_blank" rel="noopener"
                                                   class="p-1 rounded-md text-emerald-700 hover:bg-emerald-50 transition" title="Kirim Pesan WhatsApp">
                                                    <x-icon name="whatsapp" class="w-3.5 h-3.5" />
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-ink-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-ink-600">
                                        {{ $c->email ?: '-' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold text-xs {{ $c->visits > 0 ? 'bg-sage-100 text-sage-900' : 'bg-cream-200 text-ink-500' }}">
                                            {{ $c->visits }}x
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono text-[11px] text-ink-500">
                                        {{ $c->created_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <a href="{{ route('admin.customers.show', $c) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-marble-200 hover:border-sage-400 hover:bg-sage-50 text-sage-900 font-bold transition shadow-2xs">
                                            <span>Riwayat & Profil</span>
                                            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($customers->hasPages())
                    <div class="p-4 border-t border-marble-200 bg-cream-50/50">
                        {{ $customers->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.admin>
