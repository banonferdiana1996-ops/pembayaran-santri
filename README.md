# SIP SANTRI PPDS

**Sistem Informasi Pembayaran Santri Pondok Pesantren Darussalam Putri**

Aplikasi web untuk mengelola pembayaran santri secara digital: manajemen santri & kelas, pembuatan tagihan massal, pencatatan pembayaran dengan kwitansi PDF, kas (pemasukan/pengeluaran), laporan (PDF & Excel), pengumuman, pengaturan aplikasi, hingga backup database.

Dibangun dengan **Laravel 12** + **PHP 8.3** + **MySQL/MariaDB**, front-end **AdminLTE 3 (Bootstrap 5)**, dan **Spatie Laravel Permission** untuk kontrol akses berbasis peran.

---

## Fitur

| Modul | Keterangan |
| --- | --- |
| **Autentikasi** | Login/logout custom, dashboard per peran (keuangan / santri / wali) |
| **Master Data** | CRUD tahun ajaran, kelas, santri (dengan foto), dan pengguna |
| **Jenis Pembayaran** | Kelola jenis pembayaran (bulanan / sekali bayar) beserta nominal |
| **Tagihan** | Generate tagihan massal per jenis/tahun ajaran/kelas, filter status, deduplikasi otomatis |
| **Pembayaran** | Catat pembayaran (tunai/transfer), cek tagihan belum lunas, **kwitansi HTML & PDF** |
| **Kas** | Pemasukan (donasi/infaq/lainnya) & pengeluaran (operasional/sarana/gaji/lainnya) |
| **Laporan** | Laporan keuangan (arus kas) & rekap pembayaran, **export PDF & Excel** |
| **Pengumuman** | CRUD pengumuman dengan scope tampilan (landing/dashboard/semua) |
| **Pengaturan** | Profil institusi, logo & favicon (upload) |
| **Backup** | Backup database via `mysqldump`, unduh & hapus |

## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Database:** MySQL / MariaDB (mendukung SQLite untuk pengujian)
- **Paket:** `spatie/laravel-permission`, `barryvdh/laravel-dompdf`, `maatwebsite/excel`
- **Frontend:** AdminLTE 3, Bootstrap 5, jQuery, DataTables, SweetAlert2, Select2, ApexCharts, FontAwesome 6

## Peran & Izin

| Peran | Hak akses utama |
| --- | --- |
| **Admin** | Semua modul, termasuk pengaturan, pengumuman, backup |
| **Bendahara** | Tagihan, pembayaran, kas, laporan |
| **Santri** | Dashboard informasi tagihan/pembayaran milik sendiri |
| **Wali** | Dashboard informasi tagihan/pembayaran anak asuh |

## Akun Demo

| Peran | Email | Password |
| --- | --- | --- |
| Admin | `admin@ppds.test` | `password` |
| Bendahara | `bendahara@ppds.test` | `password` |
| Santri | `santri1@ppds.test` | `password` |
| Wali | `wali1@ppds.test` | `password` |

## Instalasi Lokal

**Persyaratan:** PHP ^8.3, Composer 2, MySQL/MariaDB (atau SQLite), ekstensi `mbstring`, `xml`, `bcmath`, `intl`, `gd`, `zip`.

```bash
# 1. Clone & masuk direktori
git clone https://github.com/banonferdiana1996-ops/pembayaran-santri.git
cd pembayaran-santri

# 2. Install dependensi
composer install

# 3. Siapkan environment
cp .env.example .env
php artisan key:generate
# sesuaikan kredensial DB di file .env

# 4. Migrasi + seeder (demo data)
php artisan migrate:fresh --seed

# 5. Jalankan
php artisan serve
# buka http://localhost:8000
```

## Deploy (VPS)

1. `composer install --no-dev --optimize-autoloader`
2. Siapkan `.env` produksi (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`)
3. `php artisan migrate --force` dan `php artisan storage:link`
4. Arahkan Nginx ke folder `public/` + PHP-FPM, sertakan SSL (mis. Cloudflare origin cert)
5. Aktifkan CI/CD atau jalankan skrip deploy (`git pull` → composer → cache)

## Pengujian & Linting

```bash
vendor/bin/pint --test   # cek gaya kode (Laravel Pint)
php artisan test         # test suite (SQLite :memory:)
```

## Struktur Direktori Utama

```
app/
├── Exports/          # Export Excel laporan
├── Http/Controllers/ # Controller per modul
├── Models/           # Eloquent models
└── Support/          # Helper & kelas pendukung (Setting)
resources/views/      # Blade views (AdminLTE)
routes/web.php        # Definisi route
database/             # Migrasi, seeder, factory
.github/workflows/    # CI (GitHub Actions)
```

---

Dikembangkan untuk kebutuhan operasional Pondok Pesantren Darussalam Putri.
