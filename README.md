# Sistem Inventaris Barang &mdash; Uji Kompetensi Keahlian BNSP (Skenario 3)

Aplikasi Web Manajemen Inventaris & Basis Data Terstruktur yang dirancang khusus untuk memenuhi seluruh kriteria unjuk kerja dan standar kompetensi **BNSP Pemrograman Web (Skenario 3)**:
- **Unit 1 (`TIK.PR08.007.01`):** Membuat Program Basis Data Berbasis MySQL/PostgreSQL
- **Unit 2 (`TIK.PR08.009.01`):** Membuat Program Aplikasi Web Berbasis PHP

---

## 🎯 Panduan Lengkap Pemenuhan 9 Langkah Kerja Asesor

| No | Langkah Kerja Asesor | File Sumber / Implementasi Kode | Cara Pengujian & Penjelasan Teknis |
|:---|:---|:---|:---|
| **1 & 5** | **Menjelaskan Kebutuhan Software & Lingkungan** | `config/environment.php`, `config/config.php` | Sistem berjalan pada PHP 8.x/7.4+, MySQL 8.x/MariaDB, dan web server (Apache/Laragon/PHP CLI). Ekstensi wajib yang digunakan: `pdo_mysql`, `session`, `openssl`, `json`. Buka menu **Panduan Asesor** untuk melihat diagnosa software otomatis. |
| **2** | **Mempersiapkan Aspek Keamanan (Security)** | `helpers/utils.php`, `classes/Database.php`, `login.php` | Diterapkan 4 pilar keamanan utama:<br>1. **Anti-SQL Injection:** Menggunakan PDO Prepared Statements & Parameter Binding (`:param`).<br>2. **Anti-XSS (Cross-Site Scripting):** Fungsi sanitasi `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` pada semua output.<br>3. **Keamanan Password:** Hashing password menggunakan algoritma `password_hash()` BCRYPT.<br>4. **Anti-CSRF:** Proteksi form dengan CSRF token acak 32-byte berbasis sesi. |
| **3** | **Sintaks Khusus MySQL (Agregasi & Relasi)** | `database.sql`, `classes/Product.php`, `index.php` | Memanfaatkan relasi Foreign Key (`kategori_id` &rarr; `kategori.id`), klausa `INNER JOIN`, klausa pencarian `WHERE ... LIKE`, pengurutan `ORDER BY`, serta fungsi agregasi MySQL: `COUNT(*)`, `SUM(stok)`, `SUM(harga_jual * stok)`, dan `AVG()`. |
| **4** | **Pengaksesan Database (Database Access Layer)** | `classes/Database.php`, `koneksi.php` | Mengimplementasikan **Pola Desain Singleton** pada koneksi PDO untuk efisiensi memori, didukung mekanisme *Multi-Candidate Auto-Fallback* (otomatis mendeteksi port MySQL 3306, password root kosong/laragon/root). Penanganan error dilakukan dengan blok `try-catch` dan `PDOException`. |
| **6 & 7** | **Konsep Array & Variabel PHP** | `views/dashboard.php`, `index.php`, `helpers/utils.php` | 1. **Tipe Data Skalar:** String, Integer, Float, Boolean.<br>2. **Array:** *Indexed Array*, *Associative Array* (hasil `fetchAll(PDO::FETCH_ASSOC)`), dan *Multidimensional Array*.<br>3. **Variabel Superglobal PHP:** `$_GET` (pencarian & filter kategori), `$_POST` (pengolahan form CRUD), `$_SESSION` (otentikasi & flash message), `$_SERVER` (routing & request method). |
| **8** | **Menerapkan Fungsi & Pemrograman Berorientasi Objek (OOP)** | `classes/*.php` (`Product`, `Category`, `User`, `Validator`, `Database`), `helpers/utils.php` | Menerapkan prinsip OOP murni: Encapsulation (`public`/`private`), Constructor `__construct()`, Reusable Helper Functions, dan Separation of Concerns. |
| **9** | **Koneksi Database & Manipulasi Data (CRUD)** | `classes/Product.php`, `tambah.php`, `edit.php`, `hapus.php`, `views/product_*.php` | Mendemonstrasikan 4 operasi data lengkap:<br>&bull; **Create:** Tambah data produk baru dengan validasi format.<br>&bull; **Read:** Tampilkan daftar produk dengan filter kategori dan live search.<br>&bull; **Update:** Edit data produk yang sudah ada.<br>&bull; **Delete:** Hapus data produk dengan modal konfirmasi keamanan. |

---

## 🚀 Cara Menjalankan & Menguji Aplikasi

### 1. Persiapan Database
1. Pastikan service MySQL/MariaDB sudah aktif (via XAMPP / Laragon / MySQL Server).
2. Buat database atau langsung impor file **`database.sql`**:
   - **Via Command Prompt / Terminal:**
     ```bash
     mysql -u root -p < database.sql
     ```
     *(Jika tanpa password di Laragon/XAMPP: `mysql -u root < database.sql`)*
   - **Via phpMyAdmin / HeidiSQL:**
     Buat database baru bernama `db_bnsp_inventaris`, lalu klik tab **Import** dan pilih file `database.sql`.

### 2. Menjalankan Web Server
Pilih salah satu cara berikut:

- **Cara A: Menggunakan PHP Built-in Server (Paling Cepat & Praktis):**
  Buka Terminal / Command Prompt di folder proyek ini, lalu jalankan:
  ```bash
  php -S localhost:8000
  ```
  Akses di browser: **`http://localhost:8000`**

- **Cara B: Menggunakan Laragon / XAMPP:**
  Pindahkan/salin folder proyek ini ke direktori web root:
  - Laragon: `C:\laragon\www\BNSP` &rarr; Akses: `http://localhost/BNSP` atau `http://bnsp.test`
  - XAMPP: `C:\xampp\htdocs\BNSP` &rarr; Akses: `http://localhost/BNSP`

---

## 🔑 Kredensial Login Sistem
Halaman login dilengkapi tombol auto-fill untuk kemudahan demonstrasi:
- **Username:** `admin`
- **Password:** `admin123`

---

## 🌟 Fitur Khusus & Mode Tampilan Ganda (Dual Mode)

Aplikasi ini dilengkapi dua mode antarmuka untuk fleksibilitas penilaian:

1. **Mode Sederhana / Simple Mode (Default):**
   - Tampilan bersih, flat, dan fokus pada kepatuhan lembar kerja ujian.
   - Dilengkapi *running text* infinite loop di footer dengan efek partikel interaktif saat diklik 1x.
2. **Mode Modern / Pro Mode (Smart Inventory Pro):**
   - Desain modern glassmorphism responsif, visualisasi grafik data statistik (Chart.js), filter canggih, badge stok otomatis, dan tab panduan langkah kerja terintegrasi.

### 🕹️ Cara Beralih ke Mode Modern (Easter Egg Triggers):
- **Opsi 1:** Tekan kombinasi keyboard **`Ctrl + Shift + M`**.
- **Opsi 2:** **Klik 2x (Double-Click)** pada kotak running text di bagian footer.
- **Opsi 3:** **Klik 3x** secara cepat pada Judul Halaman *"SISTEM INVENTARIS BARANG"*.
- **Opsi 4:** Tambahkan parameter URL: `http://localhost:8000/index.php?mode=pro`

---

## 📂 Struktur Berkas Proyek

```text
BNSP/
├── assets/
│   ├── css/
│   │   ├── style.css            # Desain antarmuka modern & glassmorphism (Mode Pro)
│   │   ├── simple.css           # Desain antarmuka mode sederhana
│   │   └── native.css           # Styling dasar dokumen
│   └── js/
│       ├── app.js               # Logika frontend, konfirmasi aksi, grafik Chart.js
│       └── easter_egg.js        # Logika trigger beralih mode tampilan
├── classes/
│   ├── Database.php             # Singleton PDO Connection & multi-host fallback
│   ├── Product.php              # OOP Model Data Produk (CRUD & Agregasi)
│   ├── Category.php             # OOP Model Kategori Barang
│   ├── User.php                 # OOP Model Autentikasi Pengguna
│   └── Validator.php            # OOP Validator input form
├── config/
│   ├── config.php               # Konfigurasi konstanta, database & autoloader kelas
│   ├── config.local.example.php # Template konfigurasi lokal kustom
│   └── environment.php          # Diagnosa lingkungan sistem & ekstensi PHP
├── helpers/
│   └── utils.php                # Helper format rupiah, sanitasi input, token CSRF, flash message
├── views/
│   ├── header.php               # Template Header & Navigasi Mode Modern
│   ├── footer.php               # Template Footer Mode Modern
│   ├── dashboard.php            # Ringkasan statistik & widget visualisasi
│   ├── product_list.php         # Tabel data produk lengkap dengan filter & search
│   ├── product_form.php         # Form input & edit produk
│   └── asesor_guide.php         # Tab interaktif panduan demonstrasi 9 langkah kerja
├── simple/
│   ├── index.php                # Entry point mode sederhana (Pure Native)
│   ├── tambah.php               # Form tambah mode sederhana
│   ├── edit.php                 # Form edit mode sederhana
│   ├── hapus.php                # Aksi hapus mode sederhana
│   ├── login.php                # Login mode sederhana
│   ├── logout.php               # Logout mode sederhana
│   ├── panduan.php              # Panduan asesmen mode sederhana
│   └── koneksi.php              # Koneksi PDO sederhana
├── database.sql                 # Skrip DDL/DML, Foreign Key, dan Seed Data
├── index.php                    # Router utama aplikasi (Simple Mode & Pro Mode)
├── login.php                    # Form login sistem
├── logout.php                   # Pembersihan sesi login
├── tambah.php                   # Form tambah produk (Native)
├── edit.php                     # Form edit produk (Native)
├── hapus.php                    # Aksi hapus produk (Native)
├── panduan.php                  # Halaman panduan 9 langkah kerja
├── koneksi.php                  # Koneksi database native dengan auto-fallback
└── README.md                    # Dokumentasi teknis dan panduan pengujian asesor
```

---

## 👨‍💻 Pengembang
- **Nama Peserta:** Bagus Dwicky Primananda
- **Skema Uji Kompetensi:** Pemrograman Web (Web Developer) - BNSP Skenario 3
- **Tahun:** 2026
