# Sistem Absensi QR Code

Sistem absensi berbasis QR Code yang dibangun dengan Laravel 10. Sistem ini memiliki dua role utama: **Admin** dan **Pegawai** dengan fitur-fitur lengkap untuk manajemen absensi harian.

## 🚀 Fitur Utama

### Admin Features
- **Dashboard Admin**: Overview statistik absensi
- **Manajemen Pegawai**: CRUD pegawai dengan auto-generate QR Code
- **Manajemen Jadwal Kerja**: Atur jadwal kerja per hari untuk setiap pegawai
- **Laporan Absensi**: Filter dan export laporan absensi ke Excel
- **QR Code Management**: Generate dan print QR Code untuk pegawai

### Pegawai Features
- **Dashboard Pegawai**: Status absensi hari ini dan jadwal kerja
- **QR Code Scanner**: Scan QR Code untuk absensi masuk/pulang
- **Riwayat Absensi**: Lihat history absensi pribadi
- **Jadwal Kerja**: Lihat jadwal kerja mingguan
- **Print QR Code**: Cetak QR Code pribadi

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, jQuery
- **Database**: MySQL/MariaDB
- **QR Code**: SimpleSoftwareIO/simple-qrcode
- **Icons**: Font Awesome 6
- **Export**: SheetJS (XLSX)

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (optional)
- Web Server (Apache/Nginx)

## 🔧 Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd absensi-qrcode
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_qrcode
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 6. Start Development Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## 👥 Default Login Credentials

### Admin
- **Email**: admin@absensi.com
- **Password**: admin123

### Sample Pegawai
- **Email**: john@absensi.com
- **Password**: pegawai123

- **Email**: jane@absensi.com
- **Password**: pegawai123

## 📱 Cara Penggunaan

### Untuk Admin

1. **Login** dengan kredensial admin
2. **Kelola Pegawai**:
   - Tambah pegawai baru (otomatis generate QR Code)
   - Edit data pegawai
   - Hapus pegawai
   - Lihat dan print QR Code pegawai

3. **Atur Jadwal Kerja**:
   - Pilih pegawai
   - Set jadwal per hari (Senin-Minggu)
   - Tentukan jam masuk/pulang
   - Mark hari libur jika diperlukan

4. **Monitor Laporan**:
   - Filter berdasarkan tanggal dan pegawai
   - Export ke Excel
   - Lihat statistik absensi

### Untuk Pegawai

1. **Login** dengan kredensial pegawai
2. **Dashboard**: Lihat status absensi hari ini dan jadwal
3. **Absensi**:
   - Klik "Scan QR Code"
   - Izinkan akses kamera
   - Scan QR Code pribadi untuk absen masuk
   - Scan lagi untuk absen pulang
4. **Riwayat**: Lihat history absensi pribadi
5. **Jadwal**: Cek jadwal kerja mingguan

## 🗂️ Struktur Database

### Users Table
- `id`: Primary key
- `name`: Nama lengkap
- `email`: Email (unique)
- `password`: Password (hashed)
- `role`: enum('admin', 'pegawai')
- `qrcode`: UUID untuk QR Code (unique)

### Absensis Table
- `id`: Primary key
- `user_id`: Foreign key ke users
- `tanggal`: Tanggal absensi
- `jam_masuk`: Waktu absen masuk
- `jam_pulang`: Waktu absen pulang
- `status`: enum('hadir', 'terlambat', 'alpha')
- Unique constraint: `user_id` + `tanggal`

### Jadwal Kerjas Table
- `id`: Primary key
- `id_user`: Foreign key ke users
- `hari`: enum('Senin', 'Selasa', ..., 'Minggu')
- `jam_masuk`: Jam masuk kerja
- `jam_pulang`: Jam pulang kerja
- `is_libur`: Boolean untuk hari libur
- Unique constraint: `id_user` + `hari`

## 🔐 Security Features

- **Authentication**: Laravel built-in authentication
- **Role-based Access**: Middleware untuk admin/pegawai
- **CSRF Protection**: Token CSRF pada semua form
- **Password Hashing**: Bcrypt hashing
- **Input Validation**: Server-side validation
- **Unique QR Codes**: UUID-based QR codes

## 📊 Business Logic

### Absensi Rules
1. **Satu absensi per hari**: Pegawai hanya bisa absen sekali per hari
2. **Absen masuk dulu**: Harus absen masuk sebelum bisa absen pulang
3. **Status otomatis**: 
   - "Hadir" jika absen sebelum/tepat jam masuk
   - "Terlambat" jika absen setelah jam masuk
4. **Validasi jadwal**: Cek jadwal kerja dan hari libur
5. **QR Code validation**: Validasi QR Code yang valid

### Jadwal Kerja Rules
1. **Satu jadwal per hari**: Setiap pegawai punya jadwal untuk setiap hari
2. **Hari libur**: Bisa di-set sebagai hari libur
3. **Jam kerja**: Jam pulang harus > jam masuk
4. **Fleksibel**: Admin bisa ubah jadwal kapan saja

## 🎨 UI/UX Features

- **Responsive Design**: Bootstrap 5 responsive grid
- **Modern Interface**: Clean dan user-friendly
- **Real-time Feedback**: Alert dan notification
- **Print Support**: Print QR Code dengan CSS print media
- **Mobile Friendly**: Camera access untuk mobile scanning
- **Dark/Light Theme**: Consistent color scheme

## 🔧 Customization

### Menambah Status Absensi Baru
1. Update enum di migration `absensis` table
2. Update validation di controller
3. Update badge colors di view

### Mengubah Jam Kerja Default
Edit di `AdminController@createJadwal`:
```php
'jam_masuk' => 'required',
'jam_pulang' => 'required',
```

### Custom QR Code Design
Edit di view `pegawai/qrcode.blade.php`:
```php
{!! QrCode::size(200)->backgroundColor(255,255,255)->generate($user->qrcode) !!}
```

## 🐛 Troubleshooting

### QR Code Scanner Tidak Berfungsi
- Pastikan HTTPS atau localhost
- Allow camera permission
- Cek browser compatibility

### Database Connection Error
- Cek konfigurasi `.env`
- Pastikan MySQL service running
- Cek user privileges

### Permission Denied
- Cek file permissions (755 untuk folder, 644 untuk file)
- Pastikan storage folder writable

## 📝 API Endpoints

### Authentication
- `GET /login` - Show login form
- `POST /login` - Process login
- `POST /logout` - Logout

### Admin Routes (Prefix: /admin)
- `GET /dashboard` - Admin dashboard
- `GET /pegawai` - List pegawai
- `POST /pegawai` - Create pegawai
- `PUT /pegawai/{id}` - Update pegawai
- `DELETE /pegawai/{id}` - Delete pegawai
- `GET /jadwal` - Jadwal management
- `POST /jadwal` - Create jadwal
- `GET /laporan` - Reports

### Pegawai Routes (Prefix: /pegawai)
- `GET /dashboard` - Pegawai dashboard
- `GET /qrcode` - Show QR code
- `POST /scan-absensi` - Process attendance
- `GET /absensi` - Attendance history
- `GET /jadwal` - Work schedule

## 🤝 Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 👨‍💻 Developer

Developed with ❤️ using Laravel & Bootstrap

---

**Happy Coding! 🚀**
