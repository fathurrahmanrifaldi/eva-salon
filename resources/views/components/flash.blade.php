@if (session('success'))
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-sage-300 bg-sage-50 px-4 py-3 text-sm text-sage-900 shadow-sm" role="alert">
        <x-icon name="check" class="h-5 w-5 text-sage-700 flex-shrink-0" />
        <div class="flex-1">{{ session('success') }}</div>
    </div>
@endif

@if (session('error'))
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 shadow-sm" role="alert">
        <x-icon name="x-mark" class="h-5 w-5 text-red-600 flex-shrink-0" />
        <div class="flex-1">{{ session('error') }}</div>
    </div>
@endif

@if (session('temp_password'))
    <div class="mb-4 rounded-xl border border-gold-400 bg-cream-100 p-4 text-sm text-ink-900 shadow-sm">
        <p class="font-semibold text-gold-700">Akun baru berhasil dibuat / password direset:</p>
        <p class="mt-1 font-mono text-xs text-ink-900">Email: <span class="font-bold">{{ session('temp_password')[0] }}</span></p>
        <p class="font-mono text-xs text-ink-900">Password Sementara: <span class="rounded bg-white px-2 py-0.5 font-bold text-sage-900">{{ session('temp_password')[1] }}</span></p>
        <p class="mt-1 text-xs text-ink-600">Harap salin password ini sekarang. Kasir wajib menggantinya saat pertama kali login.</p>
    </div>
@endif
