<x-layouts.public title="Cek Status Reservasi &bull; Eva Salon">
    <div class="py-12 bg-cream-100 bg-fluted border-b border-marble-200">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <span class="text-xs uppercase tracking-wider text-gold-700 font-bold">Informasi Reservasi</span>
            <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-sage-900 mt-1">
                Cek Status Booking Anda
            </h1>
            <p class="text-xs sm:text-sm text-ink-600 mt-2">
                Masukkan kode booking 8 karakter dan nomor telepon yang Anda gunakan saat mendaftar.
            </p>
        </div>
    </div>

    <div class="max-w-xl mx-auto px-4 py-12">
        <x-flash />

        @if (!empty($success))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold">
                {{ $success }}
            </div>
        @endif

        @if (!empty($error))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
                {{ $error }}
            </div>
        @endif

        @if (!empty($notFound) && request()->isMethod('POST'))
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                Data booking tidak ditemukan. Pastikan kode booking dan nomor telepon yang dimasukkan sudah sesuai dengan saat pendaftaran.
            </div>
        @endif

        <!-- Form Pencarian -->
        <div class="bg-white rounded-3xl border border-marble-200 p-6 sm:p-8 shadow-xs">
            <form action="{{ route('status.check') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="status_code" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                        Kode Booking (8 Karakter)
                    </label>
                    <input type="text" id="status_code" name="code" value="{{ old('code', request('code')) }}"
                           placeholder="Contoh: K7F39QXM" maxlength="8" required
                           class="w-full px-4 py-3 rounded-xl border border-marble-200 font-mono uppercase tracking-widest text-sm focus:ring-2 focus:ring-sage-700">
                    @error('code') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status_phone" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                        Nomor WhatsApp / Telepon
                    </label>
                    <input type="text" id="status_phone" name="phone" value="{{ old('phone', request('phone')) }}"
                           placeholder="08xxxxxxxxxx atau +628xxx" required
                           class="w-full px-4 py-3 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
                    @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-sage-700 hover:bg-sage-900 text-white font-bold py-3 rounded-full text-xs uppercase tracking-wider transition shadow-sm">
                    Cari Data Reservasi
                </button>
            </form>
        </div>

        <!-- Hasil Pencarian -->
        @if ($booking)
            <div class="mt-8 bg-white rounded-3xl border border-marble-200 p-6 sm:p-8 shadow-soft space-y-6">
                <div class="flex items-center justify-between border-b border-marble-200 pb-4">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-ink-600 font-semibold block">Kode Booking</span>
                        <span class="font-mono text-2xl font-bold text-sage-900 tracking-wider">{{ $booking->booking_code }}</span>
                    </div>
                    <div>
                        <span class="{{ $booking->status->badge() }}">
                            <x-icon :name="$booking->status->icon()" class="w-4 h-4" />
                            <span>{{ $booking->status->label() }}</span>
                        </span>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between pb-2 border-b border-marble-200/60">
                        <span class="text-ink-600">Nama Pemesan:</span>
                        <span class="font-semibold text-sage-900">{{ $booking->customer_name }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-marble-200/60">
                        <span class="text-ink-600">Layanan:</span>
                        <span class="font-semibold text-sage-900 text-right">{{ $booking->items->pluck('service_name')->join(', ') }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-marble-200/60">
                        <span class="text-ink-600">Staf:</span>
                        <span class="font-semibold text-sage-900">{{ $booking->staff->name }}</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-marble-200/60">
                        <span class="text-ink-600">Waktu Kedatangan:</span>
                        <span class="font-semibold text-sage-900">{{ tanggal($booking->start_at) }}, pukul {{ jam($booking->start_at) }} WIB</span>
                    </div>
                    <div class="flex justify-between pb-2 border-b border-marble-200/60">
                        <span class="text-ink-600">Total Biaya:</span>
                        <span class="font-bold text-sage-900 text-sm">{{ rupiah($booking->total_price) }}</span>
                    </div>
                </div>

                <!-- Tombol Pembatalan oleh Pelanggan -->
                @if ($canCancel)
                    <div class="pt-4 border-t border-marble-200">
                        <form action="{{ route('status.cancel') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                            @csrf
                            <input type="hidden" name="code" value="{{ $booking->booking_code }}">
                            <input type="hidden" name="phone" value="{{ $booking->customer_phone }}">
                            <button type="submit" class="w-full py-2.5 px-4 rounded-full border border-red-300 text-red-700 hover:bg-red-50 text-xs font-semibold transition">
                                Batalkan Reservasi Ini
                            </button>
                        </form>
                        <p class="text-[11px] text-ink-600 text-center mt-2">
                            Pembatalan online diizinkan hingga {{ $profile->cancel_limit_minutes }} menit sebelum jadwal.
                        </p>
                    </div>
                @elseif (in_array($booking->status->value, ['pending', 'confirmed']))
                    <p class="text-xs text-ink-600 text-center bg-cream-50 p-3 rounded-xl border border-marble-200">
                        Batas waktu pembatalan online ({{ $profile->cancel_limit_minutes }} menit sebelum jadwal) telah lewat. Jika ada perubahan darurat, silakan hubungi kasir via WhatsApp.
                    </p>
                @endif
            </div>
        @endif
    </div>
</x-layouts.public>
