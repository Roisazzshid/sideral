# SIDERAL - Sistem Manajemen Pemeliharaan Penerangan Gedung

**SIDERAL** adalah sistem manajemen terpadu yang dirancang untuk memantau status lampu, mengelola persediaan stok suku cadang, mencatat transaksi pemasangan dan penggantian lampu, menjadwalkan tiket pemeliharaan (Kanban Board), menganalisis telemetri energi harian, serta mengatur data spasial gedung secara realtime.

Proyek ini dibangun menggunakan **Laravel 12**, **Tailwind CSS v4**, **Alpine.js**, dan build-system **Vite**.

---

## 📋 Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan komputer Anda telah terpasang perangkat lunak berikut:
* **PHP >= 8.2** (Rekomendasi PHP 8.2 atau PHP 8.3)
* **Composer** (PHP dependency manager)
* **Node.js >= 18** (Beserta **npm**)
* **MySQL** atau **MariaDB** (Sebagai database server)

---

## 🚀 Panduan Instalasi (Step-by-Step)

Anda dapat memasang aplikasi ini baik dari file unduhan **ZIP** maupun melakukan clone langsung dari **GitHub**.

### Langkah 1: Ekstrak ZIP atau Clone Repository

* **Opsi A (Jika menggunakan file ZIP):**
  Ekstrak file `sideral.zip` ke direktori server lokal Anda (misalnya di folder `htdocs` XAMPP, `www` Laragon, atau folder kerja lainnya). Buka terminal di dalam folder hasil ekstrak tersebut.

* **Opsi B (Jika menggunakan Git):**
  Buka terminal/command prompt, lalu jalankan perintah berikut:
  ```bash
  git clone <url-repository-github-anda>
  cd sideral
  ```

### Langkah 2: Install Dependensi PHP
Jalankan perintah berikut untuk mengunduh library backend Laravel:
```bash
composer install
```

### Langkah 3: Install Dependensi Node.js (Frontend)
Jalankan perintah berikut untuk mengunduh library frontend (Tailwind CSS, ApexCharts, dll.):
```bash
npm install
```

### Langkah 4: Salin File Environment (.env)
Salin berkas konfigurasi bawaan menjadi file konfigurasi aktif:
* **Pengguna Linux / macOS / Git Bash:**
  ```bash
  cp .env.example .env
  ```
* **Pengguna Windows (Command Prompt - CMD):**
  ```cmd
  copy .env.example .env
  ```
* **Pengguna Windows (PowerShell):**
  ```powershell
  copy .env.example .env
  ```

### Langkah 5: Generate Application Key
Jalankan perintah berikut untuk menghasilkan kunci enkripsi unik aplikasi Laravel Anda:
```bash
php artisan key:generate
```

### Langkah 6: Konfigurasi Database
1. Buka file `.env` di text editor Anda (VS Code, Notepad, dll).
2. Cari baris koneksi database dan sesuaikan dengan kredensial server database lokal Anda. Contoh konfigurasi (menggunakan MySQL tanpa password):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_sideral
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Buka **phpMyAdmin** atau DBMS favorit Anda, lalu buat database baru bernama `db_sideral`.

### Langkah 7: Migrasi Database & Seeding Data Awal
Jalankan perintah berikut untuk membuat seluruh tabel dan mengisinya dengan data simulasi awal secara otomatis:
```bash
php artisan migrate --seed
```
*(Catatan: Sistem secara otomatis memiliki fitur self-healing database yang akan mengonfigurasi kolom hak akses user/operator secara otomatis saat pertama kali dibuka di browser).*

---

## 🏃 Menjalankan Aplikasi di Lokal

### Metode Utama (Direkomendasikan)
Jalankan perintah tunggal berikut untuk menyalakan Laravel server dan Vite development server secara bersamaan:
```bash
npm run dev
```
Setelah berjalan, Anda dapat mengakses aplikasi pada alamat browser berikut:
👉 **[http://localhost:8000](http://localhost:8000)**

### Metode Alternatif (Terpisah)
Jika Anda ingin menjalankan server secara terpisah pada dua jendela terminal berbeda:
* **Terminal 1 (Laravel Serve):**
  ```bash
  php artisan serve
  ```
* **Terminal 2 (Vite Assets Compilation):**
  ```bash
  npm run dev
  ```

---

## 🔑 Kredensial Login Awal

Gunakan salah satu akun bawaan hasil seed data berikut untuk masuk ke sistem:
* **Akun Admin (Full Akses):**
  * **Email:** `admin@sideral.com`
  * **Password:** `password`
* **Akun Teknisi (Khusus Tiket Pemeliharaan / Maintenance):**
  * **Email:** `teknisi@sideral.com`
  * **Password:** `password`

---

## 📁 Struktur Menu Utama SIDERAL

Aplikasi ini memiliki fitur lengkap yang dapat diakses melalui sidebar navigasi:
1. **Dashboard**: Panel ringkasan utama status lampu gedung, jadwal aktif, dan total daya terpakai.
2. **Floor Plan**: Denah ruangan interaktif 2D untuk mengatur penempatan lampu (drag & drop) dan menyalakan/mematikan lampu secara langsung.
3. **Lighting**: Daftar tabel komprehensif perangkat lampu, tipe fitting, dan status operasional.
4. **Inventory**: Manajemen persediaan stok bohlam lampu di gudang utama dengan peringatan otomatis untuk stok kritis.
5. **Maintenance**: Kanban board penjadwalan dan tiket perawatan untuk teknisi lapangan.
6. **Transaksi**: Catatan logistik mutasi keluar masuk suku cadang dan pemasangan.
7. **Report**: Tab visualisasi ApexCharts (Energi harian, penggantian sparepart, komposisi stok, biaya pengeluaran) dengan fitur ekspor CSV dan cetak PDF.
8. **Master Data**: Pengaturan spasial struktur fisik fasilitas (Gedung, Lantai, dan Area/Ruangan).
