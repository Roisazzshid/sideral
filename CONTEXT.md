# LumiTrack FM - Spesifikasi Sistem & Konteks

## 1. Tinjauan Umum
**LumiTrack FM (Lighting Inventory & Facility Management System)** adalah aplikasi web enterprise yang dirancang untuk memantau dan mengelola titik lampu, melacak inventaris gudang, menjadwalkan tugas pemeliharaan (maintenance), mencatat transaksi penggantian/pemasangan lampu, serta memvisualisasikan konsumsi energi dan estimasi biaya di seluruh area gedung. Aplikasi ini menggunakan denah lantai interaktif untuk memetakan dan mendiagnosis status setiap titik lampu secara visual.

---

## 2. Sistem Desain & Estetika
- **Tema**: Layout utama menggunakan mode terang (light-mode) dengan latar belakang card putih bersih, batas (border) abu-abu muda, serta navigasi sidebar berwarna hijau tua (teal).
- **Palet Warna Utama**:
  - Hijau Teal (`#006D5B` atau `#0b6d5b`) yang melambangkan efisiensi energi dan pengelolaan fasilitas yang bersih.
- **Warna Badge & Status**:
  - **Aktif / Aman / Selesai**: Badge atau dot berwarna Hijau.
  - **Dim / Menipis / Proses**: Badge atau dot berwarna Kuning/Oranye.
  - **Mati / Habis**:
    - **Mati**: Dot berwarna Abu-abu Gelap pada denah lantai.
    - **Habis**: Status/Teks berwarna Merah pada inventaris.
  - **Baru (Tiket Baru)**: Badge berwarna Biru pada pemeliharaan.
  - **Warning**: Badge berwarna Oranye/Amber.
  - **Error / Prioritas Tinggi (High)**: Badge atau dot berwarna Merah.
- **Tipografi**: Tipografi sans-serif modern (seperti Inter, Outfit, atau Roboto) dengan hierarki yang jelas dan penggunaan ikon yang konsisten.

---

## 3. Struktur Navigasi Sidebar
Sidebar navigasi menggunakan tema warna Hijau Teal dengan ikon yang bersih:
1. **Dashboard**: Telemetri tingkat tinggi, kartu KPI utama, grafik pemakaian energi, dan daftar transaksi penggantian terbaru.
2. **Floor Plan**: Utilitas denah interaktif untuk memantau secara visual dan mengedit titik lampu pada blueprint lantai.
3. **Lighting**: Katalog lengkap yang menampilkan tipe lampu, jenis, watt, stok, satuan, status, dan aksi.
4. **Inventory**: Metrik inventaris yang dibagi menjadi tab Ringkasan, Barang Masuk, Barang Keluar, dan Stok Opname.
5. **Maintenance**: Sistem tiket kerja untuk melacak perbaikan, pembersihan, dan jadwal pemeliharaan.
6. **Transaksi**: Riwayat pencatatan transaksi penggantian lampu dan pemakaian lampu.
7. **Report**: Halaman laporan khusus yang menampilkan grafik pemakaian energi (MWh) dan ringkasan biaya.
8. **Master Data**: Konfigurasi administratif fasilitas (Gedung, Lantai, Area / Ruangan, dan User).
9. **User**: Manajemen profil pengguna dan akun operator.
10. **Settings**: Pengaturan konfigurasi sistem.

---

## 4. Spesifikasi Halaman UI

### 1. Halaman Login
- **Visual**: Desain split-screen.
  - Panel kiri: Foto gedung perkantoran modern yang diterangi lampu di malam hari.
  - Panel kanan: Layout kartu login minimalis dengan latar belakang putih.
- **Komponen**:
  - Logo Sistem: "LumiTrack FM" dengan deskripsi "Lighting Inventory & Facility Management System".
  - Input: Email (placeholder `admin@company.com`), Password (disamarkan/masked).
  - Aksi: Link "Lupa password?" dan tombol "Login" berwarna Hijau Teal solid.
  - Footer: "© 2025 LumiTrack FM. All rights reserved."

### 2. Dashboard
- **Widget KPI Utama (Card)**:
  - **Total Titik Lampu**: Total titik koordinat lampu yang terpetakan (misalnya: `1.248 Titik`).
  - **Lampu Terpasang**: Jumlah fisik lampu yang aktif terpasang (misalnya: `12.594 Buah`).
  - **Lampu Diganti Bulan Ini**: Total penggantian lampu yang dicatat pada bulan berjalan (misalnya: `286 Buah`).
  - **Estimasi Biaya Bulan Ini**: Total estimasi biaya penggantian lampu pada bulan berjalan (misalnya: `Rp 384.200`).
- **Grafik & Telemetri**:
  - **Konsumsi Energi (kWh)**: Line chart yang melacak tren konsumsi energi harian (misalnya: periode 1 Mei - 28 Mei, dengan tooltip seperti "14 Mei 2025: Total: 650 kWh").
  - **Status Lampu (Donut Chart)**: Breakdown persentase status lampu saat ini (Aktif: 92.3%, Dim: 5.1%, Mati: 1.8%, Warning: 0.6%, Error: 0.2%) dengan total angka `1.248` di tengah donut.
- **Bagian Tabel**:
  - **Area Penggunaan Tertinggi (Tabel)**: Menampilkan 3 area/ruangan dengan konsumsi energi tertinggi (misalnya: Open Workspace A: 1.240 kWh (28.5%), Meeting Room 1: 520 kWh (12.0%), Open Workspace B: 410 kWh (9.4%)).
  - **Transaksi Terbaru (Tabel)**: Log kronologis berisi 3 transaksi penggantian lampu terakhir, menampilkan Waktu, Area/Ruangan, dan Jumlah (misalnya: `20 Mei 2025 09:21 | Meeting Room 1 | 8 buah`).

### 3. Floor Plan (Denah Interaktif)
- **Kontrol Bar Atas**:
  - Dropdown selector Gedung (misalnya: Gedung A).
  - Dropdown selector Lantai (misalnya: Lantai 4).
  - Tombol "Filter Area".
  - Tombol "+ Edit Titik Lampu" (mengaktifkan mode editor tata letak lampu pada kanvas).
- **Kontrol Panel Kiri**:
  - **Status Lampu (Filter & Legend)**: Checkbox untuk menyaring visibilitas titik lampu berdasarkan status: Aktif (Hijau), Dim (Kuning/Oranye), Mati (Abu-abu), Warning (Oranye), Error (Merah).
  - **Pencarian**: Input teks dengan placeholder "Cari area / ruangan...".
  - **Layer**: Checkbox untuk menampilkan/menyembunyikan layer visual: Titik Lampu, Area / Ruangan, Denah Lantai.
  - Aksi: Tombol "Reset View".
- **Kanvas Interaktif Utama**:
  - Layout denah lantai yang menampilkan ruangan-ruangan (seperti workspace, toilet, meeting room, pantry, lobby).
  - Node/titik lingkaran interaktif yang ditempatkan pada posisi koordinat X/Y absolut, dengan warna sesuai status masing-masing lampu.
  - Kontrol navigasi kanvas: zoom-in, zoom-out, locate/home di pojok kanan atas.

### 4. Lighting (Data Lampu)
- **Aksi Atas**:
  - Input pencarian: "Cari jenis lampu...".
  - Tombol aksi: "+ Tambah Lampu".
- **Tabel Katalog**:
  - Kolom: `No`, `Nama Lampu`, `Jenis`, `Watt`, `Stok`, `Satuan`, `Status`, `Aksi` (Edit, Delete).
  - Contoh Data Lampu:
    - Philips LED Tube (Jenis: LED Tube, 18 W, Stok: 320, Satuan: Buah, Status: Aman)
    - Osram LED Downlight (Jenis: Downlight, 15 W, Stok: 88, Satuan: Buah, Status: Menipis)
    - Philips LED Spotlight (Jenis: Spotlight, 7 W, Stok: 25, Satuan: Buah, Status: Warning)
  - Badge Status: Indikator tingkat keamanan stok seperti `Aman` (teks & bg hijau), `Menipis` (teks & bg oranye), dan `Warning` (teks & bg amber/kuning).

### 5. Inventory
- **Tab Halaman**: `Ringkasan`, `Barang Masuk`, `Barang Keluar`, `Stok Opname`.
- **Aksi Atas**: Tombol `+ Barang Masuk`.
- **Card Ringkasan KPI**:
  - **Total Stok**: Total kumulatif stok di gudang (misalnya: `825 Buah`, outline hijau).
  - **Stok Aman**: Stok di atas batas minimum (misalnya: `620 Buah`, outline hijau).
  - **Stok Menipis**: Stok mendekati batas minimum (misalnya: `160 Buah`, outline oranye).
  - **Stok Habis**: Stok kosong (misalnya: `45 Buah`, outline merah).
- **Bagian Konten**:
  - Kiri: Tabel **Stok Menipis / Habis** menampilkan daftar item (misalnya: Osram LED Downlight 15W stok 8 status Menipis; Philips LED Spotlight 7W stok 2 status Habis).
  - Kanan: Donut chart **Kategori** menunjukkan persentase jenis lampu di gudang (LED Tube 42%, LED Bulb 28%, Downlight 18%, Panel 8%, Spotlight 4%).

### 6. Transaksi (Penggantian Lampu)
- **Tab Halaman**: `Penggantian Lampu`, `Pemakaian Lampu`.
- **Aksi Atas**: Tombol `+ Transaksi Baru`.
- **Bar Filter**:
  - Date Range Picker (Rentang Tanggal, misalnya: `01/05/2025 - 31/05/2025`).
  - Dropdown Gedung (Semua Gedung), Lantai (Semua Lantai), Area/Ruangan (Semua Area).
  - Input pencarian: "Cari transaksi...".
- **Tabel Log**:
  - Kolom: `No`, `Tanggal`, `Gedung`, `Lantai`, `Area / Ruangan`, `Jenis Lampu`, `Jumlah`, `User`.
  - Log mencatat transaksi penggantian lampu rusak oleh teknisi (misalnya: 20/05/2025 09:21, Gedung A, Lantai 4, Meeting Room 1, Philips LED Tube 18W, Jumlah 8, User Admin).

### 7. Report (Laporan Pemakaian)
- **Tab Halaman**: `Laporan Pemakaian`, `Laporan Penggantian`, `Laporan Stok`, `Laporan Biaya`.
- **Filter**: Dropdown Periode (misalnya: Mei 2025), Gedung (Semua Gedung).
- **Aksi Atas**: Tombol `Generate` dan `Export`.
- **Visualisasi Analisis**:
  - Kiri: **Grafik Pemakaian Energi (MWh)** - Area chart yang memplot profil konsumsi harian Gedung A, Gedung B, Gedung C, serta Total kumulatif.
  - Kanan: **Ringkasan (Card KPI)**:
    - **Total Konsumsi**: misalnya `2.620 kWh`.
    - **Rata-rata / Hari**: misalnya `93,5 kWh`.
    - **Tertinggi / Hari**: misalnya `456 kWh (14 Mei 2025)`.
    - **Estimasi Biaya**: misalnya `Rp 3.542.000`.

### 8. Maintenance
- **Tab Halaman**: `Daftar Maintenance`, `Jadwal Maintenance`.
- **Aksi Atas**: Tombol `+ Buat Maintenance`.
- **Bar Filter**: Selector Status, Selector Prioritas, dan input "Cari maintenance...".
- **Tabel Tiket**:
  - Kolom: `No`, `Tanggal`, `Area / Ruangan`, `Jenis`, `Deskripsi`, `Prioritas`, `Status`, `Aksi`.
  - Warna Prioritas: High (badge Merah), Medium (badge Oranye/Kuning), Low (badge Hijau/Abu-abu).
  - Badge Status: Selesai (badge Hijau dengan checkmark/dot), Proses (badge Kuning/Oranye), Baru (badge Biru).
  - Contoh Tiket: "Lampu Mati" (Prioritas: High, deskripsi: "Beban mati total..."), "Lampu Flicker" (Prioritas: Medium, deskripsi: "Lampu berkedip..."), "Pembersihan Lampu" (Prioritas: Low).

### 9. Master Data
- **Tab Halaman**: `Gedung`, `Lantai`, `Area / Ruangan`, `User`.
- **Aksi Atas**: Tombol `+ Tambah Gedung`.
- **Tabel Fasilitas**:
  - Kolom: `No`, `Nama Gedung`, `Lokasi`, `Jumlah Lantai`, `Jumlah Titik Lampu`, `Aksi` (Edit, Delete).
  - Contoh Data Gedung:
    1. Gedung A | Jakarta Pusat | 5 Lantai | 842 Titik Lampu
    2. Gedung B | Jakarta Selatan | 4 Lantai | 520 Titik Lampu
    3. Gedung C | Jakarta Barat | 6 Lantai | 1.024 Titik Lampu

---

## 5. Hubungan & Skema Database Backend

Entitas dan field berikut dipetakan ke fitur-fitur di dalam aplikasi Laravel:

### 1. `Building` (Gedung)
- **Field**: `id`, `name` (Nama Gedung), `location` (Lokasi), `description` (Deskripsi), `timestamps`.
- **Hubungan**: Memiliki banyak `Floors` (Lantai).

### 2. `Floor` (Lantai)
- **Field**: `id`, `building_id`, `name` (Nama Lantai), `floor_number` (Nomor Lantai), `timestamps`.
- **Hubungan**: Merupakan bagian dari `Building`, Memiliki banyak `Rooms` (Ruangan).

### 3. `Room` (Ruangan)
- **Field**: `id`, `floor_id`, `name` (Nama Ruangan), `type` (Jenis Ruangan: office, lobby, meeting_room, toilet, pantry, server_room, lounge, utility), `timestamps`.
- **Hubungan**: Merupakan bagian dari `Floor`, Memiliki banyak `Lamps` (Lampu).

### 4. `LampType` (Jenis/Tipe Lampu)
- **Field**: `id`, `name` (Nama Lampu), `type` (Jenis Lampu: LED Tube, LED Bulb, Downlight, Panel, Spotlight), `watt` (Daya Watt), `price` (Harga Lampu), `description` (Deskripsi), `status` (Status keaktifan tipe), `timestamps`.
- **Hubungan**: Memiliki banyak `Lamps`, Memiliki satu `Inventory`, Memiliki banyak `Transactions`.

### 5. `Lamp` (Titik Lampu)
- **Field**: `id`, `room_id`, `lamp_type_id`, `code` (Kode Unik Lampu, misal: `L-0001`), `position_x` (Koordinat X pada denah), `position_y` (Koordinat Y pada denah), `status` (on, off, rusak, warning), `installed_date` (Tanggal Pemasangan), `timestamps`.
- **Hubungan**: Merupakan bagian dari `Room`, Merupakan bagian dari `LampType`.

### 6. `Inventory` (Stok Gudang)
- **Field**: `id`, `lamp_type_id`, `stock_quantity` (Jumlah Stok), `min_stock` (Stok Minimum), `timestamps`.
- **Hubungan**: Merupakan bagian dari `LampType`, Memiliki banyak `InventoryTransactions`.

### 7. `InventoryTransaction` (Mutasi Gudang)
- **Field**: `id`, `inventory_id`, `type` (enum: `masuk`, `keluar`), `quantity` (Jumlah Barang), `transaction_date` (Tanggal Transaksi), `reference` (Referensi PO/Surat Jalan), `notes` (Catatan), `timestamps`.
- **Hubungan**: Merupakan bagian dari `Inventory`.

### 8. `Transaction` (Pencatatan Transaksi Penggantian)
- **Field**: `id`, `room_id`, `lamp_type_id`, `type` (enum: `penggantian`, `pemasangan`), `quantity` (Jumlah Lampu), `transaction_date` (Tanggal Transaksi), `technician` (Teknisi yang mengerjakan), `notes` (Catatan), `timestamps`.
- **Hubungan**: Merupakan bagian dari `Room`, Merupakan bagian dari `LampType`.

### 9. `Maintenance` (Pemeliharaan & Tiket Kerusakan)
- **Field**: `id`, `room_id`, `type` (Jenis Masalah/Pemeliharaan), `description` (Deskripsi Masalah), `priority` (enum: `high`, `medium`, `low`), `status` (enum: `pending`, `in_progress`, `completed`), `scheduled_date` (Tanggal Dijadwalkan), `completed_date` (Tanggal Selesai), `assigned_to` (Teknisi yang ditunjuk), `timestamps`.
- **Hubungan**: Merupakan bagian dari `Room`.

---

## 6. Rencana Tahapan Implementasi (Step-by-Step Coding Plan)

Untuk mengimplementasikan seluruh fitur LumiTrack FM di atas dari codebase template TailAdmin yang sudah ada, berikut adalah pembagian langkah pengodean:

### Bagian 1: Migrasi Database & Seeding
1. **Verifikasi & Jalankan Migration**: Pastikan skema tabel (`buildings`, `floors`, `rooms`, `lamp_types`, `lamps`, `inventories`, `inventory_transactions`, `transactions`, `maintenances`) sudah terbuat dengan benar.
2. **Kustomisasi DatabaseSeeder**: Hubungkan `DatabaseSeeder.php` agar secara otomatis memanggil `FmLightningSeeder` untuk menginput data simulasi awal. Buat juga seeder user admin dengan email `admin@company.com` dan password default (misal: `password` atau `admin123`).
3. **Eksekusi Seeder**: Jalankan perintah `php artisan migrate:fresh --seed` untuk membersihkan dan mengisi database dengan dataset simulasi yang sesuai dengan screenshots.

### Bagian 2: Otentikasi & Integrasi Layout (Login & Sidebar)
1. **Halaman Login (`pages.auth.signin`)**: 
   - Ubah file view login untuk menerapkan desain split-screen: sisi kiri berisi foto gedung perkantoran malam hari, sisi kanan berisi form login minimalis.
   - Sambungkan input email (`admin@company.com`) dan password ke backend Auth controller.
2. **Kustomisasi Sidebar Navigasi (`MenuHelper.php`)**:
   - Ganti isi menu di `MenuHelper.php` dengan 10 item navigasi LumiTrack FM yang sesuai.
   - Terapkan warna dasar hijau teal pada sidebar dan aktifkan status penanda menu aktif (active state).

### Bagian 3: Halaman Dashboard & Integrasi Chart
1. **Query Metrik KPI**: Buat controller dashboard untuk menghitung data riil dari database (Jumlah Titik Lampu, Lampu Terpasang dari tabel `lamps` dan `lamp_types`, Lampu Diganti Bulan Ini dari tabel `transactions`, dan Estimasi Biaya bulanan).
2. **Integrasi Grafik Konsumsi Energi (Line Chart)**: Ambil riwayat konsumsi daya per tanggal lalu render menggunakan ApexCharts/Chart.js pada komponen line chart.
3. **Integrasi Donut Chart (Status Lampu)**: Lakukan pengelompokan (group by) status pada tabel `lamps` (Aktif/on, Dim, Mati/off, Warning, Error) untuk dimasukkan ke dalam chart donut di dashboard.
4. **Tabel Ringkasan**: Tampilkan data transaksi terbaru dari tabel `transactions` dan ranking penggunaan energi ruangan tertinggi.

### Bagian 4: Floor Plan Interaktif (Denah Lampu)
1. **Struktur View & Canvas**: Buat halaman Floor Plan dengan dropdown filter Gedung dan Lantai.
2. **Pemuatan Peta Denah**: Sediakan blueprint layout gambar/SVG denah lantai (misal menggunakan SVG denah kantor standar).
3. **Plotting Titik Lampu Dinamis**: Tarik data koordinat `position_x` dan `position_y` dari tabel `lamps` untuk setiap lantai yang dipilih, lalu plot titik bulat berwarna (sesuai status) menggunakan absolute CSS positioning di atas denah.
4. **Interaksi Javascript (Alpine.js)**:
   - Hubungkan checkbox filter di panel kiri (Status Lampu, Pencarian, Layer) dengan visibilitas titik lampu.
   - Buat fungsi "+ Edit Titik Lampu" untuk memposisikan ulang titik lampu menggunakan drag-and-drop atau menambahkan titik lampu baru pada kanvas denah.

### Bagian 5: Manajemen Data Lampu (Lighting) & Inventaris (Inventory)
1. **Halaman Lighting**:
   - Buat CRUD tipe/spesifikasi lampu pada tabel `lamp_types` (Nama Lampu, Jenis, Watt, Stok, dll.).
   - Tambahkan indikator visual (badge) untuk stok aman, menipis, atau kritis/warning.
2. **Halaman Inventory**:
   - Buat visualisasi KPI stok (Total Stok, Stok Aman, Menipis, Habis).
   - Tampilkan tabel daftar lampu dengan persediaan kritis pada bagian "Stok Menipis / Habis".
   - Integrasikan donut chart "Kategori" distribusi jenis lampu dari stok gudang.

### Bagian 6: Pencatatan Transaksi & Log Mutasi
1. **Mutasi Barang Masuk/Keluar**:
   - Bangun antarmuka transaksi barang masuk/keluar untuk memperbarui nilai stok di tabel `inventories`.
2. **Transaksi Penggantian & Pemasangan Baru**:
   - Buat form "+ Transaksi Baru" untuk mencatat aksi penggantian lampu rusak oleh teknisi ke tabel `transactions`.
   - Hubungkan aksi ini dengan pengurangan otomatis jumlah stok lampu terkait di tabel `inventories` dan perubahan status lampu di tabel `lamps` dari `rusak` menjadi `on`.

### Bagian 7: Pemeliharaan (Maintenance) & Pelaporan (Report)
1. **Tiket Pemeliharaan**:
   - Implementasikan CRUD tiket pemeliharaan pada tabel `maintenances` (Lampu Mati, Lampu Flicker, Pembersihan).
   - Sediakan badge status prioritas (High, Medium, Low) dan alur status pengerjaan (Baru $\rightarrow$ Proses $\rightarrow$ Selesai).
2. **Halaman Report**:
   - Buat grafik area kumulatif pemakaian energi multi-gedung.
   - Hitung rata-rata konsumsi harian dan estimasi biaya energi bulanan.
   - Sediakan tombol export (PDF/Excel) untuk laporan pemakaian, penggantian, dan biaya.

### Bagian 8: Master Data Administratif
1. **Manajemen Fasilitas**: Buat manajemen data gedung, lantai, dan area/ruangan pada menu Master Data untuk mempermudah konfigurasi spasial sebelum proses plotting lampu di halaman Floor Plan.
2. **Manajemen Operator & User**: Hubungkan otorisasi user role untuk manajemen hak akses sistem.
