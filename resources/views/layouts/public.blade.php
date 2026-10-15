<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Eva Salon - Perawatan rambut, wajah, tubuh, dan kuku di Bekasi Timur. Bersih, hangat, modern, dan terjangkau.">
    <title>{{ $title ?? 'Eva Salon - Perawatan Kecantikan & Relaksasi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 text-ink-900 font-sans antialiased min-h-screen flex flex-col selection:bg-sage-100 selection:text-sage-900">
    <!-- Navbar Publik -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-marble-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-18 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <span class="font-heading font-semibold text-2xl tracking-wide text-sage-900 group-hover:text-sage-700 transition">
                    Eva Salon
                </span>
                <span class="hidden sm:inline-block h-4 w-px bg-marble-200"></span>
                <span class="hidden sm:inline-block text-xs uppercase tracking-wider text-gold-700 font-medium">Bekasi Timur</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-ink-600">
                <a href="{{ route('home') }}" class="hover:text-sage-700 transition">Beranda</a>
                <a href="{{ route('catalog') }}" class="hover:text-sage-700 transition">Katalog Layanan</a>
                <a href="{{ route('home') }}#lokasi" class="hover:text-sage-700 transition">Lokasi & Jam</a>
                <a href="{{ route('status.form') }}" class="hover:text-sage-700 transition">Cek Status</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('status.form') }}" class="md:hidden text-xs font-medium text-ink-600 px-2 py-1 rounded hover:bg-sage-50">
                    Cek Status
                </a>
                <a href="{{ route('booking.create') }}" class="inline-flex items-center gap-2 bg-sage-700 hover:bg-sage-900 text-white text-sm font-medium px-5 py-2.5 rounded-full shadow-sm hover:shadow transition focus:outline-none focus:ring-3 focus:ring-gold-400">
                    <x-icon name="calendar" class="w-4 h-4" />
                    <span>Booking Sekarang</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Tombol Mengambang WhatsApp Resmi -->
    @php
        $waNum = preg_replace('/\D/', '', \App\Models\SalonProfile::current()->wa_number ?? '6281234567890');
    @endphp
    <aside aria-label="Kontak WhatsApp" class="fixed bottom-6 right-6 z-50">
        <a href="https://wa.me/{{ $waNum }}?text={{ rawurlencode('Halo Eva Salon, saya ingin bertanya tentang layanan perawatan.') }}"
           target="_blank" rel="noopener noreferrer"
           class="flex items-center gap-2.5 bg-[#25D366] hover:bg-[#20ba59] text-ink-900 font-semibold text-sm px-4 py-3 rounded-full shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 focus:outline-none focus:ring-3 focus:ring-gold-400"
           title="Hubungi kami di WhatsApp">
            <x-icon name="whatsapp" class="w-5 h-5 text-ink-900" />
            <span class="hidden sm:inline">Tanya via WhatsApp</span>
        </a>
    </aside>

    <!-- Footer -->
    <footer class="bg-sage-900 text-sage-100 border-t border-sage-700 mt-20 pt-16 pb-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 pb-12 border-b border-sage-700">
                <div>
                    <h2 class="font-heading text-2xl font-semibold text-white mb-3">Eva Salon</h2>
                    <p class="text-sm text-sage-300 leading-relaxed max-w-sm mb-4">
                        Perawatan rambut, wajah, tubuh, dan kuku dengan suasana yang lapang, bersih, dan menenangkan di Bekasi Timur.
                    </p>
                    <p class="text-xs text-gold-400 font-medium tracking-wide">
                        Buka Setiap Hari: 09.00 – 20.30 WIB
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-white mb-3">Alamat & Kontak</h3>
                    <address class="text-sm text-sage-300 not-italic leading-relaxed">
                        {{ \App\Models\SalonProfile::current()->address ?? 'Perumnas 3 Ruko No.3, Aren Jaya, Bekasi Timur' }}<br>
                        <span class="block mt-2">WhatsApp: {{ \App\Models\SalonProfile::current()->wa_number ?? '+62 812-3456-7890' }}</span>
                    </address>
                </div>

                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-white mb-3">Tautan Singkat</h3>
                    <ul class="space-y-2 text-sm text-sage-300">
                        <li><a href="{{ route('catalog') }}" class="hover:text-white transition">Katalog Layanan & Harga</a></li>
                        <li><a href="{{ route('booking.create') }}" class="hover:text-white transition">Booking Online</a></li>
                        <li><a href="{{ route('status.form') }}" class="hover:text-white transition">Cek Status Reservasi</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Kebijakan Privasi (UU PDP)</a></li>
                        <li><a href="{{ route('login') }}" class="text-sage-400 hover:text-white transition">Login Staf / Kasir</a></li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-sage-300 gap-4">
                <p>&copy; {{ date('Y') }} Eva Salon. Semua hak cipta dilindungi.</p>
                <p class="text-sage-300">Nuansa Hijau Sage & Emas Lembut &bull; Bekasi Timur</p>
            </div>
        </div>
    </footer>
</body>
</html>
