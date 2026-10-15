@php
    $allServices = \App\Models\Service::where('is_available', true)->orderBy('sort_order')->get();
    $allStaff = \App\Models\Staff::active()->orderBy('sort_order')->get();
    $nowLocal = \App\Support\Tz::local(\App\Support\Tz::now());
    $roundedMin = (int) (ceil($nowLocal->minute / 5) * 5);
    $defaultTime = $nowLocal->copy()->setMinute($roundedMin % 60)->format('H:i');
@endphp

<div id="walkin-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl overflow-y-auto max-h-[90vh] border border-marble-200">
        <div class="flex items-center justify-between pb-3 border-b border-marble-200">
            <div>
                <h3 class="font-heading text-2xl font-semibold text-sage-900">Walk-in Baru</h3>
                <p class="text-xs text-ink-600">Pencatatan cepat tamu langsung datang (langsung terkonfirmasi)</p>
            </div>
            <button type="button" onclick="closeWalkInModal()" class="text-ink-600 hover:text-ink-900 p-1 rounded-lg hover:bg-sage-50">
                <x-icon name="x-mark" class="w-6 h-6" />
            </button>
        </div>

        <form action="{{ route('admin.walkin') }}" method="POST" class="mt-4 space-y-5">
            @csrf

            <!-- 1. Pilih Layanan -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-2">1. Pilih Layanan</label>
                @error('service_ids', 'walkin')
                    <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
                @enderror
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-40 overflow-y-auto p-2 border border-marble-200 rounded-xl bg-cream-50">
                    @foreach ($allServices as $s)
                        <label class="flex items-start gap-2 p-2 rounded-lg border border-marble-200 bg-white hover:border-sage-300 cursor-pointer text-xs transition">
                            <input type="checkbox" name="service_ids[]" value="{{ $s->id }}"
                                   {{ is_array(old('service_ids')) && in_array($s->id, old('service_ids')) ? 'checked' : '' }}
                                   class="mt-0.5 rounded text-sage-700 focus:ring-gold-400">
                            <div>
                                <div class="font-medium text-ink-900">{{ $s->name }}</div>
                                <div class="text-[11px] text-ink-600">{{ $s->duration_minutes }}m &bull; {{ rupiah($s->price) }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 2. Pilih Staf -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-2">2. Staf</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="flex items-center gap-2 p-2 rounded-lg border border-marble-200 bg-white hover:border-sage-300 cursor-pointer text-xs">
                        <input type="radio" name="staff_id" value="" {{ old('staff_id') === null ? 'checked' : '' }} class="text-sage-700 focus:ring-gold-400">
                        <span class="font-semibold text-sage-900">Siapa saja</span>
                    </label>
                    @foreach ($allStaff as $st)
                        <label class="flex items-center gap-2 p-2 rounded-lg border border-marble-200 bg-white hover:border-sage-300 cursor-pointer text-xs">
                            <input type="radio" name="staff_id" value="{{ $st->id }}" {{ (string)old('staff_id') === (string)$st->id ? 'checked' : '' }} class="text-sage-700 focus:ring-gold-400">
                            <span class="truncate text-ink-900">{{ $st->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- 3. Jam Mulai -->
            <div>
                <label for="walkin_start_time" class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">3. Jam Mulai Hari Ini</label>
                <input type="time" id="walkin_start_time" name="start_time" value="{{ old('start_time', $defaultTime) }}" required
                       class="w-full sm:w-40 px-3 py-2 border border-marble-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sage-700">
                @error('start_time', 'walkin')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Data Pelanggan (Nama Opsional untuk Walk-in) -->
            <div class="space-y-3 pt-2 border-t border-marble-200">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="walkin_phone" class="block text-xs font-medium text-ink-600 mb-1">No. Telepon (opsional)</label>
                        <input type="text" id="walkin_phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                               onkeyup="lookupWalkinCustomer(this.value)"
                               class="w-full px-3 py-2 border border-marble-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sage-700">
                        <div id="walkin_lookup_results" class="hidden mt-1 p-2 bg-sage-50 border border-sage-300 rounded-lg text-xs space-y-1"></div>
                    </div>
                    <div>
                        <label for="walkin_name" class="block text-xs font-medium text-ink-600 mb-1">Nama Pelanggan (default: Walk-in)</label>
                        <input type="text" id="walkin_name" name="name" value="{{ old('name') }}" placeholder="Misal: Bu Ayu"
                               class="w-full px-3 py-2 border border-marble-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sage-700">
                    </div>
                </div>

                <div>
                    <label for="walkin_notes" class="block text-xs font-medium text-ink-600 mb-1">Catatan Tambahan (opsional)</label>
                    <input type="text" id="walkin_notes" name="notes" value="{{ old('notes') }}" placeholder="Permintaan khusus..."
                           class="w-full px-3 py-2 border border-marble-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-sage-700">
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-marble-200">
                <button type="button" onclick="closeWalkInModal()" class="px-4 py-2 rounded-xl text-sm font-medium text-ink-600 hover:bg-cream-100">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-sage-700 hover:bg-sage-900 shadow transition">
                    Simpan & Mulai Layanan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openWalkInModal() {
        document.getElementById('walkin-modal').classList.remove('hidden');
    }
    function closeWalkInModal() {
        document.getElementById('walkin-modal').classList.add('hidden');
    }

    let walkinLookupTimeout = null;
    function lookupWalkinCustomer(val) {
        clearTimeout(walkinLookupTimeout);
        const container = document.getElementById('walkin_lookup_results');
        if (!val || val.length < 3) {
            container.classList.add('hidden');
            container.innerHTML = '';
            return;
        }
        walkinLookupTimeout = setTimeout(() => {
            fetch('{{ route('admin.customers.lookup') }}?q=' + encodeURIComponent(val))
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        container.innerHTML = '<span class="font-semibold text-sage-900 block mb-1">Ditemukan riwayat pelanggan:</span>' +
                            data.map(c => `
                                <div class="flex items-center justify-between hover:bg-white p-1 rounded cursor-pointer" onclick="selectWalkinCustomer('${c.name}', '${c.phone || ''}')">
                                    <span><strong>${c.name}</strong> (${c.visits} kunjungan)</span>
                                    <span class="text-sage-700 font-bold underline text-[11px]">Pilih</span>
                                </div>
                            `).join('');
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                });
        }, 300);
    }

    function selectWalkinCustomer(name, phone) {
        document.getElementById('walkin_name').value = name;
        if (phone) document.getElementById('walkin_phone').value = phone;
        document.getElementById('walkin_lookup_results').classList.add('hidden');
    }

    @if ($errors->hasBag('walkin'))
        document.addEventListener('DOMContentLoaded', () => openWalkInModal());
    @endif
</script>
