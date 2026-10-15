@if (empty($slots))
    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
        Tidak ada slot kosong pada tanggal ini untuk staf yang dipilih. Silakan pilih tanggal lain atau pilih opsi <strong>"Siapa saja"</strong>.
    </div>
@else
    <div class="flex flex-wrap gap-2.5">
        @foreach ($slots as $slot)
            <label class="cursor-pointer">
                <input type="radio" name="time" value="{{ $slot }}" {{ $selected === $slot ? 'checked' : '' }}
                       onchange="onSlotSelected(this.value)" class="sr-only peer" required>
                <span class="inline-block px-4 py-2 rounded-full border border-marble-200 bg-white text-xs font-bold text-ink-900 transition peer-checked:bg-sage-700 peer-checked:text-white peer-checked:border-sage-700 hover:border-sage-400">
                    {{ $slot }} WIB
                </span>
            </label>
        @endforeach
    </div>
@endif
