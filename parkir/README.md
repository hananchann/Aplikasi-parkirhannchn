# 🅿️ SISTEM INFORMASI PARKIR

Aplikasi web berbasis PHP Native untuk mengelola sistem parkir dengan 3 level user (Admin, Petugas, Owner).

## 📋 Fitur Utama

### 👑 Admin
- ✅ Login & Logout
- ✅ CRUD User (Kelola semua user)
- ✅ CRUD Tarif Parkir
- ✅ CRUD Area Parkir
- ✅ CRUD Jenis Kendaraan
- ✅ Lihat Log Aktivitas Sistem

### 🅿️ Petugas
- ✅ Login & Logout
- ✅ Input Transaksi Kendaraan Masuk
- ✅ Proses Kendaraan Keluar
- ✅ Hitung Biaya Parkir Otomatis
- ✅ Cetak Struk Parkir

### 📊 Owner
- ✅ Login & Logout
- ✅ Lihat Rekap Transaksi
- ✅ Filter Laporan Berdasarkan Tanggal
- ✅ Lihat Total Pendapatan
- ✅ Lihat Total Transaksi

## 🛠️ Teknologi

- **Backend**: PHP Native (tanpa framework)
- **Database**: MySQL
- **Server**: XAMPP (Apache + MySQL)
- **Frontend**: Bootstrap 5, FontAwesome
- **Architecture**: MVC Pattern
- **Security**: Prepared Statements (mysqli), Password Hashing

## 📁 Struktur Folder

```
parkir/
├── config/
│   └── koneksi.php          # Database connection
├── models/
│   ├── User.php             # User model
│   ├── Tarif.php            # Tarif model
│   ├── Area.php             # Area model
│   ├── Kendaraan.php        # Kendaraan model
│   └── Transaksi.php        # Transaksi model
├── controllers/
│   └── AuthController.php   # Authentication controller
├── views/
│   ├── auth/                # Login pages
│   ├── admin/               # Admin pages
│   ├── petugas/             # Petugas pages
│   ├── owner/               # Owner pages
│   └── layouts/             # Layout components
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   └── js/
│       └── script.js        # JavaScript utilities
├── db_parkir.sql            # Database SQL file
├── index.php                # Login page
├── process_login.php        # Login handler
├── logout.php               # Logout handler
└── README.md                # This file
```

## 🚀 Instalasi

### 1. Persiapan
- Install XAMPP
- Pastikan Apache dan MySQL sudah running

### 2. Setup Database
1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Import file `db_parkir.sql`
   - Klik "Import"
   - Pilih file `db_parkir.sql`
   - Klik "Go"

### 3. Setup Aplikasi
1. Copy folder `parkir` ke `c:\xampp\htdocs\APP-hann\`
2. Pastikan struktur: `c:\xampp\htdocs\APP-hann\parkir\`

### 4. Akses Aplikasi
Buka browser dan akses: `http://localhost/APP-hann/parkir/`

## 🔐 Akun Default

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |
| Petugas | petugas | petugas123 |
| Owner | owner | owner123 |

## 💡 Cara Penggunaan

### Admin
1. Login dengan akun admin
2. Kelola user, tarif, area, dan jenis kendaraan melalui menu sidebar
3. Lihat log aktivitas sistem

### Petugas
1. Login dengan akun petugas
2. **Kendaraan Masuk**: Input plat nomor, pilih jenis kendaraan dan area
3. **Kendaraan Keluar**: Cari plat nomor, sistem otomatis hitung biaya
4. Cetak struk pembayaran

### Owner
1. Login dengan akun owner
2. Pilih rentang tanggal untuk melihat laporan
3. Lihat total pendapatan dan jumlah transaksi
4. Cetak laporan

## 🎨 Desain

- **Tema**: Dark Blue/Navy (#1E3A8A)
- **Layout**: Sidebar navigation dengan card-based design
- **Responsive**: Mobile-friendly
- **Icons**: FontAwesome 6
- **Framework**: Bootstrap 5

## 🔒 Keamanan

- ✅ Prepared Statements untuk semua query database
- ✅ Password hashing menggunakan `password_hash()`
- ✅ Session management untuk autentikasi
- ✅ Role-based access control
- ✅ Input sanitization
- ✅ SQL injection prevention

## ⚡ Optimasi

- ✅ Database indexing pada foreign keys
- ✅ Efficient JOIN queries
- ✅ Pagination untuk data besar
- ✅ Optimized SELECT queries (tidak menggunakan SELECT *)
- ✅ Reusable functions untuk perhitungan

## 📊 Database Schema

### Tables
1. **users** - Multi-role user management
2. **tarif** - Parking rate configuration
3. **area_parkir** - Parking area management
4. **kendaraan** - Vehicle type master data
5. **transaksi** - Parking transactions
6. **log_aktivitas** - System activity logs

### Relationships
- `transaksi.id_kendaraan` → `kendaraan.id_kendaraan`
- `transaksi.id_area` → `area_parkir.id_area`
- `transaksi.id_petugas` → `users.id_user`

## 🧮 Perhitungan Biaya

- Durasi parkir dihitung dalam **jam** (dibulatkan ke atas)
- Minimum durasi: **1 jam**
- Rumus: `Total Bayar = Durasi (jam) × Tarif Per Jam`

## 📝 Catatan

- Sistem berjalan di **localhost** (offline)
- Menggunakan **PHP Native** tanpa framework
- Database menggunakan **MySQL**
- Semua password di-hash menggunakan `password_hash()`
- Log aktivitas tercatat untuk setiap aksi penting

## 🐛 Troubleshooting

### Database Connection Error
- Pastikan MySQL sudah running di XAMPP
- Cek kredensial database di `config/koneksi.php`
- Default: host=localhost, user=root, password=(kosong)

### Login Tidak Berhasil
- Pastikan database sudah di-import
- Cek apakah data user sudah ada di tabel `users`
- Gunakan akun default yang sudah disediakan

### Halaman Blank/Error
- Aktifkan error reporting di PHP
- Cek error log di XAMPP
- Pastikan semua file ada di folder yang benar

## 📞 Support

Jika ada pertanyaan atau masalah, silakan hubungi administrator sistem.

---

**Dibuat untuk**: UKK RPL 2025/2026  
**Versi**: 1.0  
**Tanggal**: Februari 2026
