<x-layouts.public title="Kebijakan Privasi &bull; Eva Salon">
    <div class="max-w-3xl mx-auto px-4 py-16">
        <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-sage-900 mb-6">
            Kebijakan Privasi & Perlindungan Data
        </h1>

        <div class="prose prose-sage text-sm text-ink-600 space-y-5 leading-relaxed bg-white p-8 rounded-3xl border border-marble-200 shadow-xs">
            <p>
                Selamat datang di <strong>Eva Salon</strong>. Kami menghargai dan melindungi privasi setiap pelanggan kami sesuai dengan ketentuan <em>Undang-Undang Perlindungan Data Pribadi (UU PDP) No. 27 Tahun 2022</em> di Republik Indonesia.
            </p>

            <h2 class="font-heading text-xl font-bold text-sage-900 mt-6">1. Data yang Kami Kumpulkan</h2>
            <p>
                Saat Anda melakukan reservasi online, kami hanya meminta data yang esensial untuk keperluan layanan:
            </p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Nama lengkap pemesan</li>
                <li>Nomor telepon / WhatsApp aktif (untuk konfirmasi status booking)</li>
                <li>Catatan preferensi perawatan (opsional)</li>
            </ul>

            <h2 class="font-heading text-xl font-bold text-sage-900 mt-6">2. Penggunaan Data</h2>
            <p>
                Data yang Anda berikan semata-mata digunakan untuk:
            </p>
            <ul class="list-disc pl-5 space-y-1">
                <li>Mengunci slot jadwal perawatan di sistem salon kami</li>
                <li>Mengirimkan informasi konfirmasi, pengingat, dan status booking melalui WhatsApp</li>
                <li>Mencatat riwayat kunjungan agar kami dapat memberikan rekomendasi perawatan yang sesuai saat kunjungan berikutnya</li>
            </ul>

            <h2 class="font-heading text-xl font-bold text-sage-900 mt-6">3. Kerahasiaan & Pihak Ketiga</h2>
            <p>
                Eva Salon <strong>tidak pernah menjual, membagikan, atau menyewakan</strong> data pribadi Anda kepada pihak ketiga mana pun untuk tujuan periklanan atau komersial lainnya.
            </p>

            <h2 class="font-heading text-xl font-bold text-sage-900 mt-6">4. Hak Anda atas Data Pribadi</h2>
            <p>
                Anda berhak meminta pembaruan data yang tidak akurat atau penghapusan data kontak Anda dari basis data kami sewaktu-waktu dengan menghubungi kasir salon kami via WhatsApp di <strong>{{ $profile->wa_number ?? '+62 812-3456-7890' }}</strong>.
            </p>

            <div class="pt-6 border-t border-marble-200 text-xs text-ink-600">
                Terakhir diperbarui: 15 Oktober 2026 &bull; Eva Salon Bekasi Timur.
            </div>
        </div>
    </div>
</x-layouts.public>
