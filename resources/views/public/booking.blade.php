<x-layouts.public title="Booking Online &bull; Eva Salon">
    <div class="py-8 sm:py-12 bg-cream-100 bg-fluted border-b border-marble-200">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="text-xs uppercase tracking-wider text-gold-700 font-bold">Reservasi Mudah</span>
            <h1 class="font-heading text-3xl sm:text-4xl font-semibold text-sage-900 mt-1">
                Booking Perawatan Salon
            </h1>
            <p class="text-xs sm:text-sm text-ink-600 mt-1">
                Pilih layanan, staf, dan jam yang Anda inginkan dalam 5 langkah sederhana.
            </p>

            <!-- Step Indicator -->
            <div class="flex items-center justify-center gap-2 sm:gap-4 mt-6">
                @foreach (['1. Layanan', '2. Staf', '3. Jadwal', '4. Data', '5. Konfirmasi'] as $idx => $label)
                    <div class="flex items-center gap-2">
                        <span id="step-badge-{{ $idx + 1 }}"
                              class="step-badge w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $idx === 0 ? 'bg-sage-700 text-white' : 'bg-white text-ink-600 border border-marble-200' }}">
                            {{ $idx + 1 }}
                        </span>
                        <span class="hidden sm:inline text-xs font-medium text-ink-600">{{ $label }}</span>
                        @if ($idx < 4)
                            <span class="w-4 sm:w-8 h-0.5 bg-marble-200"></span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-8 pb-28">
        <form id="booking-form" action="{{ route('booking.store') }}" method="POST" novalidate>
            @csrf

            <!-- STEP 1: PILIH LAYANAN -->
            <div id="step-1" class="step-panel space-y-6">
                <div class="border-b border-marble-200 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="font-heading text-2xl font-bold text-sage-900">Langkah 1: Pilih Layanan</h2>
                        <p class="text-xs text-ink-600">Bisa memilih lebih dari satu layanan dalam satu kali reservasi.</p>
                    </div>
                    <span id="selection-summary-text" class="text-xs font-bold text-sage-700">0 dipilih</span>
                </div>

                @error('service_ids')
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600">{{ $message }}</div>
                @enderror

                <div class="space-y-6">
                    @foreach ($categories as $cat)
                        <div class="bg-white rounded-2xl border border-marble-200 p-5 shadow-xs">
                            <h3 class="font-heading text-lg font-bold text-sage-900 mb-3 border-b border-marble-200/60 pb-2">
                                {{ $cat->name }}
                            </h3>
                            <div class="space-y-2">
                                @foreach ($cat->services as $srv)
                                    <label class="flex items-center justify-between p-3 rounded-xl border border-marble-200/80 hover:border-sage-300 hover:bg-cream-50 cursor-pointer transition text-xs sm:text-sm {{ ! $srv->is_available ? 'opacity-50 pointer-events-none' : '' }}">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="service_ids[]" value="{{ $srv->id }}"
                                                   data-price="{{ $srv->price }}" data-duration="{{ $srv->duration_minutes }}"
                                                   data-name="{{ $srv->name }}"
                                                   {{ in_array($srv->id, $preselected) || (is_array(old('service_ids')) && in_array($srv->id, old('service_ids'))) ? 'checked' : '' }}
                                                   onchange="updateSelection()"
                                                   class="service-checkbox w-4 h-4 rounded text-sage-700 focus:ring-gold-400">
                                            <div>
                                                <span class="font-semibold text-sage-900 block">{{ $srv->name }}</span>
                                                <span class="text-[11px] text-ink-600">{{ $srv->duration_minutes }} menit</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-sage-900">{{ rupiah($srv->price) }}</span>
                                            @if ($srv->price_max)
                                                <span class="block text-[10px] text-ink-600">mulai dari</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="button" onclick="goToStep(2)" class="bg-sage-700 hover:bg-sage-900 text-white font-semibold text-sm px-7 py-3 rounded-full transition shadow-sm">
                        Lanjut ke Pilih Staf &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 2: PILIH STAF -->
            <div id="step-2" class="step-panel space-y-6 hidden">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-2xl font-bold text-sage-900">Langkah 2: Pilih Staf</h2>
                    <p class="text-xs text-ink-600">Pilih staf favorit Anda, atau serahkan ke salon untuk memilihkan yang paling luang.</p>
                </div>

                <div id="staff-options-container" class="min-h-[140px] flex items-center justify-center">
                    <span class="text-xs text-ink-600 animate-pulse">Memuat pilihan staf...</span>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    <button type="button" onclick="goToStep(1)" class="text-xs font-semibold text-ink-600 hover:text-sage-700 px-4 py-2">
                        &larr; Kembali
                    </button>
                    <button type="button" onclick="goToStep(3)" class="bg-sage-700 hover:bg-sage-900 text-white font-semibold text-sm px-7 py-3 rounded-full transition shadow-sm">
                        Lanjut ke Pilih Jadwal &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 3: PILIH JADWAL -->
            <div id="step-3" class="step-panel space-y-6 hidden">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-2xl font-bold text-sage-900">Langkah 3: Pilih Tanggal & Jam</h2>
                    <p class="text-xs text-ink-600">Hanya menampilkan jam yang stafnya benar-benar luang.</p>
                </div>

                <div class="bg-white rounded-2xl border border-marble-200 p-5 space-y-4 shadow-xs">
                    <div>
                        <label for="booking_date" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">Pilih Tanggal</label>
                        <input type="date" id="booking_date" name="date" value="{{ old('date', $min) }}" min="{{ $min }}" max="{{ $max }}"
                               onchange="loadSlots()" required
                               class="w-full sm:w-60 px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-2">Jam Tersedia</label>
                        @error('time')
                            <div class="p-3 mb-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600">{{ $message }}</div>
                        @enderror
                        <div id="slots-options-container" class="min-h-[100px] flex items-center justify-center">
                            <span class="text-xs text-ink-600">Pilih tanggal terlebih dahulu untuk melihat jam luang.</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    <button type="button" onclick="goToStep(2)" class="text-xs font-semibold text-ink-600 hover:text-sage-700 px-4 py-2">
                        &larr; Kembali
                    </button>
                    <button type="button" onclick="goToStep(4)" class="bg-sage-700 hover:bg-sage-900 text-white font-semibold text-sm px-7 py-3 rounded-full transition shadow-sm">
                        Lanjut ke Isi Data &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 4: ISI DATA -->
            <div id="step-4" class="step-panel space-y-6 hidden">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-2xl font-bold text-sage-900">Langkah 4: Data Pemesan</h2>
                    <p class="text-xs text-ink-600">Data Anda aman dan hanya digunakan untuk konfirmasi reservasi salon.</p>
                </div>

                <div class="bg-white rounded-2xl border border-marble-200 p-6 space-y-4 shadow-xs">
                    <div>
                        <label for="customer_name" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                            Nama Lengkap <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="customer_name" name="name" value="{{ old('name') }}" placeholder="Contoh: Ibu Ratih Handayani" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                            Nomor WhatsApp / Telepon <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="customer_phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
                        <span class="text-[11px] text-ink-600 mt-0.5 block">Format 08xxx atau +628xxx (digunakan untuk mengirim kode & konfirmasi).</span>
                        @error('phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="customer_notes" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                            Catatan Khusus (opsional)
                        </label>
                        <textarea id="customer_notes" name="notes" rows="2" placeholder="Contoh: Kulit sensitif, atau permintaan khusus..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Persetujuan UU PDP Wajib (BO-06) -->
                    <div class="pt-2 border-t border-marble-200">
                        <label class="flex items-start gap-2.5 cursor-pointer text-xs text-ink-600">
                            <input type="checkbox" id="consent" name="consent" value="1" {{ old('consent') ? 'checked' : '' }} required
                                   class="mt-0.5 rounded text-sage-700 focus:ring-gold-400">
                            <span>
                                Saya menyetujui data nama dan nomor telepon saya disimpan oleh Eva Salon untuk keperluan konfirmasi jadwal reservasi sesuai <a href="{{ route('privacy') }}" target="_blank" class="text-sage-700 font-semibold underline">Kebijakan Privasi</a>.
                            </span>
                        </label>
                        @error('consent') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    <button type="button" onclick="goToStep(3)" class="text-xs font-semibold text-ink-600 hover:text-sage-700 px-4 py-2">
                        &larr; Kembali
                    </button>
                    <button type="button" onclick="goToStep(5)" class="bg-sage-700 hover:bg-sage-900 text-white font-semibold text-sm px-7 py-3 rounded-full transition shadow-sm">
                        Periksa & Konfirmasi &rarr;
                    </button>
                </div>
            </div>

            <!-- STEP 5: KONFIRMASI -->
            <div id="step-5" class="step-panel space-y-6 hidden">
                <div class="border-b border-marble-200 pb-3">
                    <h2 class="font-heading text-2xl font-bold text-sage-900">Langkah 5: Konfirmasi Reservasi</h2>
                    <p class="text-xs text-ink-600">Mohon periksa kembali detail pesanan Anda sebelum mengirim.</p>
                </div>

                <div class="bg-white rounded-2xl border border-marble-200 p-6 space-y-5 shadow-xs">
                    <div class="space-y-2 pb-4 border-b border-marble-200">
                        <span class="text-xs font-bold uppercase tracking-wider text-ink-600">Layanan yang Dipilih:</span>
                        <div id="summary-services-list" class="space-y-1 text-sm font-medium text-sage-900"></div>
                        <div class="flex items-center justify-between text-xs text-ink-600 pt-2">
                            <span>Estimasi Total Waktu: <strong id="summary-total-duration" class="text-sage-900">-</strong></span>
                            <span>Perkiraan Total Biaya: <strong id="summary-total-price" class="text-sage-900 text-base">-</strong></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs pb-4 border-b border-marble-200">
                        <div>
                            <span class="text-ink-600 block">Staf Pilihan:</span>
                            <span id="summary-staff-name" class="font-semibold text-sage-900 text-sm">-</span>
                        </div>
                        <div>
                            <span class="text-ink-600 block">Jadwal Kedatangan:</span>
                            <span id="summary-schedule" class="font-semibold text-sage-900 text-sm">-</span>
                        </div>
                    </div>

                    <div class="text-xs pb-2">
                        <span class="text-ink-600 block">Nama & Kontak Pemesan:</span>
                        <span id="summary-customer-info" class="font-semibold text-sage-900 text-sm block mt-0.5">-</span>
                    </div>

                    <div class="p-4 rounded-xl bg-sage-50 border border-sage-200 text-xs text-sage-900 space-y-1">
                        <div class="font-semibold flex items-center gap-1.5 text-sage-800">
                            <x-icon name="clock" class="w-4 h-4 text-sage-700" />
                            <span>Penting untuk Diketahui:</span>
                        </div>
                        <p class="text-[11px] text-ink-600 leading-relaxed">
                            Status awal booking adalah <strong>menunggu konfirmasi kasir</strong>. Slot Anda akan langsung terkunci. Pembatalan online dapat dilakukan hingga <strong>{{ $profile->cancel_limit_minutes }} menit</strong> sebelum jam mulai.
                        </p>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-between">
                    <button type="button" onclick="goToStep(4)" class="text-xs font-semibold text-ink-600 hover:text-sage-700 px-4 py-2">
                        &larr; Ubah Data
                    </button>
                    <button type="submit" id="btn-submit-booking" class="bg-sage-700 hover:bg-sage-900 text-white font-bold text-sm px-8 py-3.5 rounded-full transition shadow-md active:scale-95">
                        Kirim Reservasi Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Sticky Bottom Bar Mobile -->
    <div id="mobile-summary-bar" class="fixed bottom-0 inset-x-0 bg-white border-t border-marble-200 p-3 sm:hidden z-30 shadow-lg flex items-center justify-between">
        <div>
            <span id="mobile-bar-count" class="text-xs font-bold text-sage-900 block">0 Layanan Dipilih</span>
            <span id="mobile-bar-meta" class="text-[11px] text-ink-600">0 mnt &bull; Rp 0</span>
        </div>
        <button type="button" id="mobile-bar-next" onclick="onMobileBarNext()" class="bg-sage-700 text-white text-xs font-bold px-4 py-2 rounded-full shadow-xs">
            Lanjut
        </button>
    </div>

    <script>
        let currentStep = 1;
        let selectedStaffValue = 'any';
        let selectedSlotValue = '';

        function updateSelection() {
            const checked = document.querySelectorAll('.service-checkbox:checked');
            let totalPrice = 0;
            let totalDuration = 0;
            checked.forEach(el => {
                totalPrice += parseInt(el.dataset.price || 0);
                totalDuration += parseInt(el.dataset.duration || 0);
            });

            document.getElementById('selection-summary-text').innerText = `${checked.length} dipilih`;
            document.getElementById('mobile-bar-count').innerText = `${checked.length} Layanan Dipilih`;
            document.getElementById('mobile-bar-meta').innerText = `${totalDuration} mnt • Rp ${totalPrice.toLocaleString('id-ID')}`;
        }

        function goToStep(step) {
            const checked = document.querySelectorAll('.service-checkbox:checked');
            if (step > 1 && checked.length === 0) {
                alert('Silakan pilih minimal 1 layanan terlebih dahulu.');
                return;
            }

            if (step === 3 && currentStep === 2) {
                // proceed
            }

            if (step === 4 && (!selectedSlotValue || !document.getElementById('booking_date').value)) {
                alert('Silakan pilih tanggal dan jam yang tersedia terlebih dahulu.');
                return;
            }

            if (step === 5) {
                const name = document.getElementById('customer_name').value.trim();
                const phone = document.getElementById('customer_phone').value.trim();
                const consent = document.getElementById('consent');
                if (!name || !phone) {
                    alert('Mohon isi nama lengkap dan nomor telepon/WhatsApp.');
                    return;
                }
                if (!consent || !consent.checked) {
                    alert('Mohon centang persetujuan kebijakan privasi terlebih dahulu.');
                    consent?.focus();
                    return;
                }
                buildSummary();
            }

            // Update UI
            document.querySelectorAll('.step-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('step-' + step).classList.remove('hidden');

            document.querySelectorAll('.step-badge').forEach((b, i) => {
                if (i + 1 === step) {
                    b.classList.remove('bg-white', 'text-ink-600', 'border');
                    b.classList.add('bg-sage-700', 'text-white');
                } else if (i + 1 < step) {
                    b.classList.remove('bg-sage-700', 'text-white');
                    b.classList.add('bg-sage-100', 'text-sage-700', 'border', 'border-sage-300');
                } else {
                    b.classList.remove('bg-sage-700', 'text-white', 'bg-sage-100');
                    b.classList.add('bg-white', 'text-ink-600', 'border', 'border-marble-200');
                }
            });

            currentStep = step;
            window.scrollTo({ top: 120, behavior: 'smooth' });

            if (step === 2) {
                loadStaff();
            } else if (step === 3) {
                loadSlots();
            }
        }

        function onMobileBarNext() {
            if (currentStep < 5) {
                goToStep(currentStep + 1);
            }
        }

        function getSelectedServiceIds() {
            return Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => cb.value);
        }

        function loadStaff() {
            const ids = getSelectedServiceIds();
            const container = document.getElementById('staff-options-container');
            container.innerHTML = '<span class="text-xs text-ink-600 animate-pulse">Memuat staf yang sesuai...</span>';

            const params = new URLSearchParams();
            ids.forEach(id => params.append('services[]', id));
            params.append('selected', selectedStaffValue);

            fetch('{{ route('booking.staff') }}?' + params.toString())
                .then(r => r.text())
                .then(html => {
                    container.innerHTML = html;
                });
        }

        function onStaffSelected(val) {
            selectedStaffValue = val;
            if (currentStep === 3) {
                loadSlots();
            }
        }

        function loadSlots() {
            const ids = getSelectedServiceIds();
            const date = document.getElementById('booking_date').value;
            const container = document.getElementById('slots-options-container');
            if (!date) return;

            container.innerHTML = '<span class="text-xs text-ink-600 animate-pulse">Mengecek jadwal kosong...</span>';

            const params = new URLSearchParams();
            ids.forEach(id => params.append('services[]', id));
            params.append('date', date);
            params.append('staff', selectedStaffValue);
            params.append('selected', selectedSlotValue);

            fetch('{{ route('booking.slots') }}?' + params.toString())
                .then(r => r.text())
                .then(html => {
                    container.innerHTML = html;
                });
        }

        function onSlotSelected(val) {
            selectedSlotValue = val;
        }

        function buildSummary() {
            const checked = document.querySelectorAll('.service-checkbox:checked');
            const listContainer = document.getElementById('summary-services-list');
            listContainer.innerHTML = '';
            let totalPrice = 0;
            let totalDuration = 0;

            checked.forEach(el => {
                totalPrice += parseInt(el.dataset.price || 0);
                totalDuration += parseInt(el.dataset.duration || 0);
                const row = document.createElement('div');
                row.className = 'flex justify-between';
                row.innerHTML = `<span>&bull; ${el.dataset.name} (${el.dataset.duration}m)</span><span>Rp ${parseInt(el.dataset.price).toLocaleString('id-ID')}</span>`;
                listContainer.appendChild(row);
            });

            document.getElementById('summary-total-duration').innerText = `${totalDuration} Menit`;
            document.getElementById('summary-total-price').innerText = `Rp ${totalPrice.toLocaleString('id-ID')}`;

            // Staf
            const checkedStaff = document.querySelector('input[name="staff_id"]:checked');
            let staffLabel = 'Siapa saja';
            if (checkedStaff && checkedStaff.value !== 'any' && checkedStaff.value !== '') {
                staffLabel = checkedStaff.closest('label').querySelector('span').innerText;
            }
            document.getElementById('summary-staff-name').innerText = staffLabel;

            // Jadwal
            const dateVal = document.getElementById('booking_date').value;
            document.getElementById('summary-schedule').innerText = `${dateVal} pukul ${selectedSlotValue} WIB`;

            // Pelanggan
            const name = document.getElementById('customer_name').value;
            const phone = document.getElementById('customer_phone').value;
            document.getElementById('summary-customer-info').innerText = `${name} (${phone})`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateSelection();

            document.getElementById('booking-form').addEventListener('submit', function(e) {
                if (currentStep < 5) {
                    e.preventDefault();
                    goToStep(currentStep + 1);
                    return;
                }
                const consent = document.getElementById('consent');
                if (!consent || !consent.checked) {
                    e.preventDefault();
                    alert('Mohon centang persetujuan kebijakan privasi terlebih dahulu.');
                    goToStep(4);
                    consent?.focus();
                    return;
                }
            });
        });
    </script>
</x-layouts.public>
