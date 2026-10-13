# Tugas 2
## Membuat Sistem Informasi Manajemen Data Sederhana

**Dosen Pengampu:**  
Donny Reza, M.Kom  

**Disusun Oleh:**  
Rashad Shaquille Taofik  
NIM: 10124050  
Kelas: IF-2  

---

# Stockify

Inventaris barang sederhana untuk Tugas 2. PHP Native 8.1+, MySQL 8.0.16+ atau MariaDB 10.2.1+, tanpa framework dan tanpa Composer. Ekstensi PHP: pdo_mysql dan mbstring.

## Menjalankan di Laragon

1. Ekstrak folder `stockify` ke `C:\laragon\www\stockify`.
2. Klik **Start All** di Laragon.
3. Buka HeidiSQL atau phpMyAdmin, lalu impor `database.sql`. Skema membuat database `stockify`; tidak menghapus tabel lama.
4. Sesuaikan `database` di `config.php`: host, port, nama database, nama pengguna dan kata sandi. Default Laragon: `127.0.0.1`, port `3306`, pengguna `root`, kata sandi kosong.
5. Buka `http://localhost/stockify/login.php`.
6. Masuk dengan nama pengguna **admin** dan kata sandi **Admin123!**.

Akun ini hanya untuk tugas lokal. Sebelum dipublikasikan, ganti hash kata sandinya di tabel `users`, gunakan pengguna database terbatas dan HTTPS. Membuat hash: `php -r "echo password_hash('KataSandiBaru', PASSWORD_DEFAULT);"`. Jangan mengunggah kredensial database asli.

## Batas fitur

- CRUD barang: kode unik, nama, kategori, jumlah stok, satuan.
- Login dan logout dengan session. Semua akun memiliki akses yang sama, tanpa role atau pengelolaan akun melalui UI.
- Bahasa Indonesia/Inggris melalui form POST; pilihan disimpan di session dan berlaku pada login, daftar, form, konfirmasi hapus, serta pesan validasi.
- Tidak ada mutasi stok, peminjaman, laporan statistik, unggah berkas atau fitur tambahan.

## Alur dan ketentuan tugas

Login → daftar barang → tambah/ubah atau konfirmasi hapus → daftar barang. Bahasa dapat diganti pada setiap halaman. Membuka CRUD tanpa login mengarahkan ke halaman login. Hapus baru terjadi setelah form konfirmasi POST dikirim.

`login.php` memverifikasi hash kata sandi lalu memperbarui ID session. `index.php` mengolah CRUD. `app.php` menyediakan koneksi PDO, session, CSRF, validasi if/else dan pengolahan tindakan switch. Semua penulisan database memakai POST dan prepared statements. Teks disimpan sebagai teks asli yang telah dipangkas; `htmlspecialchars()` diterapkan saat ditampilkan agar tidak terjadi encoding ganda. `filter_var()` memvalidasi bilangan bulat. Kode unik juga dijaga database.

Jumlah stok: bilangan bulat 0 sampai 1.000.000. Semua kolom wajib diisi. Panjang maksimum: kode 30, nama 120, kategori 80, satuan 30 karakter. Form mempertahankan input jika validasi gagal. CSRF ditolak sebelum perubahan database. Detail kegagalan database masuk log server, bukan antarmuka.

## Struktur

`config.php`: identitas dan koneksi; `app.php`: fungsi dasar; `translations.php`: terjemahan; `login.php`: login; `index.php`: CRUD; `assets/`: CSS dan indikator proses; `database.sql`: skema dan akun awal; `tests/validation.php`: pemeriksaan mandiri.

ERD: `users(id PK, username UNIQUE, password_hash)` dan `items(id PK, code UNIQUE, name, category, quantity, unit)`. Dua tabel independen; pencatatan pemilik bukan bagian tugas.

## Pemeriksaan

Jalankan `php tests/validation.php`. Uji manual: buka daftar tanpa login, login salah/benar, tambah data, kode duplikat, stok negatif, ubah, batal hapus, hapus, ganti bahasa dan berpindah halaman, logout lalu buka daftar lagi. Coba juga lebar layar ponsel dan navigasi Tab/Enter. Pemeriksaan ini tidak mengubah database melalui CLI.

## Pengumpulan

Sertakan seluruh source, `database.sql`, dan laporan PDF berisi tangkapan layar serta alur program. Unggah ke repositori GitHub publik lalu kirim tautannya melalui form tugas paling lambat **31 Oktober 2026**. Publikasi GitHub dilakukan oleh pemilik proyek.
