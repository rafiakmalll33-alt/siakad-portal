# SIAKAD PORTAL - Sistem Informasi Akademik

SISKAD Portal adalah aplikasi web modern untuk manajemen akademik universitas dengan fitur lengkap untuk Admin, Dosen, dan Mahasiswa.

## 🎯 Fitur Utama

### Admin Dashboard
- Dashboard statistik lengkap
- Manajemen data mahasiswa
- Persetujuan KRS mahasiswa
- Validasi pembayaran UKT
- Manajemen data master (tahun akademik, ruangan, mata kuliah)

### Dosen Panel
- Dashboard jadwal mengajar
- Persetujuan KRS mahasiswa bimbingan
- Input absensi mahasiswa
- Input nilai dan cetak PDF transkrip
- Manajemen kelas

### Mahasiswa Portal
- Dashboard dengan informasi IPK dan status pembayaran
- Pengisian KRS online
- Jadwal kuliah
- Rekapitulasi absensi
- Upload bukti pembayaran UKT
- Lihat nilai dan cetak KHS

## 📋 Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Tailwind CSS + Blade Templates
- **Database:** MySQL
- **Authentication:** Laravel Auth dengan Multi-Role
- **PDF Export:** Barryvdh DomPDF

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- MySQL/Laragon
- Node.js & npm

### Installation

```bash
# 1. Clone repository
git clone https://github.com/rafiakmalll33-alt/siakad-portal.git
cd siakad-portal

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Create database
# Buat database baru dengan nama: siakad_db

# 5. Run migrations & seeding
php artisan migrate:fresh --seed

# 6. Build assets
npm run build

# 7. Start server
php artisan serve
```

## 📝 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@siakad.local | password |
| Dosen | ahmad.subagyo@utn.ac.id | password |
| Mahasiswa | rian.hidayat@student.utn.ac.id | password |

## 📱 Responsive Design

Aplikasi ini fully responsive dan optimized untuk:
- 📱 Mobile (320px - 768px)
- 💻 Tablet (768px - 1024px)
- 🖥️ Desktop (1024px+)

## 🏗️ Project Structure

```
siakad-portal/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Dosen/
│   │   │   ├── Mahasiswa/
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Mahasiswa.php
│   │   ├── Dosen.php
│   │   ├── Krs.php
│   │   ├── Nilai.php
│   │   └── ...
│   └── Policies/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── components/
│       ├── auth/
│       ├── admin/
│       ├── dosen/
│       └── mahasiswa/
└── routes/
    └── web.php
```

## 🔐 Security Features

- CSRF Protection
- SQL Injection Prevention (Eloquent ORM)
- Role-Based Access Control
- Password Hashing (Bcrypt)
- Secure File Upload

## 📞 Support

Untuk pertanyaan atau masalah, silakan buat issue di repository ini.

## 📄 License

MIT License - feel free to use for educational purposes.

---

**© 2026 SIAKAD Portal - Universitas Teknologi Nusantara**