# Smart Inventory Pro &mdash; Proyek Uji Kompetensi BNSP (Skenario 3)

Aplikasi Web Manajemen Inventaris & Basis Data MySQL yang dirancang khusus untuk memenuhi standar unjuk kerja **Uji Kompetensi BNSP Pemrograman Web (Skenario 3)**:
- **Unit 1 (`TIK.PR08.007.01`):** Membuat Program Basis Data Berbasis MySQL/PostgreSQL
- **Unit 2 (`TIK.PR08.009.01`):** Membuat Program Aplikasi Web Berbasis PHP

---

## 📋 Panduan Menjawab 9 Langkah Kerja Asesor

| No | Langkah Kerja | File Sumber / Bukti Kode | Cara Menjawab & Menunjukkan ke Asesor |
|:---|:---|:---|:---|
| **1 & 5** | **Menjelaskan Kebutuhan Software** | `config/environment.php` | Tunjukkan menu **"Panduan Asesor > Langkah 1 & 5"**. Jelaskan spesifikasi runtime: PHP 8.3, MySQL 8.4, Web Server Apache/PHP Server, dan modul wajib (`pdo_mysql`, `session`, `openssl`, `json`). |
| **2** | **Mempersiapkan Security** | `helpers/utils.php`, `classes/Database.php` | Jelaskan 4 pilar keamanan yang diterapkan: <br>1. *Anti-SQL Injection* via PDO Prepared Statements.<br>2. *Anti-XSS* via `htmlspecialchars()`.<br>3. *Password Security* via `password_hash()` BCRYPT.<br>4. *Anti-CSRF* via token acak 32-byte pada session form. |
| **3** | **Sintaks Khusus MySQL** | `database.sql`, `classes/Product.php` | Buka `database.sql` atau tab **Langkah 3**. Tunjukkan contoh query `INNER JOIN`, `GROUP BY`, fungsi agregat `COUNT()`, `SUM()`, `AVG()`, dan klausa `WHERE ... LIKE`. |
| **4** | **Pengaksesan Database** | `classes/Database.php` | Jelaskan penerapan **Pola Desain Singleton** pada koneksi PDO agar hanya ada 1 instance database yang hemat memori, serta penanganan error menggunakan `PDOException`. |
| **6 & 7** | **Konsep Array & Variabel PHP** | `views/dashboard.php`, `views/asesor_guide.php` | Jelaskan:<br>1. Tipe data skalar (string, int, float, bool).<br>2. Array Indexed, Associative, Multidimensional.<br>3. Variabel Internal / Superglobal: `$_GET` (pencarian/filter), `$_POST` (form CRUD), `$_SESSION` (login/flash), `$_SERVER`. |
| **8** | **Menerapkan Fungsi & Kelas (OOP)** | `classes/*.php`, `helpers/utils.php` | Tunjukkan kelas OOP (`Product`, `Category`, `User`, `Validator`, `Database`) dengan properti, method, enkapsulasi (`public`/`private`), dan constructor `__construct()`. Serta fungsi pembantu di `helpers/utils.php`. |
| **9** | **Koneksi Database & Manipulasi Data (CRUD)** | `classes/Product.php`, `views/product_*.php` | Demonstrasikan langsung fungsi **CREATE** (tambah produk), **READ** (lihat tabel & pencarian), **UPDATE** (edit data), dan **DELETE** (hapus item dengan konfirmasi dialog). |

---

## 🚀 Cara Menjalankan Aplikasi

### 1. Inisialisasi Database
Pastikan service MySQL aktif, lalu impor file `database.sql`:
```bash
mysql -u root < database.sql
```
*(Database default: `db_bnsp_inventaris`)*

### 2. Menjalankan Web Server Lokal
Jalankan server PHP built-in di root folder project:
```bash
php -S localhost:8000
```
Buka browser dan akses: **`http://localhost:8000`**

### 3. Akun Login Default
- **Username:** `admin`
- **Password:** `admin123`
*(Tersedia tombol auto-fill di halaman login untuk kemudahan demo)*

---

## 📁 Struktur Direktori Proyek

```
BNSP/
├── assets/
│   ├── css/
│   │   ├── style.css            # Sistem desain modern, glassmorphism, responsive (Mode Pro)
│   │   └── native.css           # Styling dasar panduan
│   └── js/
│       └── app.js               # Interaktivitas, konfirmasi aksi, integrasi Chart.js
├── classes/
│   ├── Database.php             # Singleton PDO Connection dengan Auto-Fallback
│   ├── Product.php              # OOP Model Produk (CRUD & Agregasi)
│   ├── Category.php             # OOP Model Kategori
│   ├── User.php                 # OOP Model Autentikasi Pengguna
│   └── Validator.php            # OOP Input Validation
├── config/
│   ├── config.php               # Konfigurasi konstanta, database & autoloader
│   ├── config.local.example.php # Contoh konfigurasi override server
│   └── environment.php          # Inspeksi spesifikasi software/server runtime
├── helpers/
│   └── utils.php                # Helper format rupiah, sanitasi, CSRF, flash message
├── views/
│   ├── header.php               # Template Header & Navbar Modern
│   ├── footer.php               # Template Footer Modern
│   ├── dashboard.php            # Statistik agregasi, grafik Chart.js, ringkasan
│   ├── product_list.php         # Tabel CRUD produk pro, search, filter, pagination
│   ├── product_form.php         # Form input/edit produk modern
│   └── asesor_guide.php         # Tab interaktif panduan demonstrasi 9 langkah
├── database.sql                 # Skrip DDL/DML, relasi FK, dan seed data awal
├── index.php                    # Entry point aplikasi (Default: Simple Native, Pro via router)
├── login.php                    # Form login sistem (Anti-SQLi & Password BCRYPT)
├── logout.php                   # Pembersihan sesi user & redirect
├── tambah.php                   # Form tambah data produk (PHP Native INSERT)
├── edit.php                     # Form edit data produk (PHP Native UPDATE)
├── hapus.php                    # Skrip hapus data produk (PHP Native DELETE)
├── panduan.php                  # Lembar jawaban 9 langkah kerja asesor (Native)
├── koneksi.php                  # Koneksi PDO Native dengan Multi-Candidate Auto-Fallback
└── README.md                    # Dokumentasi teknis & petunjuk pengujian asesor
```
