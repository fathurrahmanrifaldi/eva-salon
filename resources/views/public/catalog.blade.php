<x-layouts.public title="Katalog Layanan & Harga &bull; Eva Salon">
    <div class="py-12 bg-cream-100 bg-fluted border-b border-marble-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
            <span class="text-xs uppercase tracking-wider text-gold-700 font-bold">Daftar Lengkap</span>
            <h1 class="font-heading text-4xl sm:text-5xl font-semibold text-sage-900 mt-2">
                Katalog Layanan & Harga
            </h1>
            <p class="text-sm text-ink-600 mt-2 max-w-xl mx-auto">
                Harga transparan tanpa biaya tersembunyi. Silakan cari atau pilih kategori perawatan yang Anda butuhkan.
            </p>

            <!-- Input Pencarian Cepat -->
            <div class="max-w-md mx-auto mt-6 relative">
                <input type="text" id="catalog-search" placeholder="Cari perawatan (misal: creambath, lulur, nail)..."
                       oninput="filterCatalog(this.value)"
                       class="w-full pl-11 pr-4 py-3 rounded-full border border-marble-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-sage-700 shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-600">
                    <x-icon name="search" class="w-5 h-5" />
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Layanan per Kategori -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
        <!-- Tab Kategori Sticky Mobile -->
        <div class="sticky top-18 z-20 bg-cream-50/95 backdrop-blur-sm py-3 mb-8 flex items-center gap-2 overflow-x-auto border-b border-marble-200 sm:justify-center">
            <a href="#all" onclick="showAllCategories(event)" class="cat-pill active px-4 py-1.5 rounded-full text-xs font-semibold bg-sage-700 text-white border border-sage-700 transition whitespace-nowrap">
                Semua
            </a>
            @foreach ($categories as $cat)
                <a href="#cat-{{ $cat->id }}" onclick="filterCategory(event, '{{ $cat->id }}')"
                   class="cat-pill px-4 py-1.5 rounded-full text-xs font-semibold bg-white border border-marble-200 text-ink-600 hover:text-sage-700 hover:border-sage-300 transition whitespace-nowrap">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        <div class="space-y-16">
            @foreach ($categories as $cat)
                <section id="cat-{{ $cat->id }}" class="cat-section">
                    <div class="border-b border-marble-200 pb-3 mb-6 flex items-baseline justify-between">
                        <h2 class="font-heading text-2xl sm:text-3xl font-semibold text-sage-900">
                            {{ $cat->name }}
                        </h2>
                        <span class="text-xs text-ink-600">{{ $cat->services->count() }} layanan</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($cat->services as $srv)
                            <div class="service-card bg-white rounded-2xl border border-marble-200 p-6 flex flex-col justify-between shadow-xs hover:border-sage-300 transition {{ ! $srv->is_available ? 'opacity-60 bg-cream-50' : '' }}"
                                 data-name="{{ strtolower($srv->name) }} {{ strtolower($srv->description ?? '') }}">
                                <div>
                                    <div class="flex items-center justify-between text-xs text-ink-600 mb-2">
                                        <span class="flex items-center gap-1 font-medium">
                                            <x-icon name="clock" class="w-3.5 h-3.5 text-sage-700" />
                                            <span>{{ $srv->duration_minutes }} menit</span>
                                        </span>
                                        @if (! $srv->is_available)
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-gray-200 text-gray-700">Tidak tersedia</span>
                                        @elseif ($srv->is_featured)
                                            <span class="text-[11px] font-semibold text-gold-700">Favorit</span>
                                        @endif
                                    </div>

                                    <h3 class="font-heading text-xl font-bold text-sage-900">{{ $srv->name }}</h3>

                                    @if ($srv->description)
                                        @if ($cat->name === 'Paket Khusus')
                                            <ul class="mt-3 space-y-1 text-xs text-ink-600 border-t border-marble-200 pt-3">
                                                @foreach ($srv->lines() as $line)
                                                    <li class="flex items-center gap-1.5">
                                                        <x-icon name="check" class="w-3.5 h-3.5 text-sage-700 flex-shrink-0" />
                                                        <span>{{ $line }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-xs text-ink-600 mt-2 leading-relaxed">{{ $srv->description }}</p>
                                        @endif
                                    @endif
                                </div>

                                <div class="mt-6 pt-4 border-t border-marble-200 flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] text-ink-600 block">Biaya</span>
                                        <span class="text-base font-bold text-sage-900">{{ $srv->priceLabel() }}</span>
                                        @if ($srv->price_max)
                                            <span class="block text-[10px] text-ink-600">Menyesuaikan di salon</span>
                                        @endif
                                    </div>

                                    @if ($srv->is_available)
                                        <a href="{{ route('booking.create', ['layanan[]' => $srv->id]) }}"
                                           class="inline-flex items-center gap-1 bg-sage-700 hover:bg-sage-900 text-white text-xs font-semibold px-4 py-2 rounded-full transition shadow-xs">
                                            <span>Pilih Layanan</span>
                                        </a>
                                    @else
                                        <button disabled class="bg-marble-200 text-ink-600 cursor-not-allowed text-xs font-semibold px-4 py-2 rounded-full">
                                            Habis
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>

    <script>
        function filterCatalog(term) {
            term = term.toLowerCase().trim();
            const cards = document.querySelectorAll('.service-card');
            cards.forEach(card => {
                const text = card.getAttribute('data-name');
                if (!term || text.includes(term)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function setActivePill(pill) {
            if (!pill) return;
            document.querySelectorAll('.cat-pill').forEach(p => {
                p.classList.remove('bg-sage-700', 'text-white', 'border-sage-700');
                p.classList.add('bg-white', 'text-ink-600', 'border-marble-200');
            });
            pill.classList.remove('bg-white', 'text-ink-600', 'border-marble-200');
            pill.classList.add('bg-sage-700', 'text-white', 'border-sage-700');
        }

        function filterCategory(e, catId) {
            e.preventDefault();
            setActivePill(e.currentTarget || e.target.closest('.cat-pill'));
            document.querySelectorAll('.cat-section').forEach(sec => {
                sec.style.display = (sec.id === 'cat-' + catId) ? 'block' : 'none';
            });
        }

        function showAllCategories(e) {
            e.preventDefault();
            setActivePill(e.currentTarget || e.target.closest('.cat-pill'));
            document.querySelectorAll('.cat-section').forEach(sec => sec.style.display = 'block');
        }
    </script>
</x-layouts.public>
