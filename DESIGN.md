# design.md: Panduan Desain Eva Salon

Panduan visual dan UX untuk website booking **Eva Salon**. Dibaca bersama `agent.md`, PRD, wireframe, dan skema database. Jika ada konflik, aturan bisnis di `agent.md` menang atas keputusan visual di file ini.

---

## 1. Identitas dan Karakter Brand

**Nama:** Eva Salon
**Alamat:** Perumnas 3 Ruko No.3, RT.004/RW.014, Aren Jaya, Kec. Bekasi Timur, Kota Bekasi, Jawa Barat 17111
**Jam operasional:** Setiap hari, 09.00 – 20.30 WIB

**Karakter yang ingin disampaikan:** bersih, hangat, modern, dan terjangkau. Salonnya terlihat lapang dan rapi, dengan nuansa hijau sage dan emas lembut. Website harus terasa seperti masuk ke salon itu, bukan template generik.

**Kata kunci desain:** tenang, lembut, elegan sederhana, ramah, rapi.
**Yang dihindari:** pink-pastel klise ala salon, gradasi mencolok, kesan mewah berlebihan yang tidak sesuai dengan harga layanan (Rp 40.000 – Rp 300.000).

## 2. Analisis Foto Interior (sumber inspirasi visual)

| Elemen di foto | Diterjemahkan ke desain web |
|---|---|
| Dinding dan resepsionis hijau sage | Warna utama (primary) |
| Dinding krem bertekstur | Warna latar (background) |
| Logo emas bergaya script | Warna aksen dan karakter logotype |
| Cermin dan jendela **berbentuk lengkung (arch)** dengan garis lampu hangat | **Motif bentuk utama**: sudut atas membulat pada foto, kartu, dan ilustrasi |
| Panel resepsionis **bergaris vertikal (fluted)** | Pola dekoratif tipis untuk pembatas dan latar seksi |
| Lantai marmer abu-abu muda dan meja marmer putih | Warna netral, kartu putih dengan bayangan lembut |
| Lampu dinding hangat | Nuansa emas hangat pada highlight, bukan putih dingin |
| Tanaman hijau dan bunga putih | Ilustrasi dan ikon organik, ruang kosong yang lega |
| Seragam staf marun | Aksen kedua yang jarang dipakai (label khusus, badge) |

> **Catatan privasi foto:** foto ini memperlihatkan wajah staf dan pelanggan. Untuk website publik, minta izin owner, dan pakai versi yang dipotong atau diburamkan pada wajah pelanggan. Idealnya potret ulang saat salon belum buka atau minta persetujuan tertulis.

## 3. Palet Warna

Nilai di bawah adalah **perkiraan dari foto**. Sesuaikan setelah mengambil sampel warna dari foto asli atau logo resmi.

### Warna inti

| Token | Hex | Pemakaian |
|---|---|---|
| `sage-50` | `#F2F6F1` | Latar seksi alternatif |
| `sage-100` | `#E2EBE0` | Latar kartu lembut, hover |
| `sage-300` | `#A9BDA5` | Border, ikon sekunder |
| `sage-500` | `#7E9A7B` | Dekoratif, ilustrasi |
| `sage-700` | `#4F6B52` | **Primary**: tombol utama, tautan |
| `sage-900` | `#2F4332` | Teks heading, footer |
| `cream-50` | `#FBF8F2` | **Background utama** |
| `cream-100` | `#F5F0E4` | Latar seksi hangat |
| `gold-400` | `#C9A45C` | Aksen dekoratif (garis, ikon, bingkai) |
| `gold-700` | `#7A5C1E` | Teks emas (lolos kontras) |
| `marble-200` | `#DCDDDF` | Border netral, pemisah |
| `ink-900` | `#262B27` | Teks utama |
| `ink-600` | `#5B635D` | Teks sekunder |
| `wine-700` | `#7A2432` | Aksen langka (badge "Paket", seragam) |

### Aturan penggunaan
- Proporsi target: **60% krem/putih, 25% sage, 10% netral gelap, 5% emas**.
- Tombol utama: latar `sage-700`, teks putih (kontras sekitar 5,9:1).
- **Emas `gold-400` hanya untuk dekorasi**, bukan teks, karena kontrasnya rendah di atas krem. Teks emas memakai `gold-700`.
- Jangan memakai warna sage terang (`sage-500` ke atas) untuk teks kecil di atas putih.
- Warna tidak boleh menjadi satu-satunya pembeda status. Selalu sertakan label atau ikon.

### Warna status booking (dashboard)

| Status | Warna | Label + ikon |
|---|---|---|
| `pending` | Kuning amber `#F2C14E` (latar), teks `#5C4300` | Menunggu konfirmasi, ikon jam |
| `confirmed` | Sage `#4F6B52` | Terkonfirmasi, ikon centang |
| `hold` | Biru keabuan `#8FA3B8`, garis putus-putus | Ditahan, ikon gembok/jam pasir |
| `completed` | Abu `#9AA09B` | Selesai, ikon centang ganda |
| `cancelled` | Merah lembut `#C0504D` | Dibatalkan, ikon silang |
| `no_show` | Wine `#7A2432` | Tidak datang, ikon orang dicoret |

## 4. Tipografi

Maksimal **dua keluarga font**. Self-host berkas `.woff2` agar cepat dan tidak bergantung CDN (penting untuk shared hosting dan koneksi seluler).

| Peran | Font | Catatan |
|---|---|---|
| Heading | **Cormorant Garamond** (600) atau **Playfair Display** | Serif elegan, selaras dengan logo bergaya script |
| Body dan UI | **Plus Jakarta Sans** (400, 500, 600) | Nyaman dibaca, mendukung Bahasa Indonesia |
| Logo "Eva Salon" | Gunakan **berkas logo asli** dari owner | Jangan meniru script emas dengan font lain |

### Skala (mobile → desktop)

| Elemen | Mobile | Desktop |
|---|---|---|
| H1 (hero) | 32 px | 52 px |
| H2 (judul seksi) | 26 px | 38 px |
| H3 (judul kartu) | 20 px | 24 px |
| Body | 16 px | 16–17 px |
| Caption, harga kecil | 14 px | 14 px |

Aturan: body minimal 16 px, tinggi baris 1,6, panjang baris maksimal sekitar 70 karakter. Harga memakai angka tabular agar sejajar (`font-variant-numeric: tabular-nums`).

## 5. Bahasa Bentuk

- **Motif utama: lengkungan (arch).** Gambar layanan, galeri, dan foto hero memakai sudut atas membulat penuh (`rounded-t-full` atau radius besar di atas, kecil di bawah).
- **Kartu:** radius 16–20 px, latar putih, border `marble-200`, bayangan sangat lembut.
- **Tombol:** pil (`rounded-full`) di halaman publik. Di dashboard, radius 10 px agar lebih padat.
- **Pola fluted:** garis vertikal tipis (opasitas 6–10%) sebagai latar seksi tertentu, misalnya header katalog dan footer. Cukup satu atau dua tempat, jangan di mana-mana.
- **Ruang kosong:** lega. Jarak antar seksi 64–96 px di desktop, 40–56 px di mobile.
- **Ikon:** garis tipis (stroke 1,5 px), gaya organik. Satu set saja (misalnya Lucide atau Heroicons).

## 6. Halaman Publik

### 6.1 Beranda

Urutan seksi:

1. **Navbar:** logo, menu (Layanan, Harga, Lokasi), tombol **Booking Sekarang** (selalu terlihat, termasuk sticky di mobile).
2. **Hero:** foto interior dalam bingkai lengkung, judul singkat, subjudul, dua tombol (Booking Sekarang dan WhatsApp).
   - Contoh judul: *"Tampil segar, tubuh nyaman, di Eva Salon."*
   - Contoh subjudul: *"Perawatan rambut, wajah, tubuh, dan kuku di Perumnas 3 Bekasi. Buka setiap hari, 09.00–20.30."*
3. **Layanan unggulan:** maksimal 6 kartu dari `is_featured`.
4. **Paket khusus:** sorot Paket Lulur Susu karena nilainya jelas bagi pelanggan.
5. **Suasana salon:** galeri maksimal 12 foto dalam bingkai lengkung.
6. **Jam operasional dan lokasi:** tabel jam, alamat, peta embed, tombol "Buka di Google Maps".
7. **CTA penutup:** "Booking sekarang, konfirmasi lewat WhatsApp."
8. **Footer:** alamat, jam, tautan WhatsApp, kebijakan privasi.

### 6.2 Katalog Layanan

- Tab atau pill kategori di atas (sticky di mobile): Terapi & Refleksi, Kuku & Rias Wajah, Perawatan Wajah & Tubuh, Paket Khusus.
- Kartu layanan: foto (opsional, ada fallback ikon), nama, deskripsi singkat, **harga**, estimasi durasi, tombol **Pilih**.
- Harga berentang ditampilkan sebagai **"Rp 150.000 – Rp 250.000"** dengan keterangan kecil *"Harga final menyesuaikan pilihan di salon."*
- Layanan tidak tersedia: tetap tampil, kartu diredupkan, label **"Tidak tersedia"**, tombol dinonaktifkan.
- Pencarian cepat di atas daftar.
- **Paket Khusus** ditampilkan sebagai kartu lebih besar dengan daftar isi paket (centang), bukan hanya nama.

### 6.3 Form Booking (5 langkah)

Sesuai wireframe: Layanan → Staf → Jadwal → Data → Konfirmasi.

- Step indicator berbentuk titik atau lengkung kecil, bukan bar panjang.
- Ringkasan pesanan (layanan, total durasi, total harga) selalu terlihat di bagian bawah layar mobile.
- Slot jam berbentuk pil. Slot penuh dan ditahan tampil sama: abu dan dicoret, tanpa penjelasan.
- Halaman konfirmasi menegaskan: **"Booking diterima, menunggu konfirmasi dari kasir."** Tombol utama: **Kirim detail ke WhatsApp**.
- Kode booking ditampilkan besar dengan tombol **Salin**.
- Batas pembatalan (30 menit sebelum jadwal) ditulis jelas di halaman konfirmasi dan halaman cek status.

### 6.4 Cek Status

Dua kolom isian (kode booking dan nomor telepon), hasil berupa kartu status dengan lencana warna dan label, serta tombol Batalkan yang hanya aktif sebelum batas waktu.

## 7. Dashboard Kasir dan Owner

Tampilan dashboard **lebih netral dan padat** daripada halaman publik. Fungsi lebih penting daripada dekorasi.

- Latar `cream-50` atau putih, sidebar `sage-900` dengan teks terang.
- Dipakai di **tablet dan layar kecil** di meja kasir. Target sentuh minimal 44 px.
- **Tombol "+ Walk-in" dan "+ Tahan"** selalu terlihat di bar atas, berwarna `sage-700`.
- Kalender: kolom per staf, warna status sesuai tabel di bagian 3, buffer diarsir.
- Banner **"Perlu konfirmasi: N"** berwarna amber, dapat diklik untuk membuka antrean.
- Indikator jam sibuk berupa batang tipis di atas kalender.
- Konfirmasi tindakan berisiko (batalkan, no-show) memakai dialog dengan alasan wajib.
- Tidak ada animasi berlebihan. Transisi 150–200 ms, hormati `prefers-reduced-motion`.

## 8. Komponen Inti

| Komponen | Spesifikasi |
|---|---|
| **Tombol primer** | `sage-700`, teks putih, `rounded-full`, tinggi 48 px (mobile), fokus berupa ring emas `gold-400` 3 px |
| **Tombol sekunder** | Border `sage-700`, teks `sage-700`, latar transparan |
| **Tombol WhatsApp** | Hijau WhatsApp resmi `#25D366` hanya untuk tombol WA, lengkap dengan ikon. Teks gelap agar kontras |
| **Kartu layanan** | Foto lengkung di atas, konten di bawah, harga tebal, durasi dengan ikon jam |
| **Input form** | Label di atas, tinggi 48 px, border `marble-200`, fokus `sage-700`, pesan error di bawah field dalam teks dan ikon |
| **Badge status** | Pil kecil dengan ikon dan label (bagian 3) |
| **Empty state** | Ilustrasi garis sederhana dengan tanaman atau lengkung, pesan ramah dan satu tombol tindakan |

## 9. Konten dan Mikro-copy

**Nada:** ramah, jelas, tidak berlebihan. Gunakan "kamu" untuk pelanggan yang lebih santai, atau "Anda" jika owner menginginkan nuansa lebih formal. **Tanyakan preferensi owner**, lalu gunakan satu gaya konsisten.

Contoh:

| Situasi | Teks |
|---|---|
| Tombol utama | Booking Sekarang |
| Slot habis | Jam ini sudah terisi, coba jam lain |
| Booking sukses | Booking diterima. Kasir kami akan mengonfirmasi lewat WhatsApp |
| Pembatalan terlambat | Pembatalan online hanya bisa dilakukan hingga 30 menit sebelum jadwal. Silakan hubungi kami via WhatsApp |
| Layanan tidak tersedia | Sedang tidak tersedia |
| Error umum | Terjadi kendala. Data Anda tidak hilang, silakan coba lagi |

## 10. Data Layanan Awal (seed)

Nama dikoreksi ejaannya untuk tampilan. **Konfirmasi ke owner** sebelum dipakai final. Durasi di bawah adalah **asumsi sementara** dan wajib diverifikasi, karena durasi menentukan blok slot di kalender.

### Terapi & Refleksi
| Layanan | Harga | Durasi (asumsi) |
|---|---|---|
| Refleksi Seluruh Tubuh | Rp 80.000 | 60 mnt |
| Kop Masuk Angin | Rp 40.000 | 30 mnt |
| Kerik / Kerok | Rp 40.000 | 30 mnt |
| Bekam | Rp 70.000 | 45 mnt |

### Kuku & Rias Wajah
| Layanan | Harga | Durasi (asumsi) |
|---|---|---|
| Nail Art / Gel | Rp 150.000 | 90 mnt |
| Manicure | Rp 85.000 | 45 mnt |
| Pedicure | Rp 90.000 | 60 mnt |
| Make Up | Rp 150.000 – Rp 250.000 | 90 mnt |
| Lash Lift | Rp 70.000 | 45 mnt |
| Eyelash Extension | Rp 150.000 – Rp 250.000 | 90 mnt |

### Perawatan Wajah & Tubuh
| Layanan | Harga | Durasi (asumsi) |
|---|---|---|
| Totok Wajah | Rp 40.000 – Rp 85.000 | 45 mnt |
| Terapi Telinga | Rp 60.000 | 30 mnt |
| Slimming Body | Rp 100.000 | 60 mnt |
| Whitening Ketiak | Rp 60.000 | 30 mnt |
| Pengencangan Payudara | Rp 100.000 | 60 mnt |
| Spa Kewanitaan | Rp 60.000 | 45 mnt |
| Waxing | Rp 50.000 – Rp 300.000 | 30–90 mnt |
| Massage Full Body | Rp 80.000 | 60 mnt |

### Paket Khusus
| Paket | Harga | Isi paket |
|---|---|---|
| Paket Lulur Susu | Rp 200.000 | Scrub lulur, mandi susu, massage susu, steam, masker susu |
| Paket Lulur Susu + Bleaching | Rp 275.000 | Scrub lulur, masker, mandi susu, steam, massage, bleaching |
| Paket Bleaching | Rp 200.000 | Scrub lulur, masker, mandi susu, bleaching |

### Dampak ke skema database
1. **Harga berentang.** Beberapa layanan memiliki harga minimum dan maksimum. Tambahkan kolom **`price_max`** (nullable) pada `services`. Jika `price_max` terisi, `price` dianggap harga minimum. Untuk booking, `price_snapshot` memakai harga minimum, dan harga final disesuaikan kasir saat pembayaran.
2. **Isi paket.** Simpan sebagai daftar poin di kolom `description`, atau tambahkan tabel kecil `service_includes (service_id, label, sort_order)` jika ingin ditampilkan sebagai daftar centang yang dapat diubah owner. Untuk MVP, kolom `description` dengan baris per poin sudah cukup.
3. **Kategori** sesuai empat grup di atas melalui `service_categories`.
4. **Jam operasional:** satu baris per hari, semua 09:00–20:30, tidak ada hari libur tetap. Booking terakhir harus selesai (termasuk buffer) sebelum 20:30.

## 11. Pertimbangan Konten Sensitif

Beberapa layanan (misalnya Spa Kewanitaan dan Pengencangan Payudara) bersifat pribadi.

- Gunakan **nama netral** dan deskripsi singkat yang sopan.
- **Tanpa foto tubuh atau ilustrasi eksplisit.** Gunakan ikon netral atau foto suasana ruangan.
- Pertimbangkan label *"Khusus wanita"* dan pilihan staf perempuan pada layanan tersebut.
- Catatan pelanggan pada booking tidak ditampilkan di ruang publik atau layar yang menghadap pelanggan lain. Dashboard kasir harus punya opsi menyembunyikan catatan saat layar terlihat orang lain.
- Layanan ini boleh disembunyikan dari beranda dan "Layanan unggulan", tetapi tetap ada di katalog.

## 12. Panduan Gambar

| Jenis | Aturan |
|---|---|
| Hero | Foto interior dengan pencahayaan hangat, dipotong dalam bingkai lengkung. Wajah pelanggan diburamkan atau dipotong |
| Galeri | Maksimal 12 foto, rasio konsisten (4:5 atau 1:1), bingkai lengkung |
| Foto layanan | Tanpa foto pelanggan nyata. Gunakan foto alat, produk, atau tangan staf, atau ilustrasi bila belum ada |
| Foto staf | Hanya dengan persetujuan staf, rasio 1:1, latar seragam |
| Format dan ukuran | WebP, lebar maksimal 1200 px, upload maksimal 2 MB, `loading="lazy"` kecuali hero |
| Teks alternatif | Wajib diisi untuk setiap gambar bermakna |

Fallback saat foto belum ada: latar `sage-100` dengan ikon layanan dalam bingkai lengkung.

## 13. Aksesibilitas dan Performa

- Kontras teks minimal WCAG AA (4,5:1 untuk teks biasa).
- Semua fungsi dapat dipakai dengan keyboard. Fokus terlihat jelas (ring emas atau sage gelap).
- Target sentuh minimal 44 × 44 px.
- Form: label terhubung, error dibacakan (`aria-live`), input dipertahankan saat gagal validasi.
- `prefers-reduced-motion` dihormati.
- Anggaran performa halaman publik: LCP ≤ 3 detik di 4G, total gambar di atas fold ≤ 300 KB, font maksimal 3 berkas `.woff2`.
- Tailwind di-build dengan purge agar CSS akhir kecil.

## 14. Token Tailwind (acuan implementasi)

```js
// tailwind.config.js (potongan)
export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.js'],
  theme: {
    extend: {
      colors: {
        sage:  { 50:'#F2F6F1', 100:'#E2EBE0', 300:'#A9BDA5', 500:'#7E9A7B', 700:'#4F6B52', 900:'#2F4332' },
        cream: { 50:'#FBF8F2', 100:'#F5F0E4' },
        gold:  { 400:'#C9A45C', 700:'#7A5C1E' },
        marble:{ 200:'#DCDDDF' },
        ink:   { 600:'#5B635D', 900:'#262B27' },
        wine:  { 700:'#7A2432' },
      },
      fontFamily: {
        heading: ['"Cormorant Garamond"', 'serif'],
        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
      },
      borderRadius: { arch: '9999px 9999px 1.25rem 1.25rem' },
      boxShadow: { soft: '0 6px 24px -8px rgba(47,67,50,0.15)' },
    },
  },
};
```

## 15. Hal yang Perlu Dikonfirmasi ke Owner

1. **Logo resmi** dalam format vektor (SVG/PNG transparan) dan kode warna brand, jika ada.
2. **Izin pemakaian foto** interior, termasuk foto yang memperlihatkan staf dan pelanggan.
3. **Durasi sebenarnya** tiap layanan, dan apakah ada buffer antar pelanggan.
4. **Cara penetapan harga berentang** (Make Up, Eyelash Extension, Totok Wajah, Waxing): berdasarkan apa harga final ditentukan?
5. **Gaya sapaan** ("kamu" atau "Anda").
6. **Layanan yang hanya boleh dikerjakan staf tertentu** (misalnya staf perempuan untuk layanan sensitif).
7. **Nomor WhatsApp resmi** salon untuk tombol `wa.me`.
8. **Ejaan nama layanan** yang diinginkan owner (koreksi di bagian 10 baru usulan).

---

*Versi 0.1. Diturunkan dari foto interior dan data layanan yang diberikan. Perbarui setelah logo resmi, foto final, dan jawaban owner diterima.*
