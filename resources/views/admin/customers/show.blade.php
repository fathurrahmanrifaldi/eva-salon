<x-layouts.admin title="Profil Pelanggan: {{ $customer->name }}">
    <div class="space-y-6">
        <!-- Breadcrumb & Header Profil -->
        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
            <div class="flex items-center gap-2 mb-3">
                <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-ink-500 hover:text-sage-700 flex items-center gap-1">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    Kembali ke Daftar Pelanggan
                </a>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="font-heading text-2xl font-bold text-sage-900">{{ $customer->name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-ink-600 mt-1">
                        @if ($customer->phone)
                            <div class="flex items-center gap-1.5 font-mono font-medium text-ink-800">
                                <x-icon name="phone" class="w-3.5 h-3.5 text-sage-600" />
                                <span>{{ \App\Support\Phone::format($customer->phone) }}</span>
                                <a href="{{ \App\Support\Wa::directLink($customer->phone) }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-600 text-white font-semibold text-[10px] hover:bg-emerald-700 transition">
                                    <x-icon name="whatsapp" class="w-3 h-3" />
                                    <span>Chat</span>
                                </a>
                            </div>
                        @endif
                        @if ($customer->email)
                            <div class="flex items-center gap-1.5">
                                <x-icon name="mail" class="w-3.5 h-3.5 text-ink-400" />
                                <span>{{ $customer->email }}</span>
                            </div>
                        @endif
                        <div class="text-ink-400">
                            Terdaftar: {{ $customer->created_at->translatedFormat('d M Y') }}
                        </div>
                    </div>
                </div>

                <!-- Statistik Ringkas Pelanggan -->
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2.5 rounded-xl bg-cream-50 border border-marble-200 text-center">
                        <span class="text-[10px] uppercase font-bold text-ink-500 block">Total Kunjungan</span>
                        <span class="text-lg font-bold text-sage-900">{{ $bookings->where('status', \App\Enums\BookingStatus::Completed)->count() }}x</span>
                    </div>
                    <div class="px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                        <span class="text-[10px] uppercase font-bold text-emerald-700 block">Total Belanja</span>
                        <span class="text-lg font-mono font-bold text-emerald-900">{{ rupiah($totalPaid) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Riwayat Kunjungan & Booking (2 Kolom) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                    <h2 class="font-heading text-lg font-bold text-sage-900 mb-4">Riwayat Kunjungan & Booking</h2>

                    @if ($bookings->isEmpty())
                        <p class="text-xs text-ink-500 italic p-6 bg-cream-50 rounded-xl text-center">
                            Belum ada riwayat booking untuk pelanggan ini.
                        </p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-cream-100 text-ink-700 font-bold uppercase text-[10px] border-b border-marble-200">
                                    <tr>
                                        <th class="p-3">Kode & Tanggal</th>
                                        <th class="p-3">Layanan</th>
                                        <th class="p-3">Staf</th>
                                        <th class="p-3 text-right">Biaya / Bayar</th>
                                        <th class="p-3 text-center">Status</th>
                                        <th class="p-3 text-right">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-marble-200">
                                    @foreach ($bookings as $b)
                                        <tr class="hover:bg-cream-50/60 transition">
                                            <td class="p-3">
                                                <span class="font-mono font-bold text-sage-900 block">{{ $b->booking_code }}</span>
                                                <span class="text-ink-500 text-[11px] block">{{ tanggal($b->start_at) }} &bull; {{ jam($b->start_at) }}</span>
                                            </td>
                                            <td class="p-3">
                                                <div class="font-semibold text-ink-900">
                                                    {{ $b->items->pluck('service_name')->join(', ') }}
                                                </div>
                                                <span class="text-[10px] text-ink-500 font-mono">{{ $b->start_at->diffInMinutes($b->end_at) }} menit</span>
                                            </td>
                                            <td class="p-3 text-ink-700 font-medium">
                                                {{ $b->staff?->name ?? 'Staf belum diatur' }}
                                            </td>
                                            <td class="p-3 text-right font-mono">
                                                <span class="font-bold text-ink-900 block">{{ rupiah($b->total_price) }}</span>
                                                <span class="text-[10px] text-emerald-700 block">Bayar: {{ rupiah($b->paidTotal()) }}</span>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $b->status->badgeClass() }}">
                                                    {{ $b->status->label() }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-right">
                                                <a href="{{ route('admin.bookings.show', $b) }}"
                                                   class="px-2.5 py-1 rounded-lg border border-marble-200 hover:bg-cream-100 text-sage-900 font-bold transition">
                                                    Buka
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom Kanan: Form Edit Data & Catatan Alergi / Preferensi (1 Kolom) -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                    <h2 class="font-heading text-lg font-bold text-sage-900 mb-4">Edit Data & Catatan</h2>

                    <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="space-y-4 text-xs">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block font-semibold text-ink-800 mb-1">Nama Pelanggan <span class="text-red-600">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                                   class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white font-medium">
                            @error('name')
                                <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-ink-800 mb-1">Nomor WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="08..."
                                   class="w-full px-3 py-2 rounded-xl border border-marble-200 font-mono focus:ring-2 focus:ring-sage-700 bg-white">
                            @error('phone')
                                <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-ink-800 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                                   class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white">
                            @error('email')
                                <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-ink-800 mb-1">Catatan Pribadi / Preferensi / Alergi</label>
                            <textarea name="notes" rows="4" placeholder="Misal: Alergi cat rambut amonia tertentu, suka teh melati hangat, sensitif kulit kepala."
                                      class="w-full px-3 py-2 rounded-xl border border-marble-200 focus:ring-2 focus:ring-sage-700 bg-white text-xs">{{ old('notes', $customer->notes) }}</textarea>
                            <p class="text-[11px] text-ink-400 mt-1">Catatan ini hanya terlihat oleh staf dan kasir salon.</p>
                            @error('notes')
                                <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-sage-700 hover:bg-sage-900 text-white font-bold transition shadow-xs">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
