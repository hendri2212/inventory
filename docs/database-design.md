# Inventaris per unit, multi instansi

Skema untuk database baru/dibangun ulang. Migration pembuatan `items` dan `users` diubah langsung; bukan jalur upgrade data lama. Jangan menjalankan `migrate:fresh` pada database berisi data yang perlu dipertahankan.

## Akun dan pencatatan

Role: `superadmin`, `admin`, `operator`. Tidak ada akun khusus peminjam. Operator mencatat peminjaman untuk orang lain melalui identitas peminjam pada transaksi; orang tersebut tidak perlu login.

| Role         | Penempatan                   | Akses                                                                   |
| ------------ | ---------------------------- | ----------------------------------------------------------------------- |
| `superadmin` | Instansi/bidang kosong       | Seluruh instansi                                                        |
| `admin`      | Satu instansi, bidang kosong | Katalog, kategori, bidang, ruang, unit, akun operator dalam instansinya |
| `operator`   | Satu instansi dan bidang     | Baca katalog/ruang instansi; kelola unit bidang sendiri                 |

Katalog bersama dikelola admin karena perubahan nama/kategori memengaruhi unit semua bidang. Unit fisik tetap milik satu bidang. Global scope menolak pembacaan tanpa akun/penempatan valid. Logout tetap tersedia untuk akun yang penempatannya dinonaktifkan.

## Tabel

| Tabel                 | Isi                                                                                                            |
| --------------------- | -------------------------------------------------------------------------------------------------------------- |
| `institutions`        | Instansi/sekolah                                                                                               |
| `departments`         | Bidang pengelola dalam instansi                                                                                |
| `categories`          | Kategori bersama dalam instansi                                                                                |
| `items`               | Katalog: kode katalog, nama, kategori, deskripsi, satuan, aktif                                                |
| `rooms`               | Ruang fisik dalam instansi: kode, nama, deskripsi, aktif                                                       |
| `item_units`          | Satu aset: kode inventaris, katalog, bidang, ruang, nomor seri, pembelian, foto, kondisi, pemusnahan, aktif    |
| `users`               | Akun login dan penempatan role                                                                                 |
| `loans`               | Nomor transaksi, bidang pemberi pinjaman, identitas peminjam, waktu pinjam/jatuh tempo, pencatat, catatan      |
| `loan_items`          | Unit yang dipinjam, kondisi keluar, pengembalian per unit, kondisi kembali, petugas penerima                   |
| `item_transfers`      | Nomor transaksi, jenis `room`/`department`, bidang/ruang asal dan tujuan, waktu, pencatat, pemberi persetujuan |
| `item_transfer_items` | Daftar unit dalam satu mutasi                                                                                  |

Setiap tabel memiliki timestamps. Foreign key gabungan `institution_id` + ID menjaga katalog, unit, ruang, bidang, dan detail transaksi tetap dalam instansi yang sama. Penghapusan referensi memakai `RESTRICT` untuk mempertahankan riwayat.

Kode katalog, kode inventaris, kode ruang, dan nomor masing-masing jenis transaksi unik per instansi. Nomor seri opsional; kode inventaris adalah identitas utama unit. Ruang opsional untuk unit yang belum ditempatkan. Ruang adalah lokasi fisik; bidang adalah pengelola, keduanya terpisah.

## Per unit

Contoh katalog `Laptop Lenovo ThinkPad` memiliki unit `LPT-001`, `LPT-002`, `LPT-003`. Setiap unit bisa memiliki bidang, ruang, nomor seri, harga/tahun pembelian, sumber dana, foto, kondisi dan tanggal pemusnahan berbeda.

Tidak ada kolom `quantity`. Jumlah berasal dari relasi `items.units`; jumlah yang dilihat operator hanya unit bidangnya. Jumlah tercatat mencakup unit nonaktif/dimusnahkan dan berbeda dari jumlah tersedia untuk dipinjam.

Harga memakai `DECIMAL(15,2)`; nilai tidak diketahui disimpan `NULL`, berbeda dari harga nol. Sumber dana: `BOSP`, `BOSDA`, `Bantuan`. Kondisi: `good`, `minor_damage`, `major_damage`; `NULL` berarti belum diperiksa. Pemusnahan terpisah dari kondisi terakhir.

Foto disimpan pada disk lokal privat. Endpoint foto unit memakai otorisasi dan scope bidang/instansi. Form edit unit tidak dapat mengganti katalog, instansi, bidang atau ruang; perpindahan harus melalui alur mutasi.

## Kontrak alur transaksi

Tabel dan relasi transaksi sudah tersedia. Endpoint, UI dan service peminjaman/pengembalian/mutasi belum dibuat pada tahap ini. Berikut aturan yang wajib diterapkan saat membuat alurnya:

- Semua operasi menggunakan transaksi database serta penguncian unit `lockForUpdate`; urutkan ID unit saat mengunci banyak unit.
- Peminjaman tidak mengganti bidang pemilik atau ruang penempatan. Pemegang sementara dicatat dalam `loans`.
- Satu pinjaman hanya berisi unit dari bidang pemberi pinjaman yang sama. Peminjam disimpan sebagai snapshot nama, identitas opsional, bidang asal opsional, dan kontak opsional; bukan foreign key ke akun.
- Saat menyerahkan unit, isi `active_item_unit_id = item_unit_id`. Indeks unik pada `active_item_unit_id` menjadi pengaman pinjaman bersamaan. Kolom ini tidak otomatis diisi database; service wajib mengelolanya dan memverifikasi nilainya.
- Saat menerima pengembalian unit, isi `returned_at`, `condition_in`, `notes_in`, `returned_by`, lalu kosongkan `active_item_unit_id` dan perbarui kondisi unit secara atomik. Detail lain boleh tetap terbuka untuk pengembalian bertahap.
- Status pinjaman diturunkan dari detail: masih ada `returned_at = NULL` berarti aktif; seluruh detail kembali berarti selesai. Keterlambatan dibandingkan dengan `due_at`.
- Unit nonaktif, dimusnahkan, sedang dipinjam, atau berkondisi tidak layak tidak boleh dipinjam/dimutasi. Validasi tanggal, petugas dan status katalog/bidang/ruang juga diperlukan.
- Mutasi ruang mempertahankan bidang. Mutasi bidang mengganti bidang dan dapat mengganti ruang. Asal setiap unit harus cocok dengan header transaksi; pecah transaksi jika asal berbeda.
- Mutasi memperbarui posisi unit dan menulis riwayat dalam transaksi database yang sama. Identitas asal/tujuan pada header harus dipertahankan setelah mutasi.
- Mutasi antarbidang memerlukan otorisasi instansi serta verifikasi tujuan. Scope baca operator mencakup riwayat mutasi keluar/masuk bidangnya; penulisan model mutasi generik oleh operator sengaja ditolak sampai service khusus tersedia.
- Petugas pencatat/penerima/pemberi persetujuan berasal dari akun login yang berwenang; jangan mempercayai ID petugas dari input klien. FK akun mempertahankan referensi, tetapi tidak memverifikasi kecocokan instansi petugas.
- Jangan mengubah atau menghapus detail transaksi selesai untuk mengoreksi posisi saat ini. Buat transaksi koreksi yang memiliki riwayat.

CRUD unit menolak pengeditan unit yang sedang dipinjam dan penghapusan unit dengan riwayat transaksi. Aturan transaksi di atas belum seluruhnya dijamin oleh skema atau event model; jangan memakai penulisan model generik sebagai pengganti service transaksi.

## Migration

Dari `/Users/hendriarifin/CODE/inventory`, untuk database baru:

```sh
php artisan migrate
```

Urutan domain: instansi → bidang → kategori → katalog → akun → ruang → unit aset → pinjaman → detail pinjaman → mutasi → detail mutasi. Tidak ada migrasi otomatis dari stok jumlah lama menjadi unit individual. Database yang sudah pernah memakai migration lama memerlukan rencana pemetaan data sebelum memakai skema ini.

## Spesifikasi unit

`items.description` menyimpan keterangan umum katalog. `item_units.description` menyimpan spesifikasi/keterangan aset fisik individual (opsional, maksimum 5000 karakter pada form). Contoh katalog Laptop memiliki unit Core i5/RAM 8 GB dan unit Ryzen 7/RAM 16 GB. Deskripsi unit ikut disalin saat duplikat serta tampil pada detail dan laporan/cetak.

Migration `2026_11_03_000007_add_description_to_item_units_table.php` menambah kolom nullable tanpa memetakan atau menimpa deskripsi katalog. Data unit lama tetap NULL hingga diisi.
