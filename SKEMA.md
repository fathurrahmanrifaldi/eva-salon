# Skema Database: Sistem Booking Salon

Skema ini ditulis sebagai migration Laravel untuk MySQL 8. Ada beberapa keputusan yang berbeda dari wireframe sebelumnya, dan alasannya saya tulis di bagian pertama.

## A. Keputusan Desain Utama

1. **`staff_id` wajib diisi di `bookings`.** Di wireframe ada kolom "Tanpa staf", tetapi pengecekan bentrok bergantung pada staf. Booking "Siapa saja" harus diberi staf saat disimpan. Sistem memilih staf yang paling sesuai, lalu menyimpannya.
2. **Hold dan booking berada di satu tabel `bookings`** dengan status `hold`. Dengan begitu, semua pengecekan slot cukup membaca satu sumber. Hold kedaluwarsa dianggap tidak aktif jika `hold_until` sudah lewat, tanpa perlu cron.
3. **Dua waktu blok per booking:** `start_at` (mulai layanan) dan `blocked_until` (selesai layanan + buffer). Overlap dihitung terhadap `blocked_until`, bukan `end_at`.
4. **MySQL tidak bisa menegakkan larangan overlap waktu lewat constraint biasa.** Unique constraint hanya mencegah nilai yang sama, bukan rentang yang bertabrakan. Karena itu, pengecekan dilakukan di transaksi dengan `lockForUpdate()` pada baris staf (lihat bagian D).
5. **Harga dan durasi disalin ke `booking_items`** agar perubahan katalog tidak mengubah riwayat.
6. **Tidak ada hard delete.** Semua foreign key memakai `restrictOnDelete`, dan pembatalan dicatat sebagai status.

## B. Ringkasan Relasi

```
users ─────────────────────────────┐ (created_by, recorded_by, changed_by)
customers 1──N bookings N──1 staff
bookings 1──N booking_items N──1 services N──N staff (service_staff)
bookings 1──N booking_status_logs
bookings 1──N payments
staff 1──N staff_schedules
staff 1──N schedule_exceptions (staff_id nullable = seluruh salon)
service_categories 1──N services
salon_profiles (1 baris), business_hours (7 baris), gallery_images
```

## C. Migration

Urutan file penting karena ada foreign key. Nama file di bawah bisa disesuaikan, tetapi urutannya sebaiknya dijaga.

### 1. `2026_10_15_000001_create_users_table.php`

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['owner', 'kasir']);
    $table->boolean('is_active')->default(true);
    $table->boolean('must_change_password')->default(false);
    $table->rememberToken();
    $table->timestamps();
});
```

Tabel `password_reset_tokens` dan `sessions` mengikuti default Laravel (`php artisan session:table`).

### 2. `2026_10_15_000002_create_salon_profiles_table.php`

```php
Schema::create('salon_profiles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->text('address');
    $table->string('phone', 20)->nullable();
    $table->string('wa_number', 20);              // format +62, sumber tombol wa.me
    $table->text('map_embed')->nullable();
    $table->string('operating_note')->nullable();

    // Aturan bisnis yang bisa diatur owner
    $table->unsignedSmallInteger('cancel_limit_minutes')->default(30);
    $table->unsignedSmallInteger('hold_default_minutes')->default(30);
    $table->unsignedSmallInteger('hold_max_minutes')->default(120);
    $table->unsignedSmallInteger('pending_alert_hours')->default(24);
    $table->boolean('require_payment_to_complete')->default(false);

    $table->timestamps();
});
```

### 3. `2026_10_15_000003_create_business_hours_table.php`

```php
Schema::create('business_hours', function (Blueprint $table) {
    $table->id();
    $table->unsignedTinyInteger('day_of_week')->unique(); // 0 = Minggu ... 6 = Sabtu
    $table->time('open_time')->nullable();
    $table->time('close_time')->nullable();
    $table->boolean('is_closed')->default(false);
    $table->timestamps();
});
```

### 4. `2026_10_15_000004_create_customers_table.php`

```php
Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone', 20)->nullable()->unique(); // format +62, NULL diizinkan berkali-kali
    $table->string('email')->nullable();
    $table->timestamp('consent_at')->nullable();      // persetujuan UU PDP
    $table->timestamp('last_visit_at')->nullable();   // dasar tinjauan retensi 24 bulan
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index('name');
});
```

### 5. `2026_10_15_000005_create_staff_table.php`

```php
Schema::create('staff', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('position')->nullable();  // kapster, terapis, dll.
    $table->string('phone', 20)->nullable();
    $table->string('photo_path')->nullable();
    $table->boolean('is_active')->default(true); // nonaktif, tidak dihapus
    $table->unsignedSmallInteger('sort_order')->default(0);
    $table->timestamps();
});
```

### 6. `2026_10_15_000006_create_staff_schedules_table.php`

```php
Schema::create('staff_schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();
    $table->unsignedTinyInteger('day_of_week');
    $table->time('start_time');
    $table->time('end_time');
    $table->timestamps();

    $table->index(['staff_id', 'day_of_week']);
});
```

Satu staf bisa memiliki beberapa baris per hari untuk shift terpisah.

### 7. `2026_10_15_000007_create_schedule_exceptions_table.php`

```php
Schema::create('schedule_exceptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('staff_id')->nullable()->constrained('staff')->restrictOnDelete();
    $table->date('starts_on');
    $table->date('ends_on');
    $table->enum('type', ['salon_closed', 'staff_leave']);
    $table->string('note')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
    $table->timestamps();

    $table->index(['starts_on', 'ends_on']);
});
```

Aturan: `salon_closed` dengan `staff_id` NULL berarti seluruh salon tutup. `staff_leave` wajib memiliki `staff_id`. Validasi ini dilakukan di form request, karena MySQL tidak mendukung check constraint lintas kolom yang nyaman di semua versi.

### 8. `2026_10_15_000008_create_service_categories_table.php`

```php
Schema::create('service_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedSmallInteger('sort_order')->default(0);
    $table->timestamps();
});
```

### 9. `2026_10_15_000009_create_services_table.php`

```php
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained('service_categories')->restrictOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->unsignedInteger('price');                 // Rupiah, bilangan bulat
    $table->unsignedSmallInteger('duration_minutes');
    $table->unsignedSmallInteger('buffer_minutes')->default(0);
    $table->string('image_path')->nullable();
    $table->boolean('is_available')->default(true);
    $table->boolean('is_featured')->default(false);
    $table->unsignedSmallInteger('sort_order')->default(0);
    $table->timestamps();

    $table->index(['is_available', 'is_featured']);
});
```

### 10. `2026_10_15_000010_create_service_staff_table.php`

```php
Schema::create('service_staff', function (Blueprint $table) {
    $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
    $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();
    $table->primary(['service_id', 'staff_id']);  // unik sekaligus indeks
});
```

### 11. `2026_10_15_000011_create_bookings_table.php`

```php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->char('booking_code', 8)->unique();    // acak, tanpa 0/O/1/I

    $table->foreignId('customer_id')->nullable()->constrained('customers')->restrictOnDelete();
    $table->string('customer_name');              // snapshot, wajib
    $table->string('customer_phone', 20)->nullable();

    $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();

    $table->dateTime('start_at');                 // UTC
    $table->dateTime('end_at');                   // selesai layanan
    $table->dateTime('blocked_until');            // end_at + buffer, dipakai untuk overlap

    $table->enum('status', [
        'hold', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show',
    ])->default('pending');
    $table->enum('source', ['online', 'walk_in', 'phone_hold']);

    $table->dateTime('hold_until')->nullable();   // hanya untuk status hold

    $table->unsignedInteger('total_price');
    $table->unsignedSmallInteger('total_duration');
    $table->text('notes')->nullable();

    $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
    $table->foreignId('confirmed_by')->nullable()->constrained('users')->restrictOnDelete();
    $table->timestamp('consent_at')->nullable();  // dicatat dari form online
    $table->timestamps();

    // Indeks untuk pengecekan bentrok dan tampilan kalender
    $table->index(['staff_id', 'start_at']);
    $table->index(['staff_id', 'blocked_until']);
    $table->index(['status', 'start_at']);
    $table->index('customer_id');
    $table->index('hold_until');
});
```

Catatan: `customer_id` nullable karena walk-in tanpa nomor telepon tetap boleh dicatat.

### 12. `2026_10_15_000012_create_booking_items_table.php`

```php
Schema::create('booking_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
    $table->foreignId('service_id')->constrained('services')->restrictOnDelete();

    // Snapshot saat booking dibuat
    $table->string('service_name');
    $table->unsignedInteger('price_snapshot');
    $table->unsignedSmallInteger('duration_snapshot');
    $table->unsignedSmallInteger('buffer_snapshot');
    $table->unsignedTinyInteger('sequence');      // urutan layanan dalam satu booking

    $table->timestamps();

    $table->unique(['booking_id', 'sequence']);
});
```

### 13. `2026_10_15_000013_create_booking_status_logs_table.php`

```php
Schema::create('booking_status_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
    $table->enum('from_status', ['hold', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->nullable();
    $table->enum('to_status', ['hold', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show']);
    $table->enum('actor_type', ['customer', 'kasir', 'owner', 'system']);
    $table->foreignId('changed_by')->nullable()->constrained('users')->restrictOnDelete();
    $table->string('reason')->nullable();          // wajib untuk cancel dan no_show di level aplikasi
    $table->timestamp('created_at')->useCurrent(); // tanpa updated_at, log bersifat append-only

    $table->index(['booking_id', 'created_at']);
});
```

### 14. `2026_10_15_000014_create_payments_table.php`

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
    $table->integer('amount');                     // boleh negatif untuk koreksi
    $table->enum('method', ['cash', 'transfer', 'qris']);
    $table->enum('kind', ['payment', 'correction'])->default('payment');
    $table->dateTime('paid_at');
    $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
    $table->string('note')->nullable();
    $table->timestamp('created_at')->useCurrent();

    $table->index('booking_id');
});
```

Sesuai PRD, pembayaran tidak dihapus. Koreksi dilakukan dengan baris baru bertipe `correction`.

### 15. `2026_10_15_000015_create_gallery_images_table.php`

```php
Schema::create('gallery_images', function (Blueprint $table) {
    $table->id();
    $table->string('path');
    $table->string('caption')->nullable();
    $table->unsignedSmallInteger('sort_order')->default(0);
    $table->timestamps();
});
```

## D. Logika Pengecekan Bentrok (inti sistem)

Ini bagian paling penting. Semua pembuatan booking dan hold harus melewati method seperti ini.

```php
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

public function reserveSlot(
    int $staffId,
    Carbon $startAt,          // UTC
    int $durationMinutes,
    int $bufferMinutes,
    callable $persist         // fungsi yang menyimpan booking di dalam transaksi
): void {
    DB::transaction(function () use ($staffId, $startAt, $durationMinutes, $bufferMinutes, $persist) {

        // 1. Kunci baris staf agar pemesanan untuk staf yang sama berjalan berurutan
        Staff::whereKey($staffId)->lockForUpdate()->firstOrFail();

        $endAt       = $startAt->copy()->addMinutes($durationMinutes);
        $blockedUntil = $endAt->copy()->addMinutes($bufferMinutes);

        // 2. Cek overlap dengan booking aktif: [start, blocked_until) bertabrakan dengan [start_baru, blocked_baru)
        $conflict = Booking::where('staff_id', $staffId)
            ->where('start_at', '<', $blockedUntil)
            ->where('blocked_until', '>', $startAt)
            ->where(function ($q) {
                $q->whereIn('status', ['pending', 'confirmed', 'completed'])
                  ->orWhere(function ($q) {
                      $q->where('status', 'hold')
                        ->where('hold_until', '>', now());   // hold kedaluwarsa = tidak aktif
                  });
            })
            ->exists();

        if ($conflict) {
            throw new SlotUnavailableException();
        }

        // 3. Validasi jadwal kerja dan cuti dilakukan di sini (dibaca dari staff_schedules dan schedule_exceptions)
        // 4. Simpan booking
        $persist($endAt, $blockedUntil);
    });
}
```

Catatan teknis:
- Overlap dihitung dengan `start < blocked_baru` dan `blocked_lama > start_baru`. Kondisi ini benar untuk semua kasus, termasuk jika satu rentang berada di dalam rentang lain.
- Hold yang sudah kedaluwarsa otomatis tidak lagi memblokir, tanpa perlu job pembersihan.
- Untuk booking "Siapa saja", aplikasi mencoba beberapa staf yang bisa melakukan semua layanan terpilih. Pengecekan di atas dijalankan per kandidat. Staf yang lolos dipilih, lalu disimpan.
- Pengecekan di frontend hanya untuk UX. Sumber kebenarannya tetap di transaksi ini.

## E. Pertanyaan Lanjutan

1. **Apakah cek dan kunci hold juga perlu saat kasir mengonfirmasi?** Konfirmasi hold harus mengubah status tanpa membuat ulang slot. Sebaiknya pakai method yang sama dengan `lockForUpdate()`, lalu pastikan `hold_until` belum lewat. Jika sudah lewat, kasir diberi pilihan memperpanjang atau melepas.
2. **Apakah `customers.phone` perlu unik?** Nomor yang sama bisa dimiliki dua orang, misalnya nomor keluarga. Kalau ini sering terjadi, ubah ke indeks biasa dan pilih pelanggan lewat nama dan telepon.
3. **Apakah layanan dengan banyak item perlu buffer per item?** Skema ini menyimpan buffer per item, tetapi `blocked_until` dihitung di aplikasi. Perlu ditentukan apakah buffer ditambahkan setelah setiap layanan atau hanya di akhir. Asumsi yang lebih aman: jumlahkan semua buffer.