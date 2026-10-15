<x-layouts.admin title="Ganti Password">
    <div class="max-w-md mx-auto bg-white rounded-3xl border border-marble-200 p-8 shadow-xs">
        <h1 class="font-heading text-2xl font-bold text-sage-900 mb-1">Ganti Password</h1>
        <p class="text-xs text-ink-600 mb-6">Untuk keamanan akun Anda, silakan ubah password Anda.</p>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                    Password Saat Ini / Sementara
                </label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                    Password Baru (Min. 8 Karakter)
                </label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">
                    Ulangi Password Baru
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
            </div>

            <button type="submit" class="w-full bg-sage-700 hover:bg-sage-900 text-white font-bold py-3 rounded-full text-xs uppercase tracking-wider transition shadow-sm">
                Simpan Password Baru
            </button>
        </form>
    </div>
</x-layouts.admin>
