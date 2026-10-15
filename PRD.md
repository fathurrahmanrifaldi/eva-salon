# PRD: Sistem Booking Salon Kecantikan "Eva Salon" (Satu Cabang)

| Item | Keterangan |
|---|---|
| Versi | 0.1 (draft untuk validasi) |
| Tanggal | 14 Oktober 2026 |
| Status | Menunggu validasi dengan owner |
| Stack | Laravel, Blade, Tailwind CSS, JavaScript, MySQL |
| Hosting | didinmediagroup.com Mini Plus |

---

## 1. Ringkasan

Salon kecantikan berskala menengah dengan sekitar 20 staf masih mengelola booking melalui kasir, pencatatan kertas, dan WhatsApp. Sistem ini menyediakan:

- **Website publik** untuk melihat layanan, harga, dan jam operasional, serta melakukan booking.
- **Dashboard kasir dan owner** untuk mengelola booking, termasuk walk-in, dalam satu kalender.
- **Integrasi WhatsApp** melalui tombol `wa.me` dengan pesan terisi otomatis.

Nilai utama sistem ada pada pencegahan double-booking dan pencatatan walk-in yang cepat, bukan pada tampilan katalog.

## 2. Latar Belakang dan Masalah

| Masalah saat ini | Dampak |
|---|---|
| Booking dicatat di kertas dan chat WA | Jadwal bentrok, sulit dicari, dan tidak bisa dianalisis |
| Kasir membalas pertanyaan slot kosong satu per satu | Waktu kasir habis untuk hal yang bisa otomatis |
| Tidak ada riwayat pelanggan | Sulit mengenali pelanggan lama dan menawarkan layanan yang sesuai |
| No-show tidak tercatat | Owner tidak tahu seberapa sering slot terbuang |

## 3. Tujuan dan Metrik Keberhasilan

### Tujuan produk
1. Mengurangi double-booking menjadi nol untuk booking yang masuk sistem.
2. Membuat pencatatan walk-in oleh kasir selesai dalam waktu maksimal 15 detik.
3. Memberi pelanggan cara booking tanpa harus menghubungi salon.

### Metrik (ukur selama pilot 2 minggu)
| Metrik | Target awal |
|---|---|
| Persentase booking yang tercatat di sistem dibanding kertas | ≥ 90% |
| Waktu input walk-in oleh kasir | ≤ 15 detik |
| Booking online per minggu | Dilacak, tanpa target dulu (belum ada baseline) |
| Kejadian double-booking | 0 |
| Waktu muat halaman kalender | ≤ 2 detik dengan data 3 bulan |

## 4. Pengguna dan Peran

| Peran | Akun login | Tujuan utama |
|---|---|---|
| **Pelanggan** | Tidak wajib | Booking dan cek status reservasi |
| **Kasir** | Ya | Mengelola booking harian, walk-in, dan pembayaran |
| **Owner** | Ya | Mengelola layanan, harga, staf, jadwal, akun kasir, dan melihat laporan |

Staf (kapster/terapis) adalah **data**, bukan pengguna sistem di versi ini.

### Hak akses
| Aksi | Owner | Kasir | Pelanggan |
|---|:---:|:---:|:---:|
| Lihat katalog dan jam operasional | ✓ | ✓ | ✓ |
| Buat booking online | — | — | ✓ |
| Buat booking walk-in | ✓ | ✓ | — |
| Ubah status booking | ✓ | ✓ | Batalkan sendiri (dengan batas waktu) |
| Kelola layanan dan harga | ✓ | — | — |
| Kelola staf dan jadwal | ✓ | Lihat saja | — |
| Kelola akun kasir | ✓ | — | — |
| Hapus booking | — | — | — |
| Lihat data pelanggan | ✓ | ✓ | Hanya miliknya sendiri |

Catatan: tidak ada yang bisa menghapus booking secara permanen. Pembatalan selalu tercatat dengan alasan.

## 5. Ruang Lingkup

### Dalam cakupan (MVP)
- Halaman publik: beranda, katalog layanan, booking online, cek status booking.
- Dashboard admin: kalender, daftar booking, walk-in, kelola layanan, kelola staf dan jadwal, data pelanggan, akun kasir.
- Tombol WhatsApp di beranda, halaman booking, dan setiap booking (untuk konfirmasi ke pelanggan).
- Log perubahan status booking.
- Pencatatan pembayaran sederhana oleh kasir (cash, transfer, QRIS).

### Di luar cakupan (MVP)
- Pembayaran online dan DP otomatis.
- Login dan akun untuk pelanggan.
- Login untuk staf.
- Notifikasi WA otomatis (WhatsApp Business API).
- Notifikasi email dan pengingat H-1 (memerlukan cron, lihat bagian risiko hosting).
- Program loyalitas dan poin.
- Multi-cabang.
- Aplikasi mobile native.
- Migrasi data pelanggan dari kertas (input bertahap seiring kunjungan).

## 6. Kebutuhan Fungsional

### 6.1 Halaman Utama (publik)

| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| HU-01 | Menampilkan profil salon (nama, deskripsi, alamat, telepon) | Data diambil dari `salon_profile`, bisa diubah owner |
| HU-02 | Menampilkan galeri suasana salon | Maksimal 12 foto, dikompres ke WebP, lebar maksimal 1200 px |
| HU-03 | Menampilkan layanan unggulan | Maksimal 6 layanan, berdasarkan `is_featured` dan `is_available` |
| HU-04 | Menampilkan jam operasional | Ambil dari `business_hours`, menandai hari libur |
| HU-05 | Tombol "Booking Sekarang" di bagian atas | Selalu terlihat di desktop dan mobile |
| HU-06 | Tombol WhatsApp | Membuka `wa.me` dengan nomor dari `salon_profile` |

### 6.2 Katalog Layanan (publik)

| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| KL-01 | Daftar layanan dikelompokkan per kategori | Urutan kategori mengikuti `sort_order` |
| KL-02 | Setiap layanan menampilkan nama, deskripsi, foto, harga, dan estimasi durasi | Harga dalam format Rupiah, durasi dalam menit atau jam |
| KL-03 | Layanan yang tidak tersedia tetap tampil dengan label "Tidak tersedia" | Tidak bisa dipilih di form booking |
| KL-04 | Pencarian sederhana berdasarkan nama layanan | Client-side, tanpa reload |

### 6.3 Booking Online (publik)

Alur 5 langkah:

1. **Pilih layanan.** Bisa lebih dari satu layanan dalam satu booking. Total durasi dihitung otomatis.
2. **Pilih staf.** Pilihan "Siapa saja" atau staf tertentu yang bisa melakukan semua layanan yang dipilih.
3. **Pilih tanggal dan jam.** Hanya slot yang tersedia yang ditampilkan.
4. **Isi data.** Nama, nomor telepon (format Indonesia), catatan (opsional), dan persetujuan penggunaan data.
5. **Konfirmasi.** Sistem menampilkan kode booking dan tombol untuk mengirim detail ke WhatsApp salon.

| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| BO-01 | Hanya menampilkan slot yang valid | Slot memenuhi semua aturan di bagian 7 |
| BO-02 | Validasi nomor telepon | Diterima format 08xxx dan +628xxx, disimpan dalam format +62 |
| BO-03 | Mencegah booking ganda | Pengecekan ulang di dalam transaksi database dengan `lockForUpdate()` |
| BO-04 | Menampilkan kode booking | Acak, 8 karakter, tanpa karakter ambigu (0/O, 1/I) |
| BO-05 | Status awal booking online adalah `pending` | Slot tetap dikunci sampai kasir mengonfirmasi atau booking dibatalkan |
| BO-06 | Persetujuan data wajib dicentang | Disimpan dengan waktu `consent_at` |
| BO-07 | Form tidak kehilangan data jika terjadi error validasi | Input dipertahankan, error ditampilkan per field |

### 6.4 Cek Status Booking (publik)

| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| CS-01 | Pelanggan memasukkan kode booking dan nomor telepon | Keduanya harus cocok, jika tidak tampilkan pesan generik |
| CS-02 | Menampilkan status, layanan, staf, dan jadwal | Tidak menampilkan data pelanggan lain |
| CS-03 | Pelanggan bisa membatalkan booking `pending` atau `confirmed` | Hanya jika lebih dari 3 jam sebelum jadwal (nilai bisa diatur owner) |
| CS-04 | Rate limit pada halaman cek status | Maksimal 10 percobaan per menit per IP |

### 6.5 Dashboard Admin (kasir dan owner)

#### a. Kalender dan daftar booking
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| AD-01 | Tampilan kalender harian dan mingguan, dikelompokkan per staf | Blok booking berwarna sesuai status |
| AD-02 | Filter berdasarkan tanggal, staf, dan status | Hasil diperbarui tanpa reload penuh |
| AD-03 | Daftar booking hari ini sebagai tampilan utama | Diurutkan berdasarkan jam mulai |

#### b. Walk-in (prioritas tertinggi)
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| WI-01 | Tombol "Walk-in" selalu terlihat di dashboard | Satu klik untuk membuka form |
| WI-02 | Form minimal: layanan, staf (atau "siapa saja"), dan nama | Nomor telepon opsional untuk walk-in |
| WI-03 | Jika nama dan telepon sudah ada, sistem menyarankan pelanggan lama | Pencarian berdasarkan nomor telepon |
| WI-04 | Walk-in langsung berstatus `confirmed` dan mengunci slot | Tidak perlu konfirmasi tambahan |
| WI-05 | Waktu input dari awal sampai tersimpan maksimal 15 detik | Diuji dengan stopwatch pada pilot |

#### c. Konfirmasi dan status
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| ST-01 | Kasir bisa mengonfirmasi booking `pending` | Status berubah ke `confirmed`, log tercatat |
| ST-02 | Kasir bisa membatalkan booking dengan alasan wajib | Alasan dipilih dari daftar atau diisi bebas |
| ST-03 | Status `completed` diubah setelah layanan selesai | Bisa dilakukan dengan catat pembayaran |
| ST-04 | Status `no_show` ditandai manual oleh kasir | Hanya bisa jika jadwal sudah lewat |
| ST-05 | Setiap perubahan status dicatat | `booking_status_logs` menyimpan pengguna, waktu, dan status lama-baru |
| ST-06 | Tombol WA di setiap booking | Pesan terisi otomatis: konfirmasi, pengingat, atau pembatalan, sesuai status |

Alur status:
```
pending ──confirm──▶ confirmed ──selesai──▶ completed
   │                    │
   └──cancel──▶ cancelled ◀──cancel──┘
                        │
                 confirmed ──lewat jadwal──▶ no_show
```

#### d. Pembayaran sederhana
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| PB-01 | Kasir mencatat pembayaran: jumlah, metode, dan waktu | Metode: cash, transfer, QRIS |
| PB-02 | Booking tidak bisa `completed` tanpa pencatatan pembayaran | Bisa diatur owner jika tidak ingin wajib |
| PB-03 | Pembayaran tidak bisa dihapus, hanya dikoreksi dengan entri baru | Mencegah manipulasi diam-diam |

#### e. Kelola layanan (owner)
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| LY-01 | CRUD kategori dan layanan | Nama, deskripsi, harga, durasi, buffer, foto, status |
| LY-02 | Mengatur staf yang bisa melakukan layanan | Relasi `service_staff` |
| LY-03 | Mengubah harga tidak mengubah booking lama | Harga disalin ke `booking_items` saat booking dibuat |
| LY-04 | Menonaktifkan layanan tanpa menghapus riwayat | Gunakan `is_available` |

#### f. Kelola staf dan jadwal (owner)
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| SF-01 | CRUD staf, dengan nonaktifkan sebagai pengganti hapus | Staf nonaktif tidak muncul di pilihan booking |
| SF-02 | Mengatur jadwal kerja mingguan per staf | Satu atau lebih shift per hari |
| SF-03 | Mengatur jam operasional salon | Termasuk hari libur |
| SF-04 | Mengatur cuti dan libur khusus | Per staf atau seluruh salon |

#### g. Data pelanggan
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| DP-01 | Daftar pelanggan dengan pencarian nama dan telepon | Paginasi 25 per halaman |
| DP-02 | Riwayat booking per pelanggan | Termasuk status dan total pembayaran |
| DP-03 | Edit data pelanggan oleh kasir atau owner | Perubahan tercatat |

#### h. Akun kasir (owner)
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| AK-01 | Owner membuat, mengedit, dan menonaktifkan akun kasir | Kasir nonaktif tidak bisa login |
| AK-02 | Reset password oleh owner | Password sementara, wajib diganti saat login pertama |

#### i. Pengaturan profil salon (owner)
| ID | Kebutuhan | Kriteria penerimaan |
|---|---|---|
| PS-01 | Mengubah nama, alamat, deskripsi, telepon, nomor WA, dan embed peta | Tampil di halaman publik |
| PS-02 | Mengelola galeri | Upload dengan kompresi otomatis |

## 7. Aturan Bisnis

### 7.1 Ketersediaan slot
Slot dianggap tersedia jika semua kondisi berikut terpenuhi:

1. Tanggal dan jam berada dalam jam operasional, dan salon tidak sedang libur.
2. Staf bekerja pada jam tersebut, dan tidak sedang cuti.
3. Rentang waktu layanan (durasi total + buffer) tidak bertabrakan dengan booking aktif staf tersebut. Booking aktif adalah `pending`, `confirmed`, atau `completed`.
4. Pengecekan diulang di dalam transaksi database saat booking disimpan.

Contoh: layanan 60 menit dengan buffer 10 menit membutuhkan blok 70 menit. Booking mulai 10.00 mengunci staf sampai 11.10.

### 7.2 Aturan "Siapa saja"
Sistem memilih staf yang bisa melakukan semua layanan yang dipilih dan memiliki slot kosong paling awal. Jika beberapa staf memenuhi, dipilih yang jumlah bookingnya paling sedikit pada hari tersebut.

### 7.3 Pembatalan
- Pelanggan bisa membatalkan sendiri hingga batas waktu tertentu (default 3 jam sebelum jadwal).
- Kasir dan owner bisa membatalkan kapan saja dengan alasan wajib.
- Booking yang dibatalkan tetap tersimpan dan tidak lagi mengunci slot.

### 7.4 Harga dan durasi
- Harga dan durasi disalin ke `booking_items` saat booking dibuat.
- Perubahan harga di katalog hanya berlaku untuk booking baru.

### 7.5 Zona waktu
Semua waktu disimpan dalam UTC dan ditampilkan dalam `Asia/Jakarta`. Atur `APP_TIMEZONE=Asia/Jakarta` sejak awal.

## 8. Kebutuhan Non-Fungsional

| Kategori | Kebutuhan |
|---|---|
| **Performa** | Halaman publik memuat ≤ 3 detik di koneksi 4G. Kalender admin ≤ 2 detik dengan 3 bulan data. |
| **Keamanan** | Password di-hash (bcrypt/argon2 bawaan Laravel). CSRF aktif. Otorisasi memakai Policy di backend. Rate limit untuk login dan cek status. |
| **Privasi (UU PDP)** | Persetujuan tercatat saat booking. Kebijakan privasi sederhana tersedia. Data tidak dibagikan ke pihak ketiga. Pelanggan bisa meminta penghapusan data melalui kasir atau owner. |
| **Retensi data** | Data pelanggan yang tidak aktif lebih dari 24 bulan ditinjau untuk dihapus atau dianonimkan (kebijakan final perlu disepakati owner). |
| **Keandalan** | Backup database mingguan dari panel hosting, dengan salinan `mysqldump` tambahan sebelum setiap perubahan skema. |
| **Aksesibilitas** | Kontras warna memenuhi WCAG AA. Form dapat digunakan dengan keyboard. |
| **Responsif** | Desain mobile-first. Dashboard kasir juga dapat dipakai di tablet. |
| **Browser** | Chrome, Edge, Safari, dan Firefox versi terbaru beserta satu versi sebelumnya. |
| **Lokalisasi** | Bahasa Indonesia. Format tanggal dan Rupiah sesuai standar Indonesia. |

## 9. Arsitektur Teknis

### 9.1 Stack
- **Backend:** Laravel (versi LTS terbaru yang didukung PHP 8.3 atau 8.4).
- **Frontend:** Blade, Tailwind CSS, dan JavaScript secukupnya. Pertimbangkan Livewire untuk kalender dan form walk-in agar interaksi lebih cepat tanpa menulis banyak JavaScript.
- **Database:** MySQL 8.
- **Build aset:** Vite di lokal. Hasil build di-upload ke server.

### 9.2 Lingkungan
| Lingkungan | Keterangan |
|---|---|
| Lokal | Laravel Herd, Docker, atau XAMPP. Database terpisah. |
| Staging (opsional) | Subdomain dari Mini Plus untuk demo ke owner. |
| Produksi | Domain utama. Data asli hanya di sini. |

Gunakan subdomain untuk staging dan domain utama untuk produksi. Mini Plus mendukung 3 subdomain, dan itu cukup untuk kebutuhan ini.

### 9.3 Batasan hosting dan dampaknya
| Batasan | Dampak | Strategi |
|---|---|---|
| Document root ke `public` | Keamanan kode bisa terekspos jika salah | Konfirmasi ke penyedia sebelum deploy. Jangan memindahkan isi `public` tanpa alasan kuat. |
| Cron | Fitur otomatis tidak berjalan | Pengingat dan auto-expire masuk fase 2. Untuk MVP, aksi dilakukan manual. |
| SSH dan Composer | Install dependency sulit | Build dan vendor di lokal, lalu upload. Migrasi dijalankan lewat fitur tersedia atau dengan skrip satu kali yang dihapus setelah dipakai. |
| Memory dan waktu eksekusi | Query berat bisa timeout | Indeks pada `staff_id, start_at` dan `booking_code`. Paginasi di setiap daftar. |
| SMTP | Email bisa gagal | Email tidak menjadi jalur utama. Notifikasi utama tetap lewat WA. |
| Penyimpanan 1 GB | Foto cepat memenuhi kuota | Kompresi WebP, batas ukuran upload 2 MB, dan batas jumlah foto galeri. |

Catatan: detail spesifikasi (versi PHP tersedia, cara mengatur document root, dan kebijakan cron) perlu dikonfirmasi langsung ke penyedia hosting sebelum development dimulai.

### 9.4 Model data
Mengacu pada model data revisi yang sudah disepakati:

`users`, `customers`, `staff`, `staff_schedules`, `schedule_exceptions`, `business_hours`, `service_categories`, `services`, `service_staff`, `bookings`, `booking_items`, `booking_status_logs`, `payments`, `salon_profile`, `gallery_images`.

Indeks penting:
- `bookings(staff_id, start_at)` untuk pengecekan bentrok.
- `bookings(booking_code)` unik.
- `customers(phone)` untuk pencarian dan lookup.
- `service_staff(service_id, staff_id)` unik.

## 10. Notifikasi WhatsApp (Tahap Awal)

Pendekatan: tombol `wa.me` dengan pesan terisi, dikirim manual oleh kasir atau pelanggan.

| Situasi | Pengirim | Isi pesan (contoh ringkas) |
|---|---|---|
| Booking online dibuat | Pelanggan (dari halaman konfirmasi) | Kode booking, layanan, staf, dan jadwal |
| Booking dikonfirmasi | Kasir (dari dashboard) | Konfirmasi dan instruksi datang |
| Pengingat H-1 | Kasir (dari daftar hari berikutnya) | Pengingat jadwal |
| Pembatalan | Kasir atau pelanggan | Konfirmasi pembatalan |

Alasan memilih pendekatan ini: tidak ada biaya tambahan, tidak perlu verifikasi WhatsApp Business API, dan sesuai kebiasaan owner dan pelanggan.

Fase 2 dapat menambahkan WhatsApp Business API atau gateway pihak ketiga jika volume dan anggaran memungkinkan.

## 11. Analisis Risiko

| Risiko | Kemungkinan | Dampak | Mitigasi |
|---|---|---|---|
| Kasir tidak memakai sistem dan kembali ke kertas | Tinggi | Tinggi | Walk-in maksimal 3 klik. Pilot paralel. Pantau metrik persentase pencatatan. |
| Double-booking karena race condition | Sedang | Tinggi | Transaksi dan `lockForUpdate()`. Unique constraint pada kombinasi tertentu. Uji dengan skenario paralel. |
| Logika slot salah (buffer, jadwal, cuti) | Sedang | Tinggi | Unit test untuk setiap aturan di bagian 7.1. Uji dengan data dummy 20 staf. |
| Kebocoran data pelanggan | Rendah sampai sedang | Tinggi | Policy di backend. Review kode auth dan akses data. Rate limit. |
| Kode hasil AI berisi celah keamanan | Sedang | Tinggi | Review manual bagian auth, upload, dan query. Jangan langsung menerima kode mentah. |
| Hosting membatasi fitur Laravel | Sedang | Sedang | Konfirmasi spesifikasi sebelum mulai. Rancang ulang fitur yang bergantung cron atau SSH. |
| Owner tidak tertarik membayar | Sedang | Tinggi | Validasi masalah lebih dulu. Pilot gratis dengan metrik yang disepakati. |
| Data penting hilang | Rendah | Tinggi | Backup mingguan, salinan `mysqldump`, dan soft delete. |
| Pelanggan merasa tidak nyaman dengan pencatatan data | Rendah | Sedang | Persetujuan jelas. Kebijakan privasi sederhana. |

## 12. Pertanyaan Terbuka (perlu dijawab owner)

1. Apakah booking online perlu konfirmasi manual oleh kasir, atau bisa langsung `confirmed`? *(Asumsi default: `pending` dengan konfirmasi manual.)*
2. Berapa batas waktu pembatalan oleh pelanggan? *(Asumsi default: 3 jam.)*
3. Apakah pembayaran wajib dicatat sebelum booking ditandai selesai? *(Asumsi default: tidak wajib di MVP.)*
4. Berapa lama data pelanggan disimpan? *(Asumsi default: ditinjau setelah 24 bulan tidak aktif.)*
5. Apakah ada layanan yang membutuhkan staf dengan keahlian khusus?
6. Apakah ada jam sibuk yang sering dikomplain pelanggan?
7. Apakah ada kebiasaan booking "tahan slot" lewat telepon yang perlu didukung?

## 13. Fase Pengembangan

### Fase 0: Validasi (1–2 minggu)
- Wawancara owner dan dua kasir.
- Konfirmasi spesifikasi hosting ke penyedia.
- Finalisasi jawaban pertanyaan terbuka.

### Fase 1: MVP (target 6–8 minggu)
- Halaman publik: beranda, katalog, booking online, cek status.
- Dashboard kasir: kalender, walk-in, konfirmasi, status, pembayaran sederhana.
- Owner: layanan, staf, jadwal, akun kasir, profil salon.
- Tombol WA.
- Deploy ke staging, lalu pilot paralel dengan kertas selama 2 minggu.

### Fase 2: Penyempurnaan (berdasarkan hasil pilot)
- Pengingat H-1 (aktifkan cron jika hosting mendukung).
- Laporan sederhana: booking per minggu, no-show, dan pendapatan.
- Riwayat pelanggan yang lebih lengkap.
- Evaluasi kebutuhan notifikasi WA otomatis.

### Fase 3: Pertimbangan (jika owner setuju dan skala tumbuh)
- Pindah ke VPS atau PaaS dengan domain sendiri.
- Akun login pelanggan.
- Pembayaran DP.

## 14. Definisi Istilah

| Istilah | Arti |
|---|---|
| Walk-in | Pelanggan datang langsung tanpa booking online |
| No-show | Pelanggan yang sudah dijadwalkan tetapi tidak datang |
| Buffer | Jeda waktu bersih antar pelanggan untuk staf |
| Slot | Rentang waktu yang bisa dipesan untuk staf tertentu |
| Staf | Kapster, terapis, atau tenaga layanan lain |

---