<x-layouts.public title="Eva Salon - Perawatan Rambut, Wajah & Tubuh di Bekasi Timur">
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-20 md:py-28 bg-gradient-to-b from-cream-100 to-cream-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sage-100 border border-sage-300 text-sage-900 text-xs font-semibold tracking-wide">
                        <x-icon name="spark" class="w-4 h-4 text-gold-700" />
                        <span>Salon Kecantikan & Perawatan Keluarga di Bekasi Timur</span>
                    </div>

                    <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-semibold text-sage-900 leading-[1.15]">
                        Tampil segar, tubuh nyaman, di <span class="italic text-gold-700">Eva Salon</span>.
                    </h1>

                    <p class="text-base sm:text-lg text-ink-600 leading-relaxed max-w-xl">
                        {{ $profile->description ?? 'Perawatan rambut, wajah, tubuh, dan kuku di Perumnas 3 Bekasi. Ruangan lapang, bersih, hangat, dan terjangkau.' }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('booking.create') }}" class="inline-flex items-center justify-center gap-2.5 bg-sage-700 hover:bg-sage-900 text-white font-medium px-7 py-3.5 rounded-full shadow-md hover:shadow-lg transition focus:ring-3 focus:ring-gold-400 text-base">
                            <x-icon name="calendar" class="w-5 h-5" />
                            <span>Booking Sekarang</span>
                        </a>

                        @php
                            $waNum = preg_replace('/\D/', '', $profile->wa_number ?? '6281234567890');
                        @endphp
                        <a href="https://wa.me/{{ $waNum }}?text={{ rawurlencode('Halo Eva Salon, saya ingin bertanya tentang layanan perawatan.') }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2.5 bg-white hover:bg-sage-50 text-sage-900 border border-sage-300 font-medium px-6 py-3.5 rounded-full transition text-base">
                            <x-icon name="whatsapp" class="w-5 h-5 text-[#25D366]" />
                            <span>Tanya via WhatsApp</span>
                        </a>
                    </div>

                    <!-- Highlight Kecil -->
                    <div class="pt-6 grid grid-cols-3 gap-4 border-t border-marble-200 text-xs sm:text-sm text-ink-600">
                        <div>
                            <span class="block font-heading text-xl sm:text-2xl font-bold text-sage-900">20+</span>
                            <span>Staf Ahli & Ramah</span>
                        </div>
                        <div>
                            <span class="block font-heading text-xl sm:text-2xl font-bold text-sage-900">09.00 - 20.30</span>
                            <span>Buka Setiap Hari</span>
                        </div>
                        <div>
                            <span class="block font-heading text-xl sm:text-2xl font-bold text-sage-900">Rp 40rb+</span>
                            <span>Harga Bersahabat</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Lengkung (Arch) Khas Eva Salon -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="relative w-full max-w-sm">
                        <!-- Bingkai Lengkung Luar -->
                        <div class="rounded-arch overflow-hidden bg-white p-3 shadow-soft border border-marble-200">
                            <div class="rounded-arch overflow-hidden aspect-[4/5] bg-sage-100 flex flex-col justify-end p-6 text-white relative bg-cover bg-center"
                                 style="background-image: linear-gradient(to top, rgba(47,67,50,0.85) 0%, rgba(47,67,50,0.2) 60%, transparent 100%), url('https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=800&q=80');">
                                <div class="relative z-10">
                                    <span class="text-xs uppercase tracking-widest text-gold-400 font-semibold">Suasana Salon</span>
                                    <h3 class="font-heading text-2xl font-semibold mt-1">Tenang, Segar & Bersih</h3>
                                    <p class="text-xs text-sage-100 mt-1">Dinding hijau sage, aksen marmer, dan pelayanan yang penuh perhatian.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Badge Lengkung Floating -->
                        <div class="absolute -bottom-4 -left-4 bg-white/95 backdrop-blur-md border border-marble-200 rounded-2xl p-4 shadow-lg max-w-[200px]">
                            <p class="text-xs font-semibold text-sage-900">Pasti Tanpa Antre</p>
                            <p class="text-[11px] text-ink-600 mt-0.5">Booking online mencegah jadwal bentrok.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Unggulan -->
    <section class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs uppercase tracking-wider text-gold-700 font-bold">Layanan Favorit</span>
                    <h2 class="font-heading text-3xl sm:text-4xl font-semibold text-sage-900 mt-1">
                        Paling Sering Dipilih Pelanggan
                    </h2>
                </div>
                <a href="{{ route('catalog') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-sage-700 hover:text-sage-900 group">
                    <span>Lihat Semua Layanan & Harga</span>
                    <span class="group-hover:translate-x-1 transition">&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($featured as $srv)
                    <div class="bg-cream-50 rounded-2xl border border-marble-200 p-6 flex flex-col justify-between hover:shadow-soft hover:border-sage-300 transition group">
                        <div>
                            <div class="flex items-center justify-between text-xs text-ink-600 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full bg-white border border-marble-200 font-medium text-sage-900">
                                    {{ $srv->category->name }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-icon name="clock" class="w-3.5 h-3.5 text-sage-700" />
                                    <span>{{ $srv->duration_minutes }} menit</span>
                                </span>
                            </div>

                            <h3 class="font-heading text-2xl font-semibold text-sage-900 group-hover:text-sage-700 transition">
                                {{ $srv->name }}
                            </h3>

                            @if ($srv->description)
                                <p class="text-xs text-ink-600 mt-2 line-clamp-2">{{ $srv->description }}</p>
                            @endif
                        </div>

                        <div class="mt-6 pt-4 border-t border-marble-200/80 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] text-ink-600 block">Harga</span>
                                <span class="text-base font-bold text-sage-900">{{ $srv->priceLabel() }}</span>
                            </div>
                            <a href="{{ route('booking.create', ['layanan[]' => $srv->id]) }}"
                               class="inline-flex items-center gap-1 bg-sage-700 hover:bg-sage-900 text-white text-xs font-semibold px-4 py-2 rounded-full transition shadow-xs">
                                <span>Pilih</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Paket Khusus Spotlight (Paket Lulur Susu) -->
    @if ($packages->isNotEmpty())
        <section class="py-20 bg-cream-100 bg-fluted">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-xs uppercase tracking-wider text-wine-700 font-bold bg-white px-3 py-1 rounded-full border border-marble-200">
                        Perawatan Lengkap Hemat
                    </span>
                    <h2 class="font-heading text-3xl sm:text-4xl font-semibold text-sage-900 mt-3">
                        Paket Perawatan Tubuh Khusus
                    </h2>
                    <p class="text-sm text-ink-600 mt-2">
                        Kombinasi lulur tradisional, pijat relaksasi, dan mandi susu untuk mengembalikan kesegaran tubuh secara menyeluruh.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($packages as $pkg)
                        <div class="bg-white rounded-3xl border border-marble-200 p-8 shadow-sm flex flex-col justify-between hover:border-gold-400 transition relative">
                            @if ($loop->first)
                                <span class="absolute -top-3 right-6 bg-gold-400 text-ink-900 text-[11px] font-bold px-3 py-1 rounded-full shadow-xs">
                                    Paling Diminati
                                </span>
                            @endif

                            <div>
                                <h3 class="font-heading text-2xl font-bold text-sage-900">{{ $pkg->name }}</h3>
                                <p class="text-xs text-ink-600 mt-1 flex items-center gap-1">
                                    <x-icon name="clock" class="w-3.5 h-3.5 text-sage-700" />
                                    <span>Estimasi durasi {{ durasi($pkg->duration_minutes) }}</span>
                                </p>

                                <div class="mt-4 mb-6">
                                    <span class="text-2xl font-extrabold text-sage-900">{{ rupiah($pkg->price) }}</span>
                                </div>

                                <div class="space-y-2 border-t border-marble-200 pt-4">
                                    <span class="text-xs font-semibold text-ink-600 uppercase tracking-wider block mb-2">Termasuk dalam paket:</span>
                                    @foreach ($pkg->lines() as $step)
                                        <div class="flex items-center gap-2 text-xs text-ink-900">
                                            <x-icon name="check" class="w-4 h-4 text-sage-700 flex-shrink-0" />
                                            <span>{{ $step }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-8 pt-4">
                                <a href="{{ route('booking.create', ['layanan[]' => $pkg->id]) }}"
                                   class="block text-center w-full bg-sage-700 hover:bg-sage-900 text-white text-sm font-semibold py-3 rounded-full transition shadow-xs">
                                    Pilih Paket Ini
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Suasana Salon & Galeri -->
    @if ($gallery->isNotEmpty())
        <section class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-xl mx-auto mb-12">
                    <span class="text-xs uppercase tracking-wider text-gold-700 font-bold">Galeri Eva Salon</span>
                    <h2 class="font-heading text-3xl sm:text-4xl font-semibold text-sage-900 mt-1">
                        Suasana Nyaman & Bersih
                    </h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($gallery as $img)
                        <div class="rounded-arch overflow-hidden border border-marble-200 bg-cream-50 aspect-[4/5] shadow-xs">
                            <img src="{{ asset($img->path) }}" alt="{{ $img->caption ?? 'Suasana Eva Salon' }}"
                                 loading="lazy" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Jam Operasional & Lokasi -->
    <section id="lokasi" class="py-20 bg-cream-50 border-t border-marble-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                <!-- Info Jam Operasional -->
                <div class="lg:col-span-5 space-y-6">
                    <div>
                        <span class="text-xs uppercase tracking-wider text-gold-700 font-bold">Kunjungi Kami</span>
                        <h2 class="font-heading text-3xl font-semibold text-sage-900 mt-1">
                            Jam Operasional & Alamat
                        </h2>
                    </div>

                    <p class="text-sm text-ink-600 leading-relaxed">
                        {{ $profile->address ?? 'Perumnas 3 Ruko No.3, RT.004/RW.014, Aren Jaya, Kec. Bekasi Timur, Kota Bekasi, Jawa Barat 17111' }}
                    </p>

                    <!-- Tabel Jam -->
                    <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-ink-600 mb-3">Jadwal Buka Mingguan</h3>
                        <div class="divide-y divide-marble-200 text-xs">
                            @foreach ($hours as $h)
                                <div class="py-2.5 flex items-center justify-between {{ $h->day_of_week === (int)date('w') ? 'font-bold text-sage-900 bg-sage-50 px-2 rounded-md' : 'text-ink-600' }}">
                                    <span>{{ $h->dayName() }} @if($h->day_of_week === (int)date('w')) <span class="text-[10px] text-sage-700 uppercase">(Hari Ini)</span> @endif</span>
                                    <span>
                                        @if ($h->is_closed)
                                            <span class="text-red-600">Tutup</span>
                                        @else
                                            {{ substr($h->open_time, 0, 5) }} – {{ substr($h->close_time, 0, 5) }} WIB
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    
                </div>

                <!-- Peta Embed / Visual Lokasi -->
                <div class="lg:col-span-7">
                    <div class="rounded-3xl overflow-hidden border border-marble-200 bg-white shadow-soft h-[380px]">
                        @if ($profile->map_embed)
                            <iframe src="{{ $profile->map_embed }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center p-8 text-center bg-cream-100 bg-fluted">
                                <span class="font-heading text-2xl font-bold text-sage-900 mb-2">Lokasi Eva Salon</span>
                                <p class="text-xs text-ink-600 max-w-md leading-relaxed mb-4">
                                    {{ $profile->address ?? 'Perumnas 3 Ruko No.3, Aren Jaya, Bekasi Timur' }}
                                </p>
                                <a href="https://maps.google.com/?q={{ urlencode($profile->address ?? 'Eva Salon Bekasi') }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-2 bg-sage-700 hover:bg-sage-900 text-white text-xs font-semibold px-5 py-2.5 rounded-full shadow-sm">
                                    <span>Buka di Google Maps</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="py-16 bg-sage-700 text-white text-center">
        <div class="max-w-3xl mx-auto px-4">
            <h2 class="font-heading text-3xl sm:text-4xl font-semibold mb-4">
                Siap Meluangkan Waktu untuk Merawat Diri?
            </h2>
            <p class="text-sm sm:text-base text-sage-100 mb-8 max-w-xl mx-auto">
                Pilih layanan dan staf favorit Anda secara online dalam 1 menit. Tanpa perlu registrasi akun.
            </p>
            <a href="{{ route('booking.create') }}" class="inline-flex items-center gap-2 bg-white hover:bg-cream-50 text-sage-900 font-bold px-8 py-3.5 rounded-full shadow-lg hover:shadow-xl transition text-base">
                <x-icon name="calendar" class="w-5 h-5 text-sage-700" />
                <span>Booking Sekarang</span>
            </a>
        </div>
    </section>
</x-layouts.public>
