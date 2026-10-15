# Wireframe Sistem Booking Salon

## A. Dampak Jawaban Owner ke PRD

| No | Jawaban | Dampak ke desain dan PRD |
|---|---|---|
| 1 | Pending, konfirmasi manual | Booking online masuk antrean "Perlu Konfirmasi" di dashboard kasir. Perlu indikator waktu tunggu agar tidak ada booking yang terlupakan. |
| 2 | Batas pembatalan 30 menit | Pelanggan bisa membatalkan hampir sampai jam datang, jadi slot yang dilepas bisa dipakai walk-in. Ini mengurangi kebutuhan notifikasi, tetapi meningkatkan risiko no-show mendadak. |
| 3 | Pembayaran tidak wajib | Status `completed` tidak mengunci pembayaran. Tombol pembayaran tetap ada, tetapi bersifat opsional. |
| 4 | Retensi 24 bulan tidak aktif | Perlu kolom `last_visit_at` di `customers`. Tinjauan bisa dilakukan manual, tanpa cron. |
| 5 | Ada layanan dengan keahlian khusus | Sudah ditangani oleh `service_staff`. Pastikan form "Siapa saja" hanya memilih staf yang bisa melakukan semua layanan terpilih. |
| 6 | Ada jam sibuk yang dikeluhkan | **Belum bisa dirancang tepat.** Perlu data: jam apa, hari apa, dan keluhannya soal menunggu atau sulit booking. Hipotesis sementara: jam sore dan akhir pekan. Lihat bagian B. |
| 7 | Ada kebiasaan "tahan slot" lewat telepon | **Mengubah model data.** Perlu status baru `hold` dan kolom `hold_until`. Lihat bagian C. |

### Perubahan model data

Tambahkan ke tabel `bookings`:

- Status baru: `hold` (slot ditahan lewat telepon, belum jadi booking).
- Kolom `hold_until` (datetime, nullable).

**Logika kunci tanpa cron:** hold dianggap aktif hanya jika `hold_until > sekarang`. Saat cek ketersediaan, hold yang sudah lewat waktunya diperlakukan sebagai slot kosong. Dengan cara ini, fitur "tahan slot" tidak membutuhkan cron, sehingga tetap berjalan di hosting Mini Plus.

Tambahan aturan bisnis:
- Hold punya durasi default 30 menit, yang bisa diperpanjang kasir satu klik.
- Hold yang sudah dikonfirmasi pelanggan dan diubah menjadi booking `pending` atau `confirmed`.

---

## B. Wireframe 1: Walk-in (prioritas tertinggi)

Target: tercatat dalam 15 detik. Layar ini dibuka dari tombol global di pojok kanan atas dashboard, dan bisa diakses dari halaman mana pun.

```
┌──────────────────────────────────────────────┐
│ ← Kembali          WALK-IN BARU        ✕    │
├──────────────────────────────────────────────┤
│ Layanan                                      │
│ [✓ Haircut 45m] [ Creambath 60m ] [ Smoothing ]│
│ [ Hair Color 120m ] [ + Lainnya ]            │
│ Sering dipilih: Haircut, Creambath           │
│                                              │
│ Staf                                         │
│ ( ● Siapa saja )  ( Rina ✕ )  ( Dimas ✕ )    │
│ ( Sari ✓ ) ( Tono ⚠ sibuk s/d 15.30 )        │
│   ✓ = bisa layanan terpilih                  │
│   ✕ = tidak bisa layanan ini                 │
│                                              │
│ Mulai: [ 14.20 ▾ ]  (default: sekarang)      │
│ Selesai perkiraan: 15.05                     │
│                                              │
│ Nama     [ ____________________ ]            │
│ Telepon  [ ____________________ ] (opsional) │
│   ↳ Ditemukan: Ibu Ayu (8 kunjungan) [Pilih] │
│                                              │
│ [ SIMPAN & MULAI ]                           │
└──────────────────────────────────────────────┘
```

### Catatan desain
- **Layanan dan staf pertama, data pelanggan terakhir.** Nama pelanggan biasanya diisi paling akhir, dan sebagian kasir tidak mengisinya sama sekali. Karena itu, nama tidak boleh wajib.
- **"Sering dipilih" memakai riwayat layanan**, sehingga satu sentuhan sudah cukup untuk pelanggan yang biasa datang.
- **Hari ini langsung terisi:** jam default adalah sekarang, dibulatkan ke 5 menit terdekat.
- **Staf sibuk tetap ditampilkan** dengan keterangan jamnya, sehingga kasir tahu opsi mana yang tersedia tanpa membuka kalender.
- **Walk-in langsung `confirmed`** dan mengunci slot, sesuai PRD WI-04.
- **Jika jam tidak cocok atau slot bentrok,** sistem menampilkan pesan dan menyarankan staf lain atau jam terdekat yang kosong. Pesan tidak boleh hanya "gagal".

---

## C. Wireframe 2: Tahan Slot via Telepon

Dibuka dari kalender dengan klik pada slot kosong, lalu memilih "Tahan".

```
┌──────────────────────────────────────────────┐
│ TAHAN SLOT                              ✕    │
├──────────────────────────────────────────────┤
│ Staf: Rina     Tanggal: Sab 17 Okt           │
│ Jam: 10.00 – 11.00 (Creambath 60m)           │
│                                              │
│ Nama pemesan   [ Mbak Dewi ________ ]        │
│ Telepon        [ 0812-xxxx-xxxx    ]         │
│                                              │
│ Tahan sampai   ( ● 30 menit )  ( 1 jam )     │
│                ( Sampai jam [ 17.00 ] )      │
│                                              │
│ Catatan   [ minta dikabari jika jadwal    ]  │
│           [ bisa dimajukan               ]   │
│                                              │
│ [ TAHAN SLOT ]                               │
└──────────────────────────────────────────────┘

Setelah ditahan, blok di kalender:
┌─────────────────────────┐
│ ⏳ DITAHAN  Dewi         │
│ Creambath · sisa 25 mnt │
│ [Konfirmasi] [Lepas]    │
└─────────────────────────┘
```

### Alur yang perlu dipastikan
- **Hold tidak diam-diam kedaluwarsa.** Jika pelanggan belum konfirmasi, kasir melihat blok ini berubah menjadi "Waktu habis, slot dilepas" beberapa menit sebelum dilepas. Ini mencegah slot hilang tanpa diketahui.
- **Konfirmasi hold** mengubah status menjadi `pending` atau `confirmed`, tergantung keputusan pada pertanyaan 1 (yang sudah dijawab `pending`).
- **Hold tampil sebagai slot terisi** di halaman publik, sehingga pelanggan online tidak bisa memesan slot yang sedang ditahan.
- **Batas maksimal hold** sebaiknya ditetapkan, misalnya 2 jam, agar tidak ada slot yang ditahan sepanjang hari.

---

## D. Wireframe 3: Kalender Harian Kasir

Tampilan utama dashboard. Setiap kolom adalah satu staf, dan indikator jam sibuk ditampilkan di bagian atas.

```
┌────────────────────────────────────────────────────────────────┐
│ Kalender  ◀ Sab 17 Okt ▶   [Hari] [Minggu]  Filter: Semua ▾   │
│ [+ Walk-in]  [+ Tahan]        Perlu konfirmasi: 3 ⚠            │
├────────────────────────────────────────────────────────────────┤
│ Jam Sibuk   ▁▁ ▂ ▅ ▇ ▇ █ █ ▇ ▅ ▃ ▂ ▁   (booking per jam)      │
├──────┬──────────┬──────────┬──────────┬──────────┬─────────────┤
│ Jam  │  Rina    │  Sari    │  Dimas   │  Tono    │  Tanpa staf │
├──────┼──────────┼──────────┼──────────┼──────────┼─────────────┤
│ 10.00│ ████████ │          │ ░░░░░░░░ │          │             │
│      │ Dewi     │  + Slot  │ DITAHAN  │  + Slot  │             │
│ 11.00│ Creamb.  │          │ Budi     │          │             │
│ 12.00│          │ ▓▓▓▓▓▓▓▓ │          │ ▓▓▓▓▓▓▓▓ │             │
│      │ ── break ──│ Ani     │          │ Lia      │             │
│ 13.00│          │ Hair Clr │          │ Smoothing│             │
│ ...  │          │          │          │          │             │
└──────┴──────────┴──────────┴──────────┴──────────┴─────────────┘

Legenda:  ▓ confirmed   ░ ditahan   ▒ pending (perlu konfirmasi)
          ☐ completed   ✕ no-show   ─ buffer   + Slot = klik untuk isi
```

### Catatan desain
- **Booking `pending` berwarna berbeda** dan muncul di daftar "Perlu konfirmasi". Ini menjawab pertanyaan 1, agar tidak ada booking yang terlewat.
- **Indikator jam sibuk di bagian atas** menjawab pertanyaan 6. Diagram ini memperlihatkan booking per jam, sehingga kasir dan owner bisa melihat pola sendiri. Pola nyata perlu diverifikasi dengan data pilot, bukan asumsi.
- **Kolom "Tanpa staf"** untuk booking "Siapa saja" yang belum ditetapkan. Ini bisa terjadi jika sistem menunda penentuan staf sampai konfirmasi.
- **Buffer ditampilkan** sebagai bagian yang diarsir, sehingga kasir melihat alasan kenapa slot tertentu tidak bisa dipilih.
- **Mobile:** pada layar kecil, kalender berubah menjadi daftar per staf, bukan grid. Tablet tetap memakai grid.

---

## E. Wireframe 4: Form Booking Publik

Alur 5 langkah, ditampilkan sebagai satu halaman dengan step indicator agar pelanggan tahu progresnya.

```
┌──────────────────────────────────────────┐
│ [Logo]  Layanan  Harga  Lokasi   [Booking]│
├──────────────────────────────────────────┤
│  ●──────●──────○──────○──────○           │
│  Layanan Staf  Jadwal Data  Konfirmasi   │
│                                          │
│ LANGKAH 1: Pilih layanan                 │
│ Cari: [ ________________ ]               │
│ ▸ Rambut                                 │
│   [✓] Haircut          45m   Rp 75.000   │
│   [ ] Creambath        60m   Rp 120.000  │
│   [ ] Hair Color       120m  Rp 350.000  │
│ ▸ Perawatan wajah                        │
│   [ ] Facial           60m   Rp 150.000  │
│   [ ] Totok wajah      45m   ✕ Tidak ada│
│                                          │
│ Dipilih: 1 layanan · 45 menit · Rp75.000 │
│                          [ LANJUT → ]    │
└──────────────────────────────────────────┘

LANGKAH 3: Pilih jadwal (setelah staf dipilih)
┌──────────────────────────────────────────┐
│ Tanggal   ◀  Okt 2026  ▶                 │
│ Sen Sel Rab Kam Jum [Sab] Min            │
│  12  13  14  15  16  [17]  18            │
│                                          │
│ Jam tersedia (Sab 17 Okt, Rina):         │
│ ( 10.00 ) ( 10.30 ) ( 11.00 ) (✕ 11.30)  │
│ ( 12.00 ) ( 13.00 ) (✕ 14.00 ) ...      │
│                                          │
│ ✕ = penuh atau ditahan                   │
│ ⚠ Jam ramai: sebagian slot cepat habis  │
│                          [ LANJUT → ]    │
└──────────────────────────────────────────┘

LANGKAH 5: Konfirmasi
┌──────────────────────────────────────────┐
│ ✓ Booking diterima (menunggu konfirmasi) │
│                                          │
│ Kode booking:  K7F3-9QXM                 │
│ Haircut · Rina · Sab 17 Okt · 10.00      │
│                                          │
│ Simpan kode ini untuk cek status.        │
│                                          │
│ [ Kirim detail ke WhatsApp salon ]       │
│ [ Cek status booking ]                   │
└──────────────────────────────────────────┘
```

### Catatan desain
- **Harga disembunyikan sebagian di langkah 1?** Tidak. Harga tetap terlihat sejak awal karena pelanggan Indonesia biasanya membandingkan harga sebelum booking.
- **Slot ditahan dan penuh** tampil sama-sama tidak tersedia, tanpa memberi tahu bahwa slot tersebut sedang ditahan. Ini melindungi privasi pemesan telepon.
- **"⚠ Jam ramai"** hanya tampil jika data pilot menunjukkan pola tersebut. Jangan dipasang permanen sebelum ada data.
- **Langkah konfirmasi** menyebut "menunggu konfirmasi" dengan jelas. Pelanggan sering mengira booking sudah pasti setelah melihat kode, sehingga kalimat ini mencegah kebingungan dan keluhan saat kasir belum mengonfirmasi.
- **Tombol WA** mengisi pesan otomatis dengan kode booking, sehingga kasir bisa mencari booking dengan cepat.

---

## F. Keputusan UX yang Perlu Divalidasi

| Keputusan | Asumsi awal | Cara validasi |
|---|---|---|
| Walk-in selesai dalam 15 detik | Form ringkas cukup | Stopwatch pada 5 walk-in nyata di hari pertama pilot |
| Kasir mau memakai kalender | Grid per staf mudah dibaca | Tunjukkan wireframe ke dua kasir dan minta mereka menunjuk tombol yang dicari |
| Hold 30 menit cukup | Pelanggan telepon biasanya konfirmasi cepat | Catat berapa hold yang berakhir tanpa konfirmasi selama dua minggu |
| Indikator jam sibuk membantu | Ada pola siang hingga sore | Bandingkan dengan data booking pilot; minta owner menjelaskan keluhan spesifik |
| Pending dikonfirmasi tepat waktu | Kasir konfirmasi dalam hari yang sama | Tambahkan metrik "waktu rata-rata dari pending ke confirmed" |

---