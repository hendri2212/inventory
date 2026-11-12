# Skawan Inventory

Aplikasi inventaris per instansi untuk mengelola katalog barang, unit inventaris,
ruangan, dan laporan. Dibangun dengan Laravel, Inertia, Vue, dan TypeScript.

## Prasyarat

- PHP 8.3 dan Composer 2. Ekstensi GD dengan dukungan WebP dan EXIF diperlukan untuk kompresi serta orientasi foto.
- Foto unit: maksimal 20 MB / 24 megapiksel per foto; disimpan sebagai WebP kualitas 82 dengan sisi terpanjang maksimal 1.920 piksel. Sesuaikan `upload_max_filesize`, `post_max_size`, dan batas request web server untuk total ukuran unggahan (maksimal 10 foto).
- Node.js 22.12 atau lebih baru dalam seri 22, beserta npm.
- SQLite dengan ekstensi PHP `pdo_sqlite`, atau MySQL dengan `pdo_mysql`.

## Instalasi lokal

Jalankan dari direktori proyek pada instalasi baru.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

SQLite adalah bawaan. Buat file database sebelum migrasi.

```bash
touch database/database.sqlite
```

Jika memakai MySQL, buat database terlebih dahulu dan sesuaikan `.env` dengan
kredensial lokal.

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory
DB_USERNAME=inventory
DB_PASSWORD=
```

Jalankan migrasi, buat akun awal, dan pasang dependensi frontend.

```bash
php artisan migrate
php artisan db:seed
npm ci
```

Seeder membuat akun `superadmin` lokal dengan email `admin@inventory.com` dan
kata sandi `12345`. Kredensial ini khusus pengembangan lokal.

## Menjalankan proyek

```bash
composer dev
```

Buka [http://localhost:8000/login](http://localhost:8000/login). Jika ingin
menjalankan Laravel dan Vite di terminal terpisah, gunakan `php artisan serve`
dan `npm run dev`.

## Migrasi dan reset database

Untuk database yang sudah berisi data, gunakan migrasi biasa. Periksa status
migrasi dengan `migrate:status`.

```bash
php artisan migrate:status
php artisan migrate
```

**`migrate:fresh` menghapus semua tabel dan data pada database yang dituju.**
Gunakan hanya untuk database lokal yang boleh direset. Opsi `--seed` membuat
ulang akun awal setelah migrasi.

```bash
php artisan migrate:fresh --seed
```

Jika konfigurasi database di `.env` berubah dan konfigurasi lama masih terpakai,
jalankan `php artisan config:clear`.

## Build frontend

```bash
npm ci
npm run build
```

Hasil build ada di `public/build`. Untuk mencobanya secara lokal, hentikan Vite
dan jalankan `php artisan serve`.

## Pengujian dan pemeriksaan kode

| Perintah              | Fungsi                                                                |
| --------------------- | --------------------------------------------------------------------- |
| `php artisan test`    | Menjalankan tes dengan SQLite in-memory.                              |
| `composer test`       | Memeriksa format PHP, menjalankan PHPStan, dan menjalankan tes.       |
| `npm run check`       | Memeriksa format dan lint frontend.                                   |
| `npm run types:check` | Memeriksa tipe Vue dan TypeScript.                                    |
| `composer ci:check`   | Menjalankan rangkaian pemeriksaan frontend dan backend seperti di CI. |

Lihat [desain database](docs/database-design.md) untuk struktur data inventaris.
