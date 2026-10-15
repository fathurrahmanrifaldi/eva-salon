<x-layouts.public title="Booking Berhasil Diterima &bull; Eva Salon">
    <div class="max-w-xl mx-auto px-4 py-16 text-center">
        <!-- Badge Sukses -->
        <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-sage-100 border border-sage-300 text-sage-700 flex items-center justify-center shadow-sm">
            <x-icon name="check" class="w-8 h-8" />
        </div>

        <span class="text-xs font-bold uppercase tracking-wider text-gold-700">Reservasi Terkirim</span>
        <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-sage-900 mt-1">
            Booking Anda Berhasil Diterima
        </h1>
        <p class="text-xs sm:text-sm text-ink-600 mt-2 max-w-md mx-auto">
            Slot jadwal Anda sudah kami kunci di sistem. Saat ini status booking Anda adalah <strong>menunggu konfirmasi</strong> dari kasir salon.
        </p>

        <!-- Kotak Kode Booking -->
        <div class="mt-8 bg-white rounded-3xl border-2 border-sage-700/30 p-6 sm:p-8 shadow-soft text-center">
            <span class="text-xs uppercase tracking-wider text-ink-600 font-semibold block mb-1">Kode Booking Anda:</span>
            <div class="flex items-center justify-center gap-3">
                <span id="booking-code" class="font-mono text-3xl sm:text-4xl font-bold tracking-widest text-sage-900">
                    {{ $booking->booking_code }}
                </span>
                <button type="button" onclick="copyCode()" class="p-2 rounded-xl bg-sage-50 hover:bg-sage-100 text-sage-700 transition" title="Salin Kode">
                    <span id="copy-btn-text" class="text-xs font-semibold">Salin</span>
                </button>
            </div>
            <p class="text-[11px] text-ink-600 mt-2">
                Simpan kode ini dan nomor telepon Anda untuk mengecek status atau membatalkan reservasi.
            </p>

            <!-- Rincian -->
            <div class="mt-6 pt-6 border-t border-marble-200 text-left text-xs space-y-2">
                <div class="flex justify-between">
                    <span class="text-ink-600">Layanan:</span>
                    <span class="font-semibold text-sage-900 text-right">{{ $booking->items->pluck('service_name')->join(', ') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-600">Staf:</span>
                    <span class="font-semibold text-sage-900">{{ $booking->staff->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-600">Jadwal:</span>
                    <span class="font-semibold text-sage-900">{{ tanggal($booking->start_at) }} pukul {{ jam($booking->start_at) }} WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-600">Total Biaya:</span>
                    <span class="font-bold text-sage-900">{{ rupiah($booking->total_price) }}</span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi WhatsApp & Cek Status -->
        <div class="mt-8 space-y-3">
            @php
                $waSalonUrl = \App\Support\Wa::toSalon($booking);
            @endphp
            @if ($waSalonUrl)
                <a href="{{ $waSalonUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 w-full bg-[#25D366] hover:bg-[#20ba59] text-ink-900 font-bold px-6 py-3.5 rounded-full shadow-md hover:shadow-lg transition text-sm">
                    <x-icon name="whatsapp" class="w-5 h-5 text-ink-900" />
                    <span>Kirim Detail ke WhatsApp Salon</span>
                </a>
            @endif

            <a href="{{ route('status.form') }}" class="inline-flex items-center justify-center w-full bg-white hover:bg-cream-100 text-sage-900 border border-marble-200 font-semibold px-6 py-3 rounded-full transition text-xs">
                Cek Status Reservasi Nanti
            </a>
        </div>
    </div>

    <script>
        function copyCode() {
            const code = document.getElementById('booking-code').innerText.trim();
            navigator.clipboard.writeText(code).then(() => {
                const btn = document.getElementById('copy-btn-text');
                btn.innerText = 'Tersalin!';
                setTimeout(() => btn.innerText = 'Salin', 2000);
            });
        }
    </script>
</x-layouts.public>
