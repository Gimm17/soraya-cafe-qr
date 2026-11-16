# Changelog Sesi Kerja — 6–7 Maret 2026

## Soraya Cafe QR · Bersama Antigravity AI

> **Scope:** 12 commits · 59 file berubah · +2.994 baris ditambah · -935 baris dihapus
> **Periode:** 6 Maret 2026 (mulai redesign) hingga 7 Maret 2026 (selesai cron job)

---

## 1. 🎨 Redesign Total Admin Panel — "Soraya Blue"

**Commit:** `d41a7ee` · 6 Mar 2026

Ini titik awal sesi ini. Seluruh tampilan admin panel dibangun ulang dari nol dengan sistem desain baru.

### Apa yang berubah?

**Sistem Desain Baru:**

- Warna utama: **Navy Blue `#1E3A8A`** (sidebar, tombol, aksen)
- Background: **putih bersih** dengan card berbayangan halus
- Typography: Inter (Google Fonts), ukuran dan weight yang konsisten
- Komponen: rounded-xl, shadow-soft, hover transitions

**Sidebar (`admin-layout.blade.php`):**

- Sidebar fixed kiri, lebar 256px, background navy gradient
- Setiap menu punya ikon SVG + label, dengan state `active` (background putih transparan)
- Logo cafe bulat dengan ring putih
- Tombol logout merah di bawah
- Mobile: overlay hamburger menu yang smooth

**Halaman yang didesain ulang:**

| Halaman        | Perubahan Utama                                                                                                                                       |
| -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Dashboard**  | 4 KPI cards (Total Orders, Pending, Revenue, Customers) dengan ikon berwarna. Chart sederhana. Quick stats.                                           |
| **Orders**     | Layout kanban dengan 4 kolom berwarna (DITERIMA=biru, DIPROSES=kuning, SIAP=hijau, SELESAI=abu). Badge status per kartu. Tombol update status inline. |
| **Products**   | Grid card produk dengan gambar, harga, badge kategori, tombol edit/hapus                                                                              |
| **Categories** | Tabel bersih dengan drag-sort order                                                                                                                   |
| **Modifiers**  | Grup modifier dengan toggle aktif/nonaktif                                                                                                            |
| **Meja & QR**  | Card per meja dengan preview QR dan tombol download                                                                                                   |
| **Feedback**   | Tabel rating bintang dengan filter dan pagination                                                                                                     |
| **Reports**    | Chart penjualan, top produk, ringkasan bulanan                                                                                                        |
| **Settings**   | Kartu per seksi (Cafe status, Close Order, Notifikasi, Struk)                                                                                         |

**File utama yang berubah:**

- `resources/views/components/admin-layout.blade.php` — layout shell
- `resources/views/admin/dashboard.blade.php`
- `resources/views/admin/orders.blade.php`
- `resources/views/admin/products.blade.php`
- `resources/views/admin/categories.blade.php`
- `resources/views/admin/tables.blade.php`
- `resources/views/admin/feedback.blade.php`
- `resources/views/admin/reports.blade.php`
- `resources/views/admin/settings.blade.php`
- `resources/css/app.css` — utility classes baru

---

## 2. 🛍️ Redesign Halaman Customer — Cafe Pages

**Commit:** `1affe97` · 6 Mar 2026

Setelah admin, giliran seluruh halaman yang dilihat pelanggan (user side) didesain ulang.

### Apa yang berubah?

**Menu (`cafe/menu.blade.php`):**

- Header sticky dengan nama cafe, info meja, dan ikon keranjang + badge item count
- Grid produk 2 kolom, setiap card: gambar, nama, deskripsi pendek, harga, tombol tambah
- Filter kategori tab horizontal (scroll horizontal di mobile)
- Skeleton loading saat produk dimuat

**Product Detail (`cafe/product.blade.php`):**

- Hero image full-width dengan overlay gradient
- Bagian modifier/varian: radio button (pilih satu) dan checkbox (multi-pilih)
- Harga total auto-update saat varian dipilih
- Tombol "Tambah ke Keranjang" sticky di bawah
- Accordion untuk deskripsi panjang

**Cart (`cafe/cart.blade.php`):**

- List item dengan gambar mini, nama, varian, qty controls (−/+)
- Catatan per item (input)
- Summary subtotal sticky di bawah
- Tombol checkout prominent

**Landing Table (`cafe/table_landing.blade.php`):**

- Halaman selamat datang saat scan QR, dengan nama cafe, nomor meja, dan tombol "Mulai Pesan"

**Bonus:**

- Tombol admin di navbar → redirect ke dashboard jika sudah login (bukan ke halaman login lagi)
- `home.blade.php` → guard fix agar tidak crash jika belum ada session

**File utama yang berubah:**

- `resources/views/cafe/menu.blade.php`
- `resources/views/cafe/product.blade.php`
- `resources/views/cafe/cart.blade.php`
- `resources/views/cafe/table_landing.blade.php`
- `resources/views/home.blade.php`
- `resources/views/components/cafe-layout.blade.php`
- `resources/views/partials/brand-head.blade.php`

---

## 3. 🔧 Fix SSL + Redirect

**Commit:** `5a7f9ce` · 7 Mar 2026

Perbaikan cepat tiga isu yang ditemukan setelah deploy:

| Isu                                    | Fix                                                      |
| -------------------------------------- | -------------------------------------------------------- |
| Midtrans cURL error SSL di localhost   | `CURLOPT_SSL_VERIFYPEER` dimatikan di env non-production |
| Tombol admin di `home.blade.php` crash | Tambah guard `@auth` sebelum cek role                    |
| Redirect admin btn                     | Sudah di-fix lebih tepat di commit ini                   |

**File:** `app/Services/Midtrans/MidtransClient.php`, `resources/views/home.blade.php`

---

## 4. 💳 Fix Client-Side Payment Verify

**Commit:** `fa202ce` · 7 Mar 2026

**Masalah:** Di localhost, webhook Midtrans tidak bisa menjangkau server (karena tidak ada public URL). Akibatnya, setelah user bayar lewat Snap popup, status order tidak berubah ke PAID meski pembayaran berhasil.

**Fix:** Setelah Snap callback `onSuccess` atau `onPending` dipanggil, browser langsung call endpoint `POST /pay/{order}/verify`. Server kemudian **tanya langsung ke Midtrans API** pakai Server Key (bukan percaya data dari browser), lalu update database.

```
Snap onSuccess → browser POST /verify → server GET Midtrans API → DB update PAID
```

**Kenapa aman?** Browser hanya kirim sinyal "tolong cek". Server yang independen mengambil data dari Midtrans. User tidak bisa manipulasi status.

**File:** `resources/views/cafe/pay.blade.php`, `app/Http/Controllers/Cafe/PaymentController.php`

---

## 5. 🔄 Realtime Polling Admin Orders

**Commit:** `37b8609` · 7 Mar 2026

**Masalah awal:** Sisi customer sudah realtime (update status secara otomatis). Tapi di sisi admin, ketika ada order masuk dari customer, admin **harus refresh manual** untuk melihatnya. Ini tidak efisien.

**Solusi yang diimplementasi:**

```
Admin page → setInterval polling → GET /admin/orders/poll (JSON)
↓ ETag check (304 Not Modified jika tidak ada perubahan)
↓ Jika ada perubahan → update UI tanpa reload halaman
↓ Exponential backoff (gagal → tunggu lebih lama, sukses → kembali cepat)
↓ Visibility API → stop polling saat tab tidak aktif / minimize
```

**Fitur teknis:**

- **ETag:** Server kirim hash dari data. Client simpan hash. Request berikutnya kirim `If-None-Match`. Jika sama → `304 Not Modified` tanpa kirim data → hemat bandwidth
- **Exponential backoff:** Poll default 10 detik, jika server error → 20s → 40s → max 2 menit
- **Visibility API:** `document.addEventListener('visibilitychange')` → jika tab hidden, polling berhenti. Saat tab aktif lagi, poll langsung
- **Kanban update:** Saat ada order baru atau status berubah, kartu order berpindah kolom dengan animasi

**File:** `resources/views/admin/orders.blade.php`, `app/Http/Controllers/Admin/AdminOrderController.php`

---

## 6. 🧾 Fix Tombol "Lihat Struk"

**Commit:** `458cea4` · 7 Mar 2026

**Masalah:** Tombol "Lihat Struk" di halaman order customer baru muncul setelah customer mengisi review/feedback. Jika customer tidak mau review, mereka tidak bisa akses struk.

**Logika sebelumnya (salah):**

```
Order SELESAI + Feedback diberikan → tombol Lihat Struk muncul
```

**Logika sesudah fix (benar):**

```
Order SELESAI → tombol Lihat Struk LANGSUNG muncul
(Review tetap opsional, muncul terpisah)
```

**File:** `resources/views/cafe/order.blade.php`

---

## 7. 🎨 Redesign Struk — Soraya Brand v2

**Commit:** `a076a7b` · 7 Mar 2026

Struk didesain ulang penuh agar konsisten dengan brand Soraya Blue.

**Sebelum:**

- Desain generik, hitam-putih polos
- Tidak ada identitas brand

**Sesudah:**

- **Header biru** dengan nama cafe besar dan info alamat/kontak
- **Badge qty** per item (misal: `×2` dengan lingkaran biru)
- **Payment pill** — badge warna untuk metode bayar (💳 QRIS, 💵 Tunai)
- **Footer biru** dengan pesan terima kasih
- **Responsive** untuk 80mm thermal printer dan A4

**File:** `resources/views/receipt.blade.php` (ditulis ulang dari 263 → 416 baris)

---

## 8. ⚙️ Customisasi Struk Diperluas — 8 Seksi

**Commit:** `fc6560d` · 7 Mar 2026

Setting struk diperluas dari 4 opsi menjadi **8 seksi customisasi penuh**.

| Seksi                 | Opsi yang Tersedia                                                                         |
| --------------------- | ------------------------------------------------------------------------------------------ |
| **🖼️ Logo**           | Upload logo, toggle tampilkan, ukuran (px), jarak bawah (px)                               |
| **🔤 Tipografi**      | Pilihan font (System / Serif / Monospace), ukuran header, ukuran body                      |
| **🎨 Warna & Tema**   | 4 tema preset (Normal / Dark / Soraya Blue / Hitam-Putih), warna aksen custom, warna total |
| **〰️ Garis & Batas**  | Style garis (solid / dashed / dotted / none), warna garis                                  |
| **📄 Ukuran Kertas**  | 80mm Thermal, A5, A4                                                                       |
| **👁️ Elemen**         | Toggle: nama customer, metode bayar, catatan item, tipe order, nomor meja                  |
| **📝 Footer**         | Teks footer utama, teks sekunder, ukuran font footer                                       |
| **🏪 Identitas Cafe** | Override nama cafe + tagline khusus untuk struk                                            |

**File:** `resources/views/admin/settings.blade.php` (bertambah ~200 baris), `app/Http/Controllers/Admin/AdminSettingController.php`, `resources/views/receipt.blade.php`

---

## 9. 🏪 Customisasi Nama & Tagline di Struk

**Commit:** `62b26d0` · 7 Mar 2026

Tambahan kecil namun penting: setting **nama cafe** dan **tagline** yang khusus dipakai di struk, terpisah dari nama umum. Berguna jika nama legal berbeda dari nama display, atau jika ingin format berbeda di struk cetak.

**Contoh:**

- Nama umum: `Soraya`
- Nama di struk: `Soraya Coffee & Friends`
- Tagline di struk: `Jl. Contoh No. 1, Kota`

**File:** `resources/views/admin/settings.blade.php`, `app/Http/Controllers/Admin/AdminSettingController.php`, `resources/views/receipt.blade.php`

---

## 10. 🔔 SweetAlert2 — Semua Tombol CRUD

**Commit:** `5b5e3bd` · 7 Mar 2026

**Masalah:** Beberapa tombol CRUD di admin sudah pakai konfirmasi SweetAlert2, tapi tidak semua. Tombol-tombol berbahaya (hapus, toggle status) ada yang langsung eksekusi tanpa konfirmasi.

**Yang ditambahkan di panel admin:**

| Tombol                       | Sebelum                        | Sesudah            |
| ---------------------------- | ------------------------------ | ------------------ |
| Toggle Cafe Buka/Tutup       | Langsung form submit           | SweetAlert confirm |
| Toggle Banner aktif/nonaktif | Langsung                       | SweetAlert confirm |
| Hapus Banner                 | `confirm()` bawaan browser     | SweetAlert merah   |
| Toggle Feedback pinned       | Langsung                       | SweetAlert confirm |
| Hapus Feedback               | Tidak ada konfirmasi           | SweetAlert merah   |
| Hapus Produk                 | Sudah ada tapi tidak konsisten | Disamakan style    |
| Hapus Kategori               | Tidak semua ada                | Dilengkapi         |

**Di sisi customer (yang diperlukan):**

- Tombol **Batalkan Pesanan** → SweetAlert dengan penjelasan konsekuensi

**Desain konsisten:** Semua menggunakan `Swal.mixin` dengan:

- Tombol konfirmasi merah untuk destructive action
- Tombol batal abu-abu
- Icon `warning` untuk yang reversible, `error` untuk permanent

**File:** `resources/views/admin/banners.blade.php`, `resources/views/admin/feedback.blade.php`, `resources/views/components/admin-layout.blade.php`, `resources/views/admin/settings.blade.php`, `resources/views/cafe/order.blade.php`

---

## 11. 🖥️ Fitur POS / Kasir

**Commit:** `893fa04` · 7 Mar 2026

Fitur terbesar dalam sesi ini — implementasi sistem kasir (Point of Sale) lengkap.

### Background

Selama ini, order hanya bisa dari customer lewat QR scan. Tidak ada cara bagi barista/kasir untuk input order manual untuk pelanggan walk-in yang tidak mau scan QR.

### Database Changes

```sql
-- Tabel orders
ALTER TABLE orders ADD COLUMN order_source VARCHAR(20) DEFAULT 'QR';
-- Nilai: 'QR' (dari customer) | 'POS' (dari kasir)

-- Tabel payments
ALTER TABLE payments ADD COLUMN cash_received BIGINT NULL;
ALTER TABLE payments ADD COLUMN cash_change BIGINT NULL;
```

**Migration:** `database/migrations/2026_03_07_015008_add_pos_fields_to_orders_and_payments.php`

### Backend

**`app/Http/Controllers/Admin/AdminPosController.php`** (baru, 171 baris):

- `index()` — load halaman POS dengan data produk, kategori, meja, dan settings
- `products()` — JSON API produk dengan modifiers untuk dynamic loading
- `store()` — buat order baru dari POS (validasi + call OrderService)
- `confirmQris()` — konfirmasi pembayaran QRIS manual oleh kasir
- `checkPayment()` — polling status pembayaran untuk auto-confirm

**`app/Services/OrderService.php`** — tambah method `createPosOrder()`:

- Set `order_source = 'POS'`
- Kode order prefix `K-` (bukan `A-`)
- Handle pembayaran CASH: langsung PAID, simpan `cash_received`, `cash_change`
- Handle pembayaran QRIS: status PENDING, tunggu konfirmasi

**Routes baru di `routes/cafe_admin.php`:**

```
GET  /admin/pos                    → admin.pos
GET  /admin/pos/products           → admin.pos.products
POST /admin/pos/order              → admin.pos.order
POST /admin/pos/confirm-qris/{id}  → admin.pos.confirm-qris
GET  /admin/pos/check-payment/{id} → admin.pos.check-payment
```

### Frontend — `resources/views/admin/pos.blade.php` (665 baris)

**Layout Split-Screen:**

```
┌──────────────────────────┬───────────────────┐
│   Katalog Produk (kiri)  │  Panel Order (kanan)│
│   - Search               │  - Nama Customer   │
│   - Filter Kategori      │  - No. Meja        │
│   - Grid Produk          │  - Keranjang       │
│                          │  - Subtotal/Total  │
│                          │  - Metode Bayar    │
│                          │  - Proses Pesanan  │
└──────────────────────────┴───────────────────┘
```

**Fitur Katalog:**

- Search real-time saat mengetik (filter client-side)
- Tab kategori horizontal, `active` state dengan background navy
- Product card: gambar, nama, harga — klik untuk tambah ke keranjang
- Jika produk punya modifier → modal varian terbuka

**Modal Modifier:**

- Tampilkan semua grup modifier (misal: Sugar Level, Ice Level, Add-ons)
- Single select (radio) atau multi-select (checkbox) per grup
- Harga modifier otomatis dijumlahkan ke unit price
- Cart key unik per kombinasi modifier → Americano Normal + Extra Shot ≠ Americano Normal

**Panel Order:**

- Input nama customer + dropdown pilih meja
- List item di keranjang: nama, varian summary, qty +/−, catatan, harga
- Subtotal + Grand Total auto-update
- Radio button Tunai / QRIS

**Pembayaran Tunai (opsional di Settings):**

- Input "Uang Diterima"
- Kembalian otomatis = Diterima − Total
- Warna hijau jika cukup, merah jika kurang

**Pembayaran QRIS:**

- Tampilkan gambar QRIS statis yang di-upload di Settings
- Setelah order dibuat → modal "Menunggu Pembayaran QRIS"
- Polling otomatis ke server tiap 3 detik (untuk auto-confirm jika ada webhook)
- Tombol "✅ Sudah Dibayar" untuk konfirmasi manual kasir

**Modal Sukses:**

- Nomor order (K-YYYYMMDD-XXXXX)
- Total pembayaran
- Tombol 🖨️ Cetak Struk (buka receipt di tab baru)
- Tombol ➕ Pesanan Baru (reset form)

**🔊 Audio Notifikasi (Web Speech API):**

```javascript
speakNominal(27000, "QRIS");
// Output suara: "Pembayaran QRIS diterima. Dua puluh tujuh ribu rupiah."
```

- Konversi angka ke kata Indonesia (`numberToIndonesianWords()`)
- Support hingga miliaran
- Bisa dimatikan dari Settings (toggle)

**Sidebar:** Tambah link "Kasir" dengan ikon register di desktop dan mobile menu.

**Update Receipt:** Struk POS menampilkan:

- Label "💵 Tunai" atau "📱 QRIS"
- Uang Diterima dan Kembalian (jika cash)

### Settings Baru

Di halaman **Settings > Pengaturan Kasir (POS):**

- Toggle kolom uang tunai (tampilkan/sembunyikan Uang Diterima + Kembalian)
- Toggle audio notifikasi
- Pilihan mode QRIS: Eksternal (upload gambar) / Midtrans (auto-confirm webhook)
- Upload gambar QRIS statis (PNG/JPG/WEBP, max 2MB)

---

## 12. ⏱️ Cron Job — Multi-Project Payment Checker

**Commit:** `78f9714` · 7 Mar 2026

### Background & Masalah

Midtrans hanya menyediakan **1 webhook URL** per environment (Sandbox/Production). Jika kamu punya beberapa project Laravel yang pakai 1 akun Midtrans yang sama, hanya 1 project yang bisa dapat notifikasi webhook.

**Solusi:** Tidak bergantung pada webhook — setiap project aktif "jemput bola" sendiri ke Midtrans API secara berkala.

### Arsitektur 3 Jalur Pembayaran

```
User Bayar
    │
    ├── Jalur 1: Client-Side (CEPAT)
    │   Browser → POST /verify → Server query Midtrans API → DB update
    │   Cocok: user masih buka browser saat bayar
    │
    ├── Jalur 2: Webhook (OTOMATIS)
    │   Midtrans → POST /midtrans/notify → Server verify signature → DB update
    │   Cocok: saat webhook URL terdaftar dan aktif
    │
    └── Jalur 3: Cron Job (SAFETY NET) ✨ BARU
        Setiap 5 menit → Server query Midtrans API → DB update
        Cocok: user tutup browser, webhook down, multi-project
```

### `app/Services/PaymentService.php` (baru, 93 baris)

Service terpusat yang dipakai oleh ketiga jalur:

```php
public function syncPaymentStatus(Order $order): string
{
    return DB::transaction(function () use ($order) {
        $order = Order::lockForUpdate()->find($order->id); // Anti race condition

        if ($order->payment_status === 'PAID') return 'PAID'; // Idempotency

        $statusData = $this->midtrans->getStatus($order->order_code); // Server Key
        // ... update berdasarkan: settlement, capture, pending, deny, expire
    });
}
```

**Fitur keamanan:**

- `DB::transaction` + `lockForUpdate()` → jika cron dan webhook jalan bersamaan, yang pertama dapat lock, yang kedua skip (sudah PAID)
- Data update 100% dari Midtrans API — tidak dari browser/request user

### `app/Console/Commands/CheckPendingPayments.php` (baru, 65 baris)

```bash
php artisan payments:check-pending
php artisan payments:check-pending --hours=48 --limit=20
```

**Cara kerja:**

1. Query orders: `payment_status = PENDING` + dibuat dalam 24 jam terakhir + gateway = midtrans
2. Loop max 10 order per run
3. Untuk setiap order: call `PaymentService::syncPaymentStatus()`
4. Delay 1 detik antar order (hindari rate limit Midtrans)
5. Log hasil tiap order

### Refactor `PaymentController`

`verifyPayment()` (jalur 1) sebelumnya punya 50+ baris logik duplikat. Sekarang cukup:

```php
public function verifyPayment(Order $order)
{
    $status = $this->paymentService->syncPaymentStatus($order);
    return response()->json(['status' => $status]);
}
```

### Schedule di `routes/console.php`

```php
Schedule::command('payments:check-pending')->everyFiveMinutes();
```

Untuk production, tambah crontab di server:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Ringkasan Sesi Ini

| Metrik         | Nilai                                                                                                  |
| -------------- | ------------------------------------------------------------------------------------------------------ |
| Jumlah commit  | **12 commits**                                                                                         |
| File berubah   | **59 file**                                                                                            |
| Baris ditambah | **+2.994**                                                                                             |
| Baris dihapus  | **-935**                                                                                               |
| File baru      | AdminPosController, PaymentService, CheckPendingPayments, pos.blade.php, PaymentService, Migration POS |

### Fitur Besar yang Selesai

| #   | Fitur                                        | Kompleksitas |
| --- | -------------------------------------------- | ------------ |
| 1   | Redesign Admin Panel "Soraya Blue"           | 🔴 Besar     |
| 2   | Redesign Cafe Pages (Customer)               | 🔴 Besar     |
| 3   | Fix SSL + Redirect Issues                    | 🟢 Kecil     |
| 4   | Client-Side Payment Verify                   | 🟡 Sedang    |
| 5   | Realtime JSON Polling Admin (ETag + backoff) | 🟡 Sedang    |
| 6   | Fix Tombol Lihat Struk                       | 🟢 Kecil     |
| 7   | Redesign Struk Soraya Brand                  | 🟡 Sedang    |
| 8   | Customisasi Struk 8 Seksi                    | 🟡 Sedang    |
| 9   | Override Nama Cafe di Struk                  | 🟢 Kecil     |
| 10  | SweetAlert2 Semua CRUD                       | 🟡 Sedang    |
| 11  | **POS / Kasir Feature**                      | 🔴 Besar     |
| 12  | **Cron Job Multi-Project**                   | 🟡 Sedang    |
