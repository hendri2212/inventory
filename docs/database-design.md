# Database inventaris multi instansi

Satu database bersama. `institution_id` menandai pemilik data.

| Tabel          | Fungsi                               | Kolom utama                                                                                                                                                                                                                              |
| -------------- | ------------------------------------ | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `institutions` | Instansi/sekolah                     | `id`, `code`, `name`, `address`, `phone`, `email`, `is_active`                                                                                                                                                                           |
| `departments`  | Bidang/unit kerja                    | `id`, `institution_id`, `code`, `name`, `description`, `is_active`                                                                                                                                                                       |
| `categories`   | Kategori bersama dalam satu instansi | `id`, `institution_id`, `name`, `description`                                                                                                                                                                                            |
| `items`        | Stok barang milik bidang             | `id`, `institution_id`, `department_id`, `category_id`, `code`, `name`, `description`, `quantity`, `unit`, `purchase_year`, `purchase_price`, `photo_path`, `condition`, `condition_notes`, `disposed_at`, `disposal_notes`, `is_active` |
| `users`        | Akun pengguna                        | `id`, `name`, `email`, `password`, `institution_id`, `department_id`, `role`, kolom autentikasi; role `superadmin`, `admin`, `operator`, `peminjam`                                                                                      |

Semua tabel memiliki `created_at` dan `updated_at`.

## Relasi dan batasan

- Instansi memiliki banyak bidang, kategori, dan barang.
- Bidang memiliki banyak barang. Satu baris barang dimiliki satu bidang.
- Kategori dapat dipakai banyak bidang dalam instansi yang sama.
- Kode instansi unik global. Kode bidang dan kode barang unik per instansi. Nama kategori unik per instansi.
- Foreign key gabungan memastikan bidang dan kategori barang berada di instansi yang sama.
- Penghapusan instansi, bidang, atau kategori yang masih digunakan ditolak (`RESTRICT`). Instansi, bidang, dan barang dapat dinonaktifkan lewat `is_active`.
- `quantity` berupa jumlah bulat, default `0`. `unit` default `unit`.
- `purchase_year`: tahun pembelian, bilangan tahun empat digit yang perlu divalidasi aplikasi.
- `purchase_price`: harga beli per unit dalam rupiah, `DECIMAL(15,2)`; aplikasi perlu menolak nilai negatif.
- `photo_path`: lokasi satu file foto di storage; database menyimpan path, bukan isi file. Upload dan akses foto perlu mengikuti otorisasi instansi.
- Ketiga kolom pembelian/foto nullable agar data lama atau informasi yang belum tersedia tetap dapat disimpan. `NULL` berarti belum diketahui, berbeda dari harga `0`.
- Satu baris barang memiliki satu tahun dan harga pembelian. Pembelian dengan tahun/harga berbeda perlu baris berkode berbeda; bila kelak perlu satu katalog barang dengan banyak pembelian, pisahkan detail pengadaan ke tabel tersendiri.

Contoh: Sekolah A → Tata Usaha → Komputer, kategori Elektronik. Sekolah B boleh memakai kode barang yang sama, dengan data bidang dan kategorinya sendiri.

## Kondisi dan pemusnahan

| Kolom             | Arti                                                                       |
| ----------------- | -------------------------------------------------------------------------- |
| `condition`       | `good` = baik, `minor_damage` = rusak ringan, `major_damage` = rusak parah |
| `condition_notes` | Catatan pemeriksaan atau kerusakan                                         |
| `disposed_at`     | Waktu pemusnahan; `NULL` berarti belum tercatat dimusnahkan                |
| `disposal_notes`  | Alasan atau referensi berita acara pemusnahan                              |

Semua kolom ini nullable. Kondisi `NULL` berarti belum diperiksa/dicatat; data lama tidak otomatis dianggap baik. Pemusnahan terpisah dari kondisi fisik agar kondisi terakhir tetap tersimpan. `is_active` tetap penanda administratif.

Satu baris `items` mewakili jumlah barang dengan kondisi dan status pemusnahan yang sama. Contoh 10 kursi: 7 baik, 2 rusak ringan, 1 dimusnahkan perlu tiga baris berkode berbeda. Pemusnahan sebagian jumlah harus memisahkan baris terlebih dahulu; `disposed_at` berlaku untuk seluruh jumlah pada baris tersebut.

Saat fitur stok/peminjaman dibuat, barang dengan `disposed_at` terisi harus dikecualikan dari stok tersedia dan tidak boleh dipinjam. Migration ini hanya menambah penyimpanan informasi; alur pemusnahan, validasi, dan riwayat perubahan belum dibuat. Kondisi tambahan memerlukan perubahan daftar enum melalui migration.

## Batas tahap ini

`items` mencatat stok per jenis barang dalam satu bidang. Nomor seri, kondisi per unit, ruangan, pengadaan, peminjaman, dan riwayat mutasi belum dibuat. Untuk aset individual, tambahkan `item_units` sebelum membangun fitur tersebut.

## Akun dan akses

| Role         | Penempatan                    | Akses                                                                         |
| ------------ | ----------------------------- | ----------------------------------------------------------------------------- |
| `superadmin` | Instansi dan bidang `NULL`    | Seluruh instansi                                                              |
| `admin`      | Instansi wajib; bidang `NULL` | Kelola bidang, kategori, barang, dan akun operator/peminjam dalam instansinya |
| `operator`   | Instansi dan bidang wajib     | Kelola barang bidang sendiri; baca kategori instansi                          |
| `peminjam`   | Instansi dan bidang wajib     | Baca barang bidang sendiri dan kategori instansi                              |

Satu akun berada dalam maksimal satu instansi dan satu bidang. Satu bidang dapat memiliki banyak akun. Foreign key gabungan menolak penempatan akun pada bidang instansi lain; foreign key bidang tunggal juga memastikan bidang benar-benar ada ketika instansi kosong. Validasi model menegakkan penempatan sesuai role. Kolom role/penempatan tidak masuk mass assignment.

Model `Institution`, `Department`, `Category`, dan `Item` memiliki global scope berdasarkan akun login. Tanpa akun/penempatan valid, query tidak mengembalikan data. Operator/peminjam hanya melihat bidang sendiri; kategori dibagi dalam instansi. Event model menolak penyimpanan/penghapusan inventaris di luar akses akun. Akun instansi/bidang nonaktif ditolak. Dashboard memakai `EnsureTenantAccess`; logout tetap tersedia.

Policies terdaftar untuk inventaris dan akun. Endpoint CRUD belum dibuat. Saat menambah endpoint, gunakan middleware `auth` dan `EnsureTenantAccess`, route binding model yang berscope, validasi request, serta `Gate::authorize(...)` sebelum tindakan. Query daftar akun wajib `User::query()->visibleTo($request->user())` disertai policy `viewAny`; model `User` tidak memakai global scope agar autentikasi tetap dapat menemukan akun. Relasi `institution->users` dan `department->users` sudah membatasi akun yang terlihat.

Untuk membuat barang, tetapkan relasi instansi/bidang/kategori secara eksplisit setelah otorisasi, bukan lewat mass assignment input request. Jangan memakai `DB::table`, `withoutGlobalScopes`, atau bulk update/delete untuk operasi pengguna: jalur tersebut dapat melewati event/policies. Job/CLI perlu konteks akun valid untuk menggunakan model inventaris. Query mentah hanya untuk pekerjaan internal terpercaya.

Role dan relasi akun langsung didefinisikan dalam migration pembuatan `users`. Migration `users` lama diganti nama menjadi `2026_11_02_000005_create_users_table.php` agar dijalankan setelah instansi/bidang tersedia. Tidak ada migration alter akun atau pemetaan akun lama. Rancangan ini untuk database yang dibangun ulang melalui `migrate:fresh`. Seeder membuat akun admin baru sebagai `superadmin` dan tidak menimpa akun yang sudah ada.

## Menjalankan migration

Dari `/Users/hendriarifin/CODE/inventory`:

```sh
php artisan migrate
```

Lima migration domain, urutan: instansi → bidang → kategori → barang → akun. Kolom pembelian, foto, kondisi, dan pemusnahan langsung didefinisikan di migration pembuatan barang. Gunakan PHP yang memenuhi kebutuhan Composer proyek.
