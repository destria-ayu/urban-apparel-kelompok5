#  Urban Apparel

## Anggota Kelompok
1. Destria Ayu Lestari (2410631170016)
2. M. Akmal Fauzan N.R. (2410631170079)
3. Rois Alif Pradipa (2410631170107)


##  Deskripsi dan Tujuan Website
**Urban Apparel** adalah platform *e-commerce* berbasis web yang menyediakan katalog pakaian *streetwear* dan kasual. Tujuan dari website ini adalah memberikan kemudahan bagi pelanggan untuk mencari produk pakaian, mengelola keranjang belanja, hingga melakukan proses *checkout* dan pelacakan riwayat pesanan secara praktis dan terintegrasi.


##  Fitur-fitur Utama Website
Berdasarkan implementasi sistem, berikut adalah fitur-fitur utama yang tersedia:
* **Register & Login Pengguna:** Pengguna dapat membuat akun dan masuk ke dalam sistem dengan aman.
* **Pencarian Produk:** Memudahkan pengguna menemukan produk berdasarkan nama atau kata kunci tertentu.
* **Melihat Detail Produk:** Menampilkan informasi lengkap produk seperti nama, harga, ukuran, stok, deskripsi, dan foto produk.
* **Pemesanan Produk:** Memproses produk yang dipilih pengguna menjadi pesanan.
* **Rating Produk:** Pengguna dapat memberikan rating dan ulasan serta menghapus ulasan miliknya sendiri.


##  Struktur Project
Berikut adalah penjelasan folder dan file penting di dalam *repository* ini:

* **File Database:**
  * `pbwfashion.sql`: File *dump* database MySQL yang berisi struktur tabel (`users`, `produk`, `cart`, `orders`, `order_items`, `ulasan`) dan data awal produk/akun.
  * `koneksi.php`: File konfigurasi untuk menghubungkan PHP dengan database.

* **File Autentikasi:**
  * `register.php`: Halaman pendaftaran akun pengguna.
  * `login.php`: Halaman *login* dengan verifikasi *password*.
  * `logout.php`: File untuk menghancurkan sesi (*session*) pengguna.

* **File Halaman Utama & Transaksi:**
  * `index.php`: Halaman beranda yang memuat *banner carousel*, filter pencarian, dan katalog produk.
  * `detail.php`: Menampilkan detail spesifik dari sebuah produk beserta kolom komentar/ulasan.
  * `cart.php`: Halaman manajemen keranjang belanja.
  * `update_quantity.php`: Menangani logika penambahan (`+`) atau pengurangan (`-`) jumlah barang di keranjang beserta validasi batas stok.
  * `checkout.php`: Menangani logika penyimpanan data pesanan ke dalam *database*.
  * `history.php`: Halaman untuk melihat riwayat transaksi pengguna.
  * `detail_order.php`: Menampilkan rincian dari satu ID pesanan spesifik.

* **Folder Aset:**
  * `uploads/`: Direktori yang wajib ada untuk menyimpan file foto-foto produk pakaian (`.jpg`, `.jpeg`).


##  Cara Menjalankan Aplikasi

Berikut adalah langkah-langkah panduan untuk menjalankan aplikasi **Urban Apparel** di lingkungan lokal (*localhost*) menggunakan XAMPP:

### 1. Persiapan Web Server
* Pastikan aplikasi web server seperti **XAMPP** sudah terinstal di komputer.
* Buka XAMPP Control Panel, lalu jalankan modul **Apache** dan **MySQL** dengan mengklik tombol **Start** pada kedua modul tersebut.

### 2. Penempatan File Project
* Buat sebuah folder project di dalam direktori `htdocs` (umumnya berada di `C:\xampp\htdocs\`), misalnya dengan nama folder `urban-apparel`.
* Pindahkan seluruh file *source code* PHP (`index.php`, `koneksi.php`, `login.php`, dll) ke dalam folder `urban-apparel` tersebut.
* Pastikan file-file gambar produk diletakkan di dalam folder bernama `uploads/` yang berada di dalam folder project tersebut.

### 3. Import Database
* Buka browser dan akses halaman **phpMyAdmin** melalui URL: `http://localhost/phpmyadmin/`.
* Buat database baru dengan mengklik menu **New**, lalu masukkan nama database: `pbwfashion` (pastikan nama sesuai dengan konfigurasi yang ada di file `koneksi.php`).
* Klik pada nama database `pbwfashion` yang baru dibuat tersebut, lalu pilih tab **Import** di bagian atas halaman.
* Klik tombol **Choose File** / **Browse**, lalu pilih file database `pbwfashion.sql` dari folder project.
* Gulir ke bawah halaman dan klik tombol **Import** atau **Go** untuk memproses pembuatan struktur tabel beserta datanya.

### 4. Akses Aplikasi
* Buka tab baru di browser dan jalankan aplikasi dengan mengakses URL berikut:
  ```text
  http://localhost/urban-apparel/


##  Link Video Presentasi
[**Klik di sini untuk menonton video presentasi kelompok kami**](https://drive.google.com/file/d/1g36qvxwk75SWs20jjDguPdCo-UzwBnAr/view?usp=drive_link)
