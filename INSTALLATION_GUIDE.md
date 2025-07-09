# Panduan Instalasi Sistem Absensi QR Code

## 📋 Prerequisites

Pastikan sistem Anda memiliki:
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Web Server (Apache/Nginx/Laragon)
- Git (optional)

## 🔧 Langkah Instalasi

### 1. Persiapan Environment

#### Untuk Windows (Laragon)
1. Download dan install [Laragon](https://laragon.org/)
2. Start Apache dan MySQL dari Laragon
3. Buka terminal Laragon

#### Untuk Linux/Mac (XAMPP/MAMP)
1. Install XAMPP/MAMP
2. Start Apache dan MySQL
3. Buka terminal

### 2. Clone/Download Project

#### Jika menggunakan Git:
```bash
git clone <repository-url>
cd absensi-qrcode
```

#### Jika download ZIP:
1. Extract file ZIP ke folder web server
2. Rename folder menjadi `absensi-qrcode`
3. Masuk ke folder tersebut

### 3. Install Dependencies

```bash
composer install
```

Jika ada error, coba:
```bash
composer install --ignore-platform-reqs
```

### 4. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Konfigurasi Database

#### Buat Database
1. Buka phpMyAdmin atau MySQL client
2. Buat database baru: `absensi_qrcode`

#### Edit file .env
```env
APP_NAME="Sistem Absensi QR Code"
APP_ENV=local
APP_KEY=base64:... (sudah di-generate)
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_qrcode
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### 6. Jalankan Migration dan Seeder

```bash
php artisan migrate
php artisan db:seed
```

### 7. Set Permissions (Linux/Mac)

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 8. Start Development Server

```bash
php artisan serve
```

Atau jika menggunakan Laragon, akses: `http://absensi-qrcode.test`

## 🔑 Login Credentials

### Admin
- **Email**: admin@absensi.com
- **Password**: admin123

### Sample Pegawai
- **Email**: john@absensi.com
- **Password**: pegawai123

- **Email**: jane@absensi.com
- **Password**: pegawai123

## ✅ Verifikasi Instalasi

1. Buka browser dan akses aplikasi
2. Login dengan kredensial admin
3. Cek dashboard admin
4. Coba tambah pegawai baru
5. Login sebagai pegawai dan test QR scanner

## 🐛 Troubleshooting

### Error: "Class 'PDO' not found"
```bash
# Aktifkan extension PHP
# Edit php.ini dan uncomment:
extension=pdo_mysql
```

### Error: "Permission denied"
```bash
# Set permission yang benar
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

### Error: "Key path does not exist"
```bash
php artisan key:generate
```

### Error: "SQLSTATE[HY000] [1045]"
- Cek username/password database di .env
- Pastikan MySQL service running
- Cek port database (default: 3306)

### QR Scanner tidak berfungsi
- Pastikan menggunakan HTTPS atau localhost
- Allow camera permission di browser
- Test di browser yang support WebRTC

### Error: "Composer not found"
1. Download Composer dari https://getcomposer.org/
2. Install secara global
3. Restart terminal

## 📱 Testing di Mobile

1. Pastikan laptop/PC dan mobile di network yang sama
2. Cek IP address laptop: `ipconfig` (Windows) atau `ifconfig` (Linux/Mac)
3. Akses dari mobile: `http://[IP_ADDRESS]:8000`
4. Allow camera permission untuk QR scanner

## 🚀 Production Deployment

### 1. Environment Production
```env
APP_ENV=production
APP_DEBUG=false
```

### 2. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 3. Web Server Configuration

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/absensi-qrcode/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 📞 Support

Jika mengalami masalah:
1. Cek log error di `storage/logs/laravel.log`
2. Pastikan semua requirements terpenuhi
3. Cek konfigurasi database dan web server
4. Test di browser yang berbeda

---

**Selamat! Sistem Absensi QR Code siap digunakan! 🎉**
