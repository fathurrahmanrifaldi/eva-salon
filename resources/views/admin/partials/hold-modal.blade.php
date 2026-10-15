@php
    $allServices = \App\Models\Service::where('is_available', true)->orderBy('sort_order')->get();
    $allStaff = \App\Models\Staff::active()->orderBy('sort_order')->get();
    $todayDate = \App\Support\Tz::local(\App\Support\Tz::now())->toDateString();
    $profile = \App\Models\SalonProfile::current();
@endphp

<div id="hold-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl overflow-y-auto max-h-[90vh] border border-marble-200">
        <div class="flex items-center justify-between pb-3 border-b border-marble-200">
            <div>
                <h3 class="font-heading text-2xl font-semibold text-sage-900">Tahan Slot via Telepon</h3>
                <p class="text-xs text-ink-600">Kunci jadwal sementara sampai konfirmasi pelanggan tiba</p>
            </div>
            <button type="button" onclick="closeHoldModal()" class="text-ink-600 hover:text-ink-900 p-1 rounded-lg hover:bg-sage-50">
                <x-icon name="x-mark" class="w-6 h-6" />
            </button>
        </div>

        <form action="{{ route('admin.hold') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <!-- Layanan -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-2">Pilih Layanan</label>
                @error('service_ids', 'hold')
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

            <!-- Staf & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="hold_staff_id" class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">Staf</label>
                    <select name="staff_id" id="hold_staff_id" class="w-full px-3 py-2 border border-marble-200 rounded-xl text-xs focus:ring-2 focus:ring-sage-700">
                        <option value="">Siapa saja</option>
                        @foreach ($allStaff as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="hold_date" class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">Tanggal</label>
                    <input type="date" id="hold_date" name="date" value="{{ old('date', $todayDate) }}" required
                           class="w-full px-3 py-2 border border-marble-200 rounded-xl text-xs focus:ring-2 focus:ring-sage-700">
                </div>
            </div>

            <!-- Jam Mulai Layanan -->
            <div>
                <label for="hold_time" class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">Jam Booking</label>
                <input type="time" id="hold_time" name="time" value="{{ old('time', '10:00') }}" required
                       class="w-full px-3 py-2 border border-marble-200 rounded-xl text-xs focus:ring-2 focus:ring-sage-700">
            </div>

            <!-- Data Pemesan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="hold_name" class="block text-xs font-medium text-ink-600 mb-1">Nama Pemesan</label>
                    <input type="text" id="hold_name" name="name" value="{{ old('name') }}" placeholder="Mbak Dewi" required
                           class="w-full px-3 py-2 border border-marble-200 rounded-xl text-xs focus:ring-2 focus:ring-sage-700">
                </div>
                <div>
                    <label for="hold_phone" class="block text-xs font-medium text-ink-600 mb-1">Nomor Telepon</label>
                    <input type="text" id="hold_phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required
                           class="w-full px-3 py-2 border border-marble-200 rounded-xl text-xs focus:ring-2 focus:ring-sage-700">
                </div>
            </div>

            <!-- Durasi Tahan Slot -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-ink-600 mb-1">Tahan Sampai</label>
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="hold_minutes" value="{{ $profile->hold_default_minutes }}" checked class="text-sage-700 focus:ring-gold-400">
                        <span>{{ $profile->hold_default_minutes }} Menit (standar)</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="hold_minutes" value="60" class="text-sage-700 focus:ring-gold-400">
                        <span>1 Jam</span>
                    </label>
                </div>
            </div>

            <!-- Catatan -->
            <div>
                <label for="hold_notes" class="block text-xs font-medium text-ink-600 mb-1">Catatan Tambahan (opsional)</label>
                <input type="text" id="hold_notes" name="notes" value="{{ old('notes') }}" placeholder="Minta dikabari jika bisa dimajukan..."
                       class="w-full px-3 py-2 border border-marble-200 rounded-xl text-xs focus:ring-2 focus:ring-sage-700">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-marble-200">
                <button type="button" onclick="closeHoldModal()" class="px-4 py-2 rounded-xl text-xs font-medium text-ink-600 hover:bg-cream-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-sage-700 hover:bg-sage-900 shadow transition">
                    Tahan Slot Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openHoldModal(date = null, time = null, staffId = null) {
        if (date) document.getElementById('hold_date').value = date;
        if (time) document.getElementById('hold_time').value = time;
        if (staffId) document.getElementById('hold_staff_id').value = staffId;
        document.getElementById('hold-modal').classList.remove('hidden');
    }
    function closeHoldModal() {
        document.getElementById('hold-modal').classList.add('hidden');
    }

    @if ($errors->hasBag('hold'))
        document.addEventListener('DOMContentLoaded', () => openHoldModal());
    @endif
</script>
