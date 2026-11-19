# Soraya Cafe — QR Table Ordering

Aplikasi pemesanan mandiri berbasis **QR meja** untuk kafe — pelanggan memindai QR, memilih menu dengan catatan/modifier, membayar melalui **iPaymu**, lalu memantau progres pesanan dapur hingga memberi ulasan.

![Laravel 12](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Endroid QR](https://img.shields.io/badge/QR-Endroid_v6-111827?style=flat-square)
![Payment](https://img.shields.io/badge/Gateway-iPaymu_v2-16A34A?style=flat-square)

### Alur Order & Rute Utama

- **Customer (`/t/{token}` → `/menu` → `/checkout` → `/order/{code}`)** — Deteksi meja otomatis via token QR, keranjang, checkout iPaymu, status pesanan *real-time*, dan feedback 1× setelah pesanan `SELESAI`.
- **Admin (`/admin/tables`, `/admin/orders`)** — Generator QR per meja siap cetak, manajemen menu, pembaruan status (`DITERIMA → DIPROSES → READY → SELESAI`), serta webhook idempoten `/ipaymu/notify`.

```bash
composer install && cp .env.example .env && php artisan key:generate
php artisan migrate && php artisan db:seed
php artisan serve
```
