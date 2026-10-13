# Hasil pemeriksaan

13 Oktober 2026. Runtime: PHP 8.3.33, MySQL 8.4.3, browser bawaan Codex. Database dan session uji berada di direktori terpisah, bukan database Laragon pengguna.

## Fungsionalitas

- PASS: seluruh file PHP lolos pemeriksaan sintaks.
- PASS: pemeriksaan mandiri validasi kosong, batas angka/panjang, input array, sanitasi, hash akun awal dan kesamaan kunci terjemahan.
- PASS: pengujian HTTP login salah/benar, redirect tanpa login, create/read/update/delete, kode duplikat, ID tidak ditemukan, input negatif/kosong, token CSRF salah, escaping script, bahasa session dan logout.
- PASS: alur browser tambah, simpan, ubah, kembali, batal, konfirmasi hapus, hapus, pilihan bahasa dan logout.
- PASS: daftar diperiksa pada 375, 760 dan 1366 piksel; document scrollWidth tidak melebihi viewport. Form Stockify juga diperiksa pada 375 piksel.
- PASS: navigasi keyboard dari pilihan bahasa menuju tombol Terapkan/Apply, dengan outline fokus solid. Kontrol memakai input, select, button dan link HTML standar.
- PASS: log server pengujian akhir tidak berisi Warning, Fatal atau Uncaught.
- PASS: laporan PDF dirender dan diperiksa; tidak ada teks bertumpuk atau terpotong.

## Delivery Gate antislop

### Hard Gate

- R-02 PASS: teks aplikasi tidak memakai em dash.
- R-03 PASS: daftar diuji pada tiga lebar layar; form memakai satu kolom pada layar kecil.
- R-17 PASS: jumlah catatan berasal dari jumlah hasil query; tidak ada statistik buatan.
- R-18 PASS: tidak ada testimoni atau identitas fiktif.
- R-23 PASS: nama produk berupa teks sesuai permintaan; tidak ada logo, foto atau ilustrasi buatan.
- R-24 PASS: semua tautan menuju daftar, form edit, tambah atau konfirmasi hapus yang tersedia.
- R-25 PASS: sembilan pasangan teks/latar diuji, rasio minimum 6,09:1; batas AA 4,5:1.
- R-26 PASS: tombol login, bahasa, CRUD dan logout telah menjalankan tindakan nyata.
- R-27 PASS: daftar kosong memberi petunjuk tambah; submit menampilkan pesan proses; validasi dan database memiliki pesan kesalahan.
- R-28 PASS: tidak ada FAQ tambahan.
- R-32 PASS: label input, kontrol HTML standar dan outline focus-visible tersedia; perpindahan fokus keyboard diperiksa.
- R-33 PASS: fitur ditulis langsung pada source; tidak ada skrip patch source yang dikirimkan.
- R-34 PASS: tema tetap sesuai permintaan; tidak ada tombol tema yang perlu diuji.
- R-35 PASS: aplikasi dijalankan dengan MySQL nyata; kontrol utama diperiksa melalui browser dan HTTP.
- R-36 PASS: tidak ada klaim keamanan, performa atau kepatuhan yang dibuat-buat.
- R-37 PASS: arah dark glassmorphism/terang mengikuti pengguna; alasan visual dan dials dicatat di DESIGN.md.
- R-38 PASS: database awal tidak berisi data inventaris/katalog palsu; tangkapan layar diberi keterangan data uji.

### Purpose Gate

- R-01 PASS: gradasi Stockify memperlihatkan satu panel kaca sesuai permintaan pengguna; BookNest memakai latar datar.
- R-04 PASS: tidak ada ikon dekoratif atau pustaka ikon tambahan.
- R-06 PASS: Segoe UI untuk form Windows; serif pada BookNest untuk identitas katalog, tertulis di DESIGN.md.
- R-07 PASS: tidak ada grid atau pola latar dekoratif.
- R-08 PASS: tidak ada panah dekoratif pada tombol.
- R-09 PASS: tidak ada capsule badge.
- R-10 PASS: efek kaca hanya pada satu panel konten yang tampil per halaman Stockify.
- R-12 PASS: bayangan hanya mengangkat satu panel kaca, bukan setiap komponen.
- R-13 PASS: tidak ada efek glow.
- R-14 PASS: data ditampilkan di tabel, tidak menggunakan kumpulan kartu fitur.
- R-19 PASS: MOTION 1, hanya hover/fokus dan pesan submit; tidak ada loop animasi.
- R-22 PASS: tidak ada ilustrasi tambahan.

### Liveliness

- PASS dials: ENERGY 2 / RHYTHM 2 / MOTION 1 dicatat dalam DESIGN.md.
- PASS konsistensi: satu panel kerja, tanpa animasi berulang atau dekorasi berlebihan.
- PASS fokus: judul daftar dan tombol tambah; input pada halaman form; login pada halaman autentikasi.
- PASS whitespace: pemisahan 36 piksel judul/tabel dan 24 piksel kelompok input.
- PASS aksen: amber/hijau untuk tindakan utama.
- PASS motif: identitas Stockify gelap dengan panel kaca; BookNest terang dengan judul serif.
- PASS Design Read: arah, alasan dan batas tampilan dinyatakan sebelum pembuatan UI.

### Craftsmanship dan Quality Locks

- C-1 PASS: alasan visual tertulis dalam DESIGN.md.
- C-2 PASS: tidak ada kontrol mati, semuanya mengarah ke form atau halaman nyata.
- C-3 PASS: hanya login, daftar, form dan konfirmasi hapus sesuai tugas.
- C-4 PASS: layout responsif dan kontrol keyboard standar; status kosong, kesalahan dan submit tersedia.
- C-5 PASS: tidak ada klaim atau angka fiktif.
- R-05 PASS: struktur mengikuti pekerjaan CRUD, tanpa landing page, dashboard statistik atau hero generik.
- R-11 PASS: panel radius 16 piksel; kontrol radius 6 piksel, tanpa pill berulang.
- R-15 PASS: tombol menyebut tindakan Masuk, Tambah data, Simpan, Ubah, Hapus dan Keluar.
- R-16 PASS: copy berisi instruksi data, tidak memakai jargon pemasaran.
- R-20 PASS: identitas inventaris/katalog terlihat pada kolom, judul dan tema masing-masing.
- R-21 PASS: tema gelap/terang berasal dari instruksi pengguna.
- R-29 PASS: palet netral dengan satu aksen tindakan; warna kesalahan hanya untuk status kesalahan.
- R-30 PASS: tidak ada upaya meniru produk lain.
- R-31 PASS: keputusan warna, tipografi, panel dan jarak punya alasan tertulis.

## Batas pemeriksaan

MariaDB, hosting publik, pengujian beban dan seluruh kombinasi browser belum diuji. Session serta port database uji berbeda dari default paket; paket akhir tetap memakai default Laragon 3306. Data uji tidak disertakan dalam SQL distribusi. Pengguna perlu mengimpor SQL dan menyesuaikan koneksi sesuai README.
