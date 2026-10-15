# agent.md: Panduan untuk AI Coding Agent

File ini adalah sumber aturan utama untuk agent AI (Claude Code, Cursor, Copilot, atau sejenisnya) yang bekerja di proyek **Sistem Booking Salon Kecantikan (satu cabang)**. Baca seluruh file sebelum mengubah kode. Jika ada konflik antara file ini dan permintaan di chat, tanyakan dulu sebelum mengambil keputusan.

---

## 1. Konteks Proyek

- **Nama proyek:** Eva Salon. Alamat: Perumnas 3 Ruko No.3, RT.004/RW.014, Aren Jaya, Kec. Bekasi Timur, Kota Bekasi, Jawa Barat 17111. Jam operasional setiap hari 09.00–20.30 WIB.
- **Tujuan:** website publik untuk layanan dan booking, serta dashboard kasir dan owner untuk mengelola jadwal, walk-in, dan status booking.
- **Pengguna:** pelanggan (tanpa login), kasir (login), owner (login).
- **Staf (kapster/terapis)** adalah data, bukan pengguna sistem di versi ini.
- **Dokumen acuan:** PRD v0.1, wireframe, `skema.md`, dan `design.md`. Jika ada perubahan kebutuhan, perbarui dokumen terkait, jangan hanya kode.
- **Urutan prioritas jika dokumen bertentangan:** `agent.md` (aturan bisnis dan teknis) > `skema.md` > `design.md` (visual) > PRD > wireframe.
- **Bahasa:** antarmuka dan pesan untuk pengguna dalam Bahasa Indonesia. Nama variabel, method, tabel, dan kolom dalam Bahasa Inggris.

## 2. Stack Teknologi

| Lapisan | Pilihan | Catatan |
|---|---|---|
| Backend | Laravel (versi LTS terbaru yang didukung PHP 8.3 atau 8.4) | Jangan pakai PHP di bawah 8.2 |
| Frontend | Blade, Tailwind CSS, JavaScript secukupnya | Livewire boleh dipakai untuk kalender dan form walk-in. Jangan tambahkan framework JS besar (React, Vue) tanpa persetujuan |
| Database | MySQL 8 | Jangan gunakan fitur khusus database lain |
| Build aset | Vite, dijalankan di lokal | Hasil build (`public/build`) ikut di-upload ke hosting |
| Hosting | didinmediagroup.com Mini Plus | Lihat bagian 9 untuk batasan |

**Larangan:** jangan menambah package baru tanpa alasan tertulis di PR atau komentar. Setiap dependency adalah beban hosting dan keamanan.

**Font:** Cormorant Garamond (atau Playfair Display) untuk heading, dan Plus Jakarta Sans untuk body. Self-host berkas `.woff2` di `public/fonts`, jangan memuat dari CDN Google Fonts.

## 3. Lingkungan dan Konfigurasi

- Set `APP_TIMEZONE=Asia/Jakarta` dan `APP_LOCALE=id`.
- Simpan semua waktu di database dalam **UTC**. Tampilkan dalam **Asia/Jakarta**. Konversi hanya di lapisan presentasi atau di satu helper terpusat.
- Jangan pernah commit file `.env`, kredensial database, atau API key. Gunakan `.env.example` sebagai template tanpa nilai rahasia.
- Lingkungan: lokal (dev), staging (subdomain untuk demo), produksi (domain utama). Jangan jalankan migration atau seeder di produksi tanpa instruksi eksplisit.
- Jangan mengembangkan langsung terhadap database produksi.

## 4. Peran dan Otorisasi

| Aksi | Owner | Kasir | Pelanggan |
|---|:---:|:---:|:---:|
| Lihat katalog dan jam operasional | ✓ | ✓ | ✓ |
| Buat booking online | — | — | ✓ |
| Buat walk-in dan tahan slot (hold) | ✓ | ✓ | — |
| Konfirmasi, ubah status, catat pembayaran | ✓ | ✓ | — |
| Batalkan booking | ✓ | ✓ (wajib alasan) | Sendiri, selama lebih dari batas waktu |
| Kelola layanan dan harga | ✓ | — | — |
| Kelola staf, jadwal, dan cuti | ✓ | Lihat saja | — |
| Kelola akun kasir | ✓ | — | — |
| Hapus booking atau pembayaran | — | — | — |

**Aturan wajib:**
- Otorisasi dilakukan di **backend** dengan **Policy** Laravel. Menyembunyikan tombol di tampilan tidak dihitung sebagai otorisasi.
- Setiap endpoint dan method controller harus memanggil `authorize()` atau middleware yang setara.
- Jangan pernah menambahkan `forceDelete()` atau `delete()` pada `bookings`, `payments`, `booking_status_logs`, atau `customers`.
- Akun kasir yang nonaktif (`is_active = false`) tidak boleh login.

## 5. Model Data dan Konvensi

Tabel yang sudah disepakati:
`users`, `salon_profiles`, `business_hours`, `customers`, `staff`, `staff_schedules`, `schedule_exceptions`, `service_categories`, `services`, `service_staff`, `bookings`, `booking_items`, `booking_status_logs`, `payments`, `gallery_images`.

Aturan:
- **Jangan mengubah migration yang sudah pernah dijalankan di produksi.** Buat migration baru untuk perubahan.
- Setiap foreign key memakai `restrictOnDelete()`. Tidak ada cascade delete.
- Harga dan durasi di `booking_items` adalah **snapshot**. Jangan membacanya ulang dari `services` untuk booking lama.
- `booking_status_logs` bersifat **append-only**. Tidak ada `updated_at`, tidak ada update atau delete.
- `payments` bersifat append-only. Koreksi dilakukan dengan baris baru bertipe `correction` dan nilai negatif.
- Harga disimpan sebagai `unsignedInteger` dalam Rupiah. Jangan gunakan float.
- Layanan dengan harga berentang (Make Up, Eyelash Extension, Totok Wajah, Waxing) memakai `services.price` sebagai harga minimum dan `services.price_max` (nullable) sebagai harga maksimum. Validasi: `price_max` harus lebih besar atau sama dengan `price` jika diisi.
- Untuk booking, `price_snapshot` di `booking_items` menyimpan harga minimum. Harga final ditetapkan kasir saat pembayaran dan dicatat di `payments`, bukan diubah di `booking_items`.
- Paket khusus disimpan sebagai satu baris di `services` dengan isi paket di `description`, satu poin per baris.
- Kategori layanan yang dipakai: Terapi & Refleksi, Kuku & Rias Wajah, Perawatan Wajah & Tubuh, Paket Khusus.
- Nomor telepon disimpan dalam format `+62`. Validasi input menerima `08xxx` dan `+628xxx`, lalu dinormalisasi.
- `booking_code` 8 karakter acak, tanpa `0`, `O`, `1`, `I`. Gunakan `random_int` atau generator aman, bukan `rand()`.

## 6. Aturan Bisnis Inti (Jangan Diubah Tanpa Persetujuan)

### 6.1 Status booking
```
hold ──konfirmasi──▶ pending ──konfirmasi kasir──▶ confirmed ──selesai──▶ completed
  │                    │                              │
  └─────lepas/expired──┴──────────cancel──────────────┴──▶ cancelled
                                                        confirmed ──lewat jadwal──▶ no_show
```
- Booking online selalu masuk sebagai `pending` dan menunggu konfirmasi kasir.
- Walk-in langsung `confirmed`.
- `completed` tidak mewajibkan pembayaran (sesuai keputusan owner). Ini diatur oleh `salon_profiles.require_payment_to_complete`.
- `no_show` hanya boleh ditandai jika `start_at` sudah lewat.
- Setiap perubahan status **wajib** menulis baris ke `booking_status_logs`. Batalkan dan no_show wajib menyertakan `reason`.

### 6.2 Ketersediaan slot
Slot tersedia jika semua kondisi berikut terpenuhi:
1. Tanggal dan jam berada dalam `business_hours` dan tidak ada `schedule_exceptions` bertipe `salon_closed` yang mencakup tanggal tersebut.
2. Staf memiliki `staff_schedules` pada hari dan jam tersebut, dan tidak ada `staff_leave` yang mencakup tanggal tersebut.
3. Rentang `[start_at, blocked_until)` tidak overlap dengan booking aktif staf yang sama. Booking aktif adalah `pending`, `confirmed`, `completed`, dan `hold` dengan `hold_until > now()`.
4. Pengecekan diulang di dalam transaksi dengan `lockForUpdate()` pada baris `staff`.

Rumus overlap yang benar: `existing.start_at < new.blocked_until AND existing.blocked_until > new.start_at`.

**Jangan pernah** mengandalkan pengecekan di frontend sebagai sumber kebenaran. Frontend hanya untuk UX.

Method pusat untuk semua pembuatan booking dan hold adalah `BookingService::reserveSlot()` (atau nama setara). **Jangan menulis logika overlap di controller atau di tempat lain.**

### 6.3 Hold
- Durasi default dan maksimum diambil dari `salon_profiles` (`hold_default_minutes`, `hold_max_minutes`).
- Hold tidak butuh cron. Hold yang sudah lewat `hold_until` dianggap tidak aktif saat pengecekan.
- Saat kasir mengonfirmasi hold yang sudah lewat, tampilkan pilihan memperpanjang atau melepas. Jangan konfirmasi diam-diam.

### 6.4 Pembatalan oleh pelanggan
- Pelanggan bisa membatalkan booking `pending` atau `confirmed` jika waktu sekarang masih lebih dari `cancel_limit_minutes` sebelum `start_at` (default 30 menit).
- Pembatalan oleh pelanggan tetap menulis log dengan `actor_type = customer`.

### 6.5 Pelanggan dan privasi
- `consent_at` wajib tercatat saat booking online. Form tidak bisa dikirim tanpa centang persetujuan.
- Pelanggan hanya bisa melihat booking-nya sendiri melalui kombinasi kode booking **dan** nomor telepon. Pesan error harus generik agar tidak membocorkan keberadaan kode.
- Rate limit pada halaman cek status: 10 percobaan per menit per IP.
- Jangan menampilkan data pelanggan lain atau detail hold kepada publik. Slot ditahan tampil sebagai "tidak tersedia".
- Catatan pelanggan pada booking tidak ditampilkan di layar yang menghadap pelanggan lain. Dashboard kasir wajib punya opsi menyembunyikan catatan.

## 6A. Aturan Layanan Sensitif

- Layanan Spa Kewanitaan dan Pengencangan Payudara memakai nama netral dan deskripsi sopan, tanpa foto tubuh atau ilustrasi eksplisit.
- Kedua layanan (dan layanan lain yang ditandai `is_sensitive`) boleh disembunyikan dari beranda, tetapi tetap ada di katalog.
- Tambahkan kolom `is_sensitive` (boolean, default false) pada `services`, dan kolom `staff.gender` (enum `female`, `male`, nullable) agar pelanggan bisa memilih staf perempuan. Validasi: layanan dengan `is_sensitive = true` hanya bisa dipesan dengan staf yang sesuai aturan owner. Aturan final menunggu konfirmasi owner (lihat bagian 12).

## 7. Standar Kode

### Umum
- Ikuti PSR-12 dan format dengan Laravel Pint (`./vendor/bin/pint`) sebelum commit.
- Gunakan **Form Request** untuk validasi, bukan validasi langsung di controller.
- Gunakan **Service class** untuk logika bisnis yang melibatkan lebih dari satu model. Controller tipis.
- Gunakan **Enum PHP** untuk status, peran, metode pembayaran, dan source. Jangan hardcode string di banyak tempat.
- Gunakan Eloquent dan query builder. Jangan pakai raw SQL kecuali ada alasan performa yang terdokumentasi, dan selalu gunakan binding.
- Hindari N+1. Gunakan eager loading dan periksa dengan `Model::preventLazyLoading()` di environment lokal.

### Frontend
- Tailwind CSS sebagai satu-satunya sistem styling. Jangan menambahkan CSS framework lain.
- Token warna, font, dan radius wajib diambil dari `design.md` bagian 14 (konfigurasi Tailwind). Jangan menulis kode warna hex langsung di Blade atau komponen.
- Warna emas (`gold-400`) hanya untuk dekorasi dan fokus ring. Teks emas wajib memakai `gold-700`.
- Teks kecil di atas putih tidak boleh memakai `sage-500` ke atas.
- Motif utama adalah lengkungan (`rounded-arch`). Gambar layanan, galeri, dan hero mengikuti bentuk ini.
- Warna status booking di dashboard mengikuti tabel di `design.md` bagian 3, dan selalu disertai label dan ikon. Warna tidak boleh menjadi satu-satunya pembeda.
- Tombol WhatsApp memakai `#25D366` dan hanya untuk aksi WhatsApp.
- Dashboard kasir dan owner memakai latar netral dan target sentuh minimal 44 px. Halaman publik boleh lebih ekspresif, tetapi tetap mengikuti palet.
- Hormati `prefers-reduced-motion`. Transisi 150–200 ms.
- Mobile-first. Setiap halaman publik harus diuji di lebar 360 px.
- Semua form harus bisa dipakai dengan keyboard dan memiliki label yang terhubung (`for` dan `id`).
- Kontras warna memenuhi WCAG AA.
- Pesan error validasi ditampilkan per field. Input yang sudah diisi harus dipertahankan saat error.
- JavaScript kustom seminimal mungkin. Jika ada, tulis di `resources/js` dan jangan pakai inline script di Blade.

### Keamanan
- CSRF aktif untuk semua form. Jangan menonaktifkan `VerifyCsrfToken` untuk route web.
- Upload foto: validasi MIME dan ukuran (maksimal 2 MB), konversi ke WebP, ubah ukuran maksimal lebar 1200 px, dan simpan dengan nama acak. Jangan pakai nama file dari pengguna.
- Foto staf hanya dipublikasikan dengan persetujuan tertulis staf. Foto galeri yang memperlihatkan wajah pelanggan harus diburamkan atau dipotong sebelum diunggah.
- Escape semua output Blade (`{{ }}`). Jangan gunakan `{!! !!}` kecuali untuk konten yang sudah disanitasi dan dicatat alasannya.
- Jangan log password, kode booking lengkap yang dikaitkan dengan telepon, atau token.
- Untuk akun kasir baru, password sementara dibuat acak dan `must_change_password = true`.

## 8. Testing

Wajib ada test untuk:
- Setiap skenario di bagian 6.2 (overlap sebagian, overlap penuh, buffer tepat bersebelahan, hold aktif, hold kedaluwarsa, cuti, salon tutup).
- Dua request booking paralel untuk slot yang sama. Hanya satu yang boleh berhasil.
- Policy untuk setiap peran dan setiap aksi di tabel bagian 4. Termasuk percobaan akses yang seharusnya ditolak.
- Transisi status yang tidak valid harus ditolak dan tidak menulis log.
- Pembatalan pelanggan di dalam dan di luar `cancel_limit_minutes`.
- Konversi zona waktu: input dan tampilan dalam Asia/Jakarta, penyimpanan dalam UTC.

Perintah standar:
```bash
php artisan test
./vendor/bin/pint --test
```

Jangan menandai pekerjaan selesai jika test gagal. Jika ada test yang dilewati (`skip`), jelaskan alasannya.

## 9. Batasan Hosting (didinmediagroup.com Mini Plus)

Asumsikan batasan berikut sampai dikonfirmasi penyedia:
- **Tidak ada cron.** Jangan membuat fitur yang bergantung pada `schedule:run`, queue worker, atau job terjadwal. Aksi yang seharusnya otomatis dibuat manual atau dihitung saat dibaca.
- **Tidak ada SSH atau Composer di server.** Jalankan `composer install --no-dev --optimize-autoloader` dan `npm run build` di lokal, lalu upload `vendor/` dan `public/build/`.
- **Migration di server** hanya lewat cara yang tersedia di panel, atau skrip satu kali yang dihapus setelah dijalankan. Jangan mengandalkan `php artisan migrate` di server tanpa konfirmasi.
- **Document root** harus mengarah ke folder `public`. Jika tidak bisa, berhenti dan laporkan. Jangan memindahkan isi `public` ke root tanpa persetujuan.
- **Email (SMTP)** bukan jalur utama. Notifikasi utama adalah WhatsApp via `wa.me`.
- **Penyimpanan 1 GB.** Foto harus dikompres. Pantau ukuran folder `storage/` dan `public/`.
- **Memory dan waktu eksekusi terbatas.** Paginasi semua daftar (default 25 per halaman). Jangan memuat seluruh tabel ke memori.
- Konfigurasi `.htaccess` atau Nginx mengikuti panduan penyedia. Jangan menebak aturan rewrite.

Jika sebuah fitur membutuhkan salah satu batasan di atas, hentikan dan jelaskan opsinya ke pengguna.

## 10. Aturan Kerja untuk Agent

### Yang harus dilakukan
- Baca bagian yang relevan dari PRD, wireframe, dan skema sebelum mengubah kode.
- Untuk setiap perubahan skema, buat migration baru dan perbarui dokumen skema.
- Jalankan test dan Pint sebelum menyatakan pekerjaan selesai.
- Tulis commit kecil dengan pesan jelas dalam Bahasa Indonesia atau Inggris, konsisten satu bahasa per commit. Contoh: `feat(booking): tambah reserveSlot dengan lockForUpdate`.
- Laporkan dengan jujur apa yang sudah diuji dan apa yang belum.
- Jika ada asumsi baru, tulis di bagian "Asumsi" pada PR atau catatan perubahan.

### Yang tidak boleh dilakukan
- Mengubah aturan di bagian 6 tanpa persetujuan eksplisit dari pengguna.
- Menghapus data (hard delete) atau mengubah migration yang sudah dijalankan.
- Menambahkan fitur di luar cakupan MVP (lihat PRD bagian 5, "Di luar cakupan").
- Menambahkan integrasi pembayaran, WhatsApp Business API, atau login pelanggan tanpa diminta.
- Menggunakan data pelanggan, nama staf, logo, atau foto nyata di seeder atau test. Gunakan data dummy.
- Menggunakan foto interior Eva Salon di produksi sebelum owner memberi izin tertulis. Foto dari owner tetap perlu dipotong atau diburamkan untuk wajah pelanggan dan staf yang belum setuju.
- Mengubah nama layanan atau harga dari daftar resmi tanpa konfirmasi owner. Ejaan koreksi di `design.md` bagian 10 masih usulan.
- Menonaktifkan test, Policy, atau validasi untuk membuat kode "jalan".
- Menyatakan fitur selesai tanpa test yang relevan.

### Saat tidak yakin
- Jika kebutuhan ambigu, tanyakan satu pertanyaan spesifik dan tawarkan opsi default yang aman.
- Jika kode yang dihasilkan bergantung pada batasan hosting, jelaskan dampaknya sebelum menulis kode.
- Jika menemukan celah keamanan atau bug di kode yang sudah ada, laporkan segera, jangan diam-diam diperbaiki di perubahan yang tidak terkait.

## 11. Urutan Pengerjaan yang Disarankan

1. Setup proyek, konfigurasi `.env`, timezone, locale, dan font self-hosted.
2. Konfigurasi token Tailwind dari `design.md` bagian 14.
3. Migration dan model sesuai `skema.md`, dengan enum dan relasi.
4. Seeder: `business_hours` (setiap hari 09:00–20:30), satu `salon_profiles` (Eva Salon), 20 staf dummy beserta jadwal, dan layanan sesuai `design.md` bagian 10 dengan durasi sementara.
5. `BookingService::reserveSlot()` beserta unit test lengkap untuk bagian 6.2.
6. Policy dan middleware peran.
7. Dashboard kasir: kalender, walk-in, hold, konfirmasi, status, pembayaran.
8. Halaman publik: beranda, katalog, booking online, cek status, mengikuti `design.md` bagian 6.
9. Dashboard owner: layanan, staf, jadwal, cuti, akun kasir, profil salon, galeri.
10. Tombol WhatsApp (`wa.me`) dengan pesan terisi.
11. Uji responsif, aksesibilitas, kontras warna, dan performa kalender dengan data 3 bulan.
12. Build aset, siapkan paket deploy, dan dokumentasikan langkah upload.

## 12. Pertanyaan Terbuka yang Masih Perlu Diverifikasi

- Spesifikasi PHP yang tersedia di hosting dan kebijakan cron (konfirmasi ke penyedia).
- Apakah `customers.phone` tetap unik atau dibuat indeks biasa (lihat keputusan di skema).
- Apakah buffer dijumlahkan per layanan atau hanya di akhir booking multi-layanan.
- Kebijakan retensi final setelah 24 bulan tidak aktif (saat ini hanya tinjauan manual).
- Durasi sebenarnya setiap layanan (saat ini asumsi di `design.md` bagian 10).
- Cara penetapan harga berentang: berdasarkan apa harga final ditentukan.
- Aturan layanan sensitif dan staf (`is_sensitive`, `staff.gender`): layanan mana dan staf mana yang diizinkan.
- Gaya sapaan ke pelanggan: "kamu" atau "Anda". Sampai dijawab, gunakan "Anda" di halaman formal dan "kamu" di pesan WhatsApp template.
- Logo resmi (SVG/PNG transparan), izin pemakaian foto interior, dan nomor WhatsApp resmi salon.

Jika salah satu pertanyaan di atas memengaruhi kode yang sedang dikerjakan, tanyakan sebelum mengimplementasikan.

---

*Versi 0.2. Terakhir diperbarui: 15 Oktober 2026 (menambahkan rujukan `design.md`, `skema.md`, aturan harga berentang, layanan sensitif, dan aturan visual). Perbarui file ini setiap kali ada keputusan baru yang memengaruhi cara agent bekerja.*
