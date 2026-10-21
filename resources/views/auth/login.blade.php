<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Staf & Kasir &bull; Eva Salon</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 text-ink-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl border border-marble-200 p-8 shadow-soft">
        <div class="text-center mb-6">
            <span class="font-heading text-3xl font-bold text-sage-900 block">Eva Salon</span>
            <span class="text-xs uppercase tracking-widest text-gold-700 font-semibold">Portal Kasir & Owner</span>
        </div>

        <x-flash />

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="login_email" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">Email</label>
                <input type="email" id="login_email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
            </div>

            <div>
                <label for="login_password" class="block text-xs font-bold uppercase tracking-wider text-ink-600 mb-1">Password</label>
                <input type="password" id="login_password" name="password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-marble-200 text-sm focus:ring-2 focus:ring-sage-700">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-ink-600">
                    <input type="checkbox" name="remember" class="rounded text-sage-700 focus:ring-gold-400">
                    <span>Ingat saya</span>
                </label>
                <a href="{{ route('home') }}" class="text-sage-700 hover:underline">Kembali ke Beranda</a>
            </div>

            <button type="submit" class="w-full bg-sage-700 hover:bg-sage-900 text-white font-bold py-3 rounded-full text-xs uppercase tracking-wider transition shadow-sm">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-marble-200 text-center text-xs text-ink-600">
            <p>Demo kredensial:</p>
            <p class="font-mono text-[11px] text-sage-900">kasir@evasalon.test / password</p>
            <p class="font-mono text-[11px] text-sage-900 mt-1">owner@hash / all fitur</p>
        </div>
    </div>
</body>
</html>
