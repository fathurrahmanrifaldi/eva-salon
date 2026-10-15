<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\TodayController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\CashierController;
use App\Http\Controllers\Owner\ProfileController;
use App\Http\Controllers\Owner\ServiceController;
use App\Http\Controllers\Owner\StaffController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kebijakan-privasi', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/layanan', [CatalogController::class, 'index'])->name('catalog');

Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::get('/booking/staff', [BookingController::class, 'staff'])->name('booking.staff');
Route::get('/booking/slots', [BookingController::class, 'slots'])->name('booking.slots');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/selesai', [BookingController::class, 'done'])->name('booking.done');

// Rate limit cek status: 10 per menit per IP (agent.md bagian 6.5)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/status', [StatusController::class, 'form'])->name('status.form');
    Route::post('/status', [StatusController::class, 'check'])->name('status.check');
    Route::post('/status/batal', [StatusController::class, 'cancel'])->name('status.cancel');
});

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ganti-password', [AuthController::class, 'editPassword'])->name('password.edit');
    Route::post('/ganti-password', [AuthController::class, 'updatePassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Dashboard Admin (Kasir & Owner)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'panel'])->prefix('admin')->group(function () {
    Route::redirect('/', '/admin/hari-ini')->name('admin.dashboard');

    Route::get('/hari-ini', [TodayController::class, 'index'])->name('admin.today');
    Route::get('/kalender', [CalendarController::class, 'index'])->name('admin.calendar');

    Route::post('/walk-in', [AdminBookingController::class, 'walkIn'])->name('admin.walkin');
    Route::post('/hold', [AdminBookingController::class, 'hold'])->name('admin.hold');

    Route::get('/booking/{booking}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
    Route::post('/booking/{booking}/konfirmasi', [AdminBookingController::class, 'confirm'])->name('admin.bookings.confirm');
    Route::post('/booking/{booking}/perpanjang', [AdminBookingController::class, 'extend'])->name('admin.bookings.extend');
    Route::post('/booking/{booking}/lepas', [AdminBookingController::class, 'release'])->name('admin.bookings.release');
    Route::post('/booking/{booking}/transisi/{to}', [AdminBookingController::class, 'transition'])->name('admin.bookings.transition');
    Route::post('/booking/{booking}/bayar', [AdminBookingController::class, 'pay'])->name('admin.bookings.pay');

    Route::get('/pelanggan', [CustomerController::class, 'index'])->name('admin.customers.index');
    Route::get('/pelanggan/{customer}', [CustomerController::class, 'show'])->name('admin.customers.show');
    Route::put('/pelanggan/{customer}', [CustomerController::class, 'update'])->name('admin.customers.update');
    Route::get('/api/pelanggan/lookup', [CustomerController::class, 'lookup'])->name('admin.customers.lookup');

    /*
    |----------------------------------------------------------------------
    | Fitur Khusus Owner (Otorisasi via Policy)
    |----------------------------------------------------------------------
    */
    Route::get('/layanan', [ServiceController::class, 'index'])->name('owner.services.index');
    Route::get('/layanan/tambah', [ServiceController::class, 'create'])->name('owner.services.create');
    Route::post('/layanan', [ServiceController::class, 'store'])->name('owner.services.store');
    Route::get('/layanan/{service}/edit', [ServiceController::class, 'edit'])->name('owner.services.edit');
    Route::put('/layanan/{service}', [ServiceController::class, 'update'])->name('owner.services.update');
    Route::post('/kategori', [ServiceController::class, 'storeCategory'])->name('owner.categories.store');
    Route::put('/kategori/{category}', [ServiceController::class, 'updateCategory'])->name('owner.categories.update');

    Route::get('/staf', [StaffController::class, 'index'])->name('owner.staff.index');
    Route::get('/staf/tambah', [StaffController::class, 'create'])->name('owner.staff.create');
    Route::post('/staf', [StaffController::class, 'store'])->name('owner.staff.store');
    Route::get('/staf/{staff}/edit', [StaffController::class, 'edit'])->name('owner.staff.edit');
    Route::put('/staf/{staff}', [StaffController::class, 'update'])->name('owner.staff.update');
    Route::post('/staf/{staff}/toggle', [StaffController::class, 'toggle'])->name('owner.staff.toggle');
    Route::post('/cuti', [StaffController::class, 'storeException'])->name('owner.exceptions.store');
    Route::delete('/cuti/{exception}', [StaffController::class, 'destroyException'])->name('owner.exceptions.destroy');

    Route::get('/kasir', [CashierController::class, 'index'])->name('owner.cashiers.index');
    Route::post('/kasir', [CashierController::class, 'store'])->name('owner.cashiers.store');
    Route::put('/kasir/{cashier}', [CashierController::class, 'update'])->name('owner.cashiers.update');
    Route::post('/kasir/{cashier}/toggle', [CashierController::class, 'toggle'])->name('owner.cashiers.toggle');
    Route::post('/kasir/{cashier}/reset-password', [CashierController::class, 'resetPassword'])->name('owner.cashiers.reset');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('owner.profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('owner.profile.update');
    Route::post('/galeri', [ProfileController::class, 'storeImage'])->name('owner.gallery.store');
    Route::delete('/galeri/{image}', [ProfileController::class, 'destroyImage'])->name('owner.gallery.destroy');
});
