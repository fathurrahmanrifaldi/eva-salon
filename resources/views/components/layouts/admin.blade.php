<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} &bull; Eva Salon Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream-50 text-ink-900 font-sans antialiased min-h-screen flex flex-col">
    @php
        $pendingCount = \App\Models\Booking::where('status', \App\Enums\BookingStatus::Pending->value)->count();
    @endphp

    <!-- Topbar Dashboard -->
    <header class="bg-white border-b border-marble-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <!-- Brand & Navigasi Utama -->
            <div class="flex items-center gap-6">
                <a href="{{ route('admin.today') }}" class="flex items-center gap-2">
                    <span class="font-heading font-semibold text-xl text-sage-900">Eva Salon</span>
                    <span class="text-[11px] font-semibold tracking-wider uppercase px-2 py-0.5 rounded-full bg-sage-100 text-sage-700">
                        {{ auth()->user()->role->label() }}
                    </span>
                </a>

                <nav class="hidden lg:flex items-center gap-1 text-xs font-semibold">
                    <a href="{{ route('admin.today') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.today*') ? 'bg-sage-100 text-sage-900' : 'text-ink-600 hover:text-sage-700 hover:bg-cream-100' }}">
                        Hari Ini
                    </a>
                    <a href="{{ route('admin.calendar') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.calendar*') ? 'bg-sage-100 text-sage-900' : 'text-ink-600 hover:text-sage-700 hover:bg-cream-100' }}">
                        Kalender
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.customers*') ? 'bg-sage-100 text-sage-900' : 'text-ink-600 hover:text-sage-700 hover:bg-cream-100' }}">
                        Data Pelanggan
                    </a>

                    @if (auth()->user()->isOwner())
                        <span class="h-4 w-px bg-marble-200 mx-1"></span>
                        <a href="{{ route('owner.services.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('owner.services*') ? 'bg-sage-100 text-sage-900' : 'text-ink-600 hover:text-sage-700 hover:bg-cream-100' }}">
                            Layanan
                        </a>
                        <a href="{{ route('owner.staff.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('owner.staff*') ? 'bg-sage-100 text-sage-900' : 'text-ink-600 hover:text-sage-700 hover:bg-cream-100' }}">
                            Staf & Cuti
                        </a>
                        <a href="{{ route('owner.cashiers.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('owner.cashiers*') ? 'bg-sage-100 text-sage-900' : 'text-ink-600 hover:text-sage-700 hover:bg-cream-100' }}">
                            Akun Kasir
                        </a>
                        <a href="{{ route('owner.profile.edit') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('owner.profile*') ? 'bg-sage-100 text-sage-900' : 'text-ink-600 hover:text-sage-700 hover:bg-cream-100' }}">
                            Profil & Jam
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Tombol Cepat Walk-In, Tahan Slot, dan Notif -->
            <div class="flex items-center gap-2 sm:gap-3">
                @if ($pendingCount > 0)
                    <a href="{{ route('admin.today', ['scope' => 'pending']) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-xs font-semibold hover:bg-amber-200 transition"
                       title="Lihat antrean booking yang menunggu konfirmasi kasir">
                        <x-icon name="clock" class="w-4 h-4 text-amber-700" />
                        <span>Perlu Konfirmasi: <strong>{{ $pendingCount }}</strong></span>
                    </a>
                @endif

                <button type="button" onclick="openWalkInModal()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sage-700 hover:bg-sage-900 text-white text-xs font-bold shadow-sm transition active:scale-95"
                        title="Catat walk-in (target 15 detik)">
                    <span>+ Walk-in</span>
                </button>

                <button type="button" onclick="openHoldModal()"
                        class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-sage-700 text-sage-700 hover:bg-sage-50 text-xs font-bold transition active:scale-95"
                        title="Tahan slot booking via telepon">
                    <span>+ Tahan Slot</span>
                </button>

                <!-- User Dropdown & Logout -->
                <div class="flex items-center gap-2 border-l border-marble-200 pl-3">
                    <span class="text-xs font-medium text-ink-900 hidden sm:inline">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-ink-600 hover:text-red-700 px-2 py-1.5 rounded hover:bg-red-50 transition" title="Keluar">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subnav Mobile/Tablet -->
        <div class="lg:hidden flex items-center gap-2 px-4 py-2 border-t border-marble-200 overflow-x-auto text-xs font-semibold bg-cream-50">
            <a href="{{ route('admin.today') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.today*') ? 'bg-sage-700 text-white' : 'text-ink-600' }}">Hari Ini</a>
            <a href="{{ route('admin.calendar') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.calendar*') ? 'bg-sage-700 text-white' : 'text-ink-600' }}">Kalender</a>
            <a href="{{ route('admin.customers.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('admin.customers*') ? 'bg-sage-700 text-white' : 'text-ink-600' }}">Pelanggan</a>
            @if (auth()->user()->isOwner())
                <a href="{{ route('owner.services.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('owner.services*') ? 'bg-sage-700 text-white' : 'text-ink-600' }}">Layanan</a>
                <a href="{{ route('owner.staff.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('owner.staff*') ? 'bg-sage-700 text-white' : 'text-ink-600' }}">Staf</a>
                <a href="{{ route('owner.cashiers.index') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('owner.cashiers*') ? 'bg-sage-700 text-white' : 'text-ink-600' }}">Kasir</a>
                <a href="{{ route('owner.profile.edit') }}" class="px-2.5 py-1 rounded {{ request()->routeIs('owner.profile*') ? 'bg-sage-700 text-white' : 'text-ink-600' }}">Profil</a>
            @endif
        </div>
    </header>

    <!-- Konten Dashboard -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6">
        <x-flash />
        {{ $slot }}
    </main>

    <!-- Include Modal Global -->
    @include('admin.partials.walk-in-modal')
    @include('admin.partials.hold-modal')
</body>
</html>
