# Sistem Manajemen Inventaris dan Penjualan

Sistem ini adalah aplikasi web berbasis Laravel untuk mengelola inventaris dan penjualan pada sebuah distributor. Aplikasi ini menyediakan fitur manajemen barang, pengguna, barang masuk, barang keluar, pengembalian barang, serta dashboard untuk memantau penjualan dan stok.

## Fitur Utama

- Autentikasi pengguna dengan peran Admin dan Karyawan.
- Manajemen data barang dan pengguna (khusus Admin).
- Manajemen barang masuk dan barang keluar (khusus Karyawan dan Admin).
- Pengelolaan pengembalian barang.
- Dashboard interaktif yang menampilkan:
  - Penjualan bersih dan total transaksi.
  - Jumlah barang terjual dan stok total.
  - Grafik penjualan dari waktu ke waktu.
  - Daftar transaksi dan barang keluar terbaru.
  - Daftar barang dengan stok rendah.
  - Total keuntungan.

## Instalasi

1. Pastikan Anda memiliki PHP, Composer, dan server web (misal: Apache, Nginx) yang sudah terpasang.
2. Clone atau unduh repository ini.
3. Jalankan perintah berikut di direktori proyek untuk menginstal dependensi:
   ```
   composer install
   ```
4. Salin file `.env.example` menjadi `.env` dan sesuaikan konfigurasi database serta pengaturan lainnya.
5. Jalankan migrasi dan seeder database:
   ```
   php artisan migrate --seed
   ```
6. Jalankan server development Laravel:
   ```
   php artisan serve
   ```
7. Akses aplikasi melalui browser di alamat `http://localhost:8000`.

## Struktur Folder Penting

- `app/Http/Controllers` : Berisi controller untuk mengatur logika aplikasi.
- `routes/web.php` : Mendefinisikan rute aplikasi web.
- `resources/views` : Template tampilan menggunakan Blade.
- `database/migrations` : Skrip migrasi database.
- `database/seeders` : Skrip pengisian data awal.
- `public/` : Folder publik untuk file yang dapat diakses langsung.

## Lisensi

Proyek ini menggunakan Laravel Framework yang dilisensikan di bawah MIT License.

---
