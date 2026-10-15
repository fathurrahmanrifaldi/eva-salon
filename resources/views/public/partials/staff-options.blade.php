<div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
    <!-- Opsi "Siapa Saja" -->
    <label class="flex items-start gap-2.5 p-3 rounded-2xl border cursor-pointer transition {{ $selected === 'any' ? 'border-sage-700 bg-sage-50 ring-1 ring-sage-700' : 'border-marble-200 bg-white hover:border-sage-300' }}">
        <input type="radio" name="staff_id" value="any" {{ $selected === 'any' ? 'checked' : '' }}
               onchange="onStaffSelected(this.value)" class="mt-1 text-sage-700 focus:ring-gold-400">
        <div>
            <span class="block text-xs font-bold text-sage-900">Siapa saja</span>
            <span class="text-[11px] text-ink-600 leading-tight">Pilih otomatis staf yang paling luang</span>
        </div>
    </label>

    @foreach ($staff as $st)
        <label class="flex items-start gap-2.5 p-3 rounded-2xl border cursor-pointer transition {{ (string)$selected === (string)$st->id ? 'border-sage-700 bg-sage-50 ring-1 ring-sage-700' : 'border-marble-200 bg-white hover:border-sage-300' }}">
            <input type="radio" name="staff_id" value="{{ $st->id }}" {{ (string)$selected === (string)$st->id ? 'checked' : '' }}
                   onchange="onStaffSelected(this.value)" class="mt-1 text-sage-700 focus:ring-gold-400">
            <div>
                <span class="block text-xs font-bold text-sage-900">{{ $st->name }}</span>
                <span class="text-[11px] text-ink-600 block">{{ $st->position ?? 'Terapis' }}</span>
                @if ($st->gender === 'female')
                    <span class="inline-block mt-0.5 text-[10px] text-sage-700 font-semibold bg-sage-100 px-1.5 py-0.2 rounded">Perempuan</span>
                @endif
            </div>
        </label>
    @endforeach
</div>

@if ($staff->isEmpty())
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
        Belum ada staf yang terdaftar untuk semua kombinasi layanan yang dipilih. Silakan pilih layanan lain atau hubungi kami via WhatsApp.
    </div>
@endif
