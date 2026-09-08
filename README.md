# 📚 Library System

**Library System** adalah aplikasi manajemen perpustakaan sederhana yang dibuat menggunakan **Laravel 13** sebagai project latihan dari Ebook **30 Hari Belajar Laravel** oleh CodingAsik Academy.

Project ini dibuat untuk membantu pembaca mempraktikkan konsep Laravel secara langsung, mulai dari routing, migration, model, relationship, validation, CRUD, hingga pembuatan antarmuka menggunakan Livewire dan Tailwind CSS.

---

## ✨ Fitur

Beberapa fitur yang tersedia di dalam aplikasi:

* 📚 **Manajemen Buku**

  * Menampilkan daftar buku
  * Menambahkan buku
  * Mengubah data buku
  * Menghapus buku
  * Upload cover buku
  * Pencarian buku
  * Sorting data buku
  * Pagination

* 🗂️ **Manajemen Kategori**

  * Menampilkan kategori buku
  * Menambahkan kategori
  * Mengubah kategori
  * Menghapus kategori

* 🔎 **Pencarian & Filter**

  * Pencarian berdasarkan judul atau penulis
  * Filter berdasarkan kategori
  * Sorting berdasarkan kolom tertentu

* 📦 **Manajemen Stok**

  * Menampilkan jumlah stok buku
  * Mengelola perubahan stok
  * Mencatat aktivitas stok

* 🎨 **Responsive UI**

  * Menggunakan Tailwind CSS
  * Responsive pada berbagai ukuran layar
  * Menggunakan reusable UI components

---

## 🛠️ Teknologi

Project ini dibuat menggunakan:

| Teknologi    | Versi |
| ------------ | ----- |
| Laravel      | 13    |
| PHP          | 8.3+  |
| Livewire     | 4     |
| Tailwind CSS | 4     |
| Vite         | -     |
| MySQL        | -     |
| Blade        | -     |
| JavaScript   | -     |

---

## 📋 Requirements

Pastikan komputer kamu sudah memiliki:

* **PHP 8.3 atau lebih baru**
* **Composer**
* **Node.js & NPM**
* **MySQL / MariaDB**
* **Git**

Untuk memastikan versi yang terpasang:

```bash
php -v
composer -V
node -v
npm -v
```

---

## 🚀 Cara Install

### 1. Clone Repository

Clone repository ke komputer:

```bash
git clone https://github.com/username/library-system.git
```

Masuk ke folder project:

```bash
cd library-system
```

> Ganti URL repository di atas dengan URL repository Library System kamu.

---

### 2. Install Dependency PHP

Jalankan:

```bash
composer install
```

---

### 3. Install Dependency JavaScript

Jalankan:

```bash
npm install
```

---

### 4. Buat File Environment

Copy `.env.example` menjadi `.env`.

```bash
cp .env.example .env
```

Jika menggunakan Windows dan perintah tersebut tidak tersedia, kamu bisa menyalin `.env.example` secara manual dan mengubah namanya menjadi `.env`.

---

### 5. Generate Application Key

Jalankan:

```bash
php artisan key:generate
```

---

### 6. Konfigurasi Database

Buka file `.env`, kemudian sesuaikan konfigurasi database:

```env
DB_DATABASE=library_system
DB_USERNAME=root
DB_PASSWORD=
```

Buat database dengan nama:

```text
library_system
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan konfigurasi MySQL di komputer kamu.

---

### 7. Jalankan Migration & Seeder

Jalankan:

```bash
php artisan migrate --seed
```

Perintah tersebut akan membuat tabel database sekaligus menjalankan seeder yang tersedia.

---

### 8. Buat Storage Link

Karena aplikasi menggunakan upload cover buku, buat symbolic link storage dengan:

```bash
php artisan storage:link
```

---

### 9. Jalankan Development Server

Jalankan Laravel:

```bash
php artisan serve
```

Kemudian buka:

```text
http://127.0.0.1:8000
```

---

### 10. Jalankan Vite

Buka terminal baru dan jalankan:

```bash
npm run dev
```

Biarkan proses Vite tetap berjalan selama proses development.

---

## 📁 Struktur Project

Struktur project mengikuti struktur standar Laravel dengan beberapa tambahan untuk Livewire:

```text
library-system/
├── app/
│   ├── Livewire/
│   ├── Models/
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── components/
│       ├── layouts/
│       └── pages/
├── routes/
│   └── web.php
├── public/
├── storage/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

---

## 🎯 Tujuan Project

Project ini dibuat sebagai **project latihan**, bukan sebagai aplikasi perpustakaan production-ready.

Melalui project ini, kamu dapat mempraktikkan berbagai konsep penting dalam Laravel dengan membangun aplikasi nyata secara bertahap.

Materi yang dipraktikkan antara lain:

* Routing
* Blade
* Migration
* Model & Eloquent
* Eloquent Relationship
* CRUD
* Validation
* File Upload
* Pagination
* Search & Filter
* Livewire
* Reusable Components
* Tailwind CSS
* Database Seeder

Project ini merupakan companion project untuk Ebook:

> **30 Hari Belajar Laravel — CodingAsik Academy**

---

## 📖 Belajar Laravel

Ingin mempelajari proses pembuatan project ini dari awal secara bertahap?

Kunjungi:

[CodingAsik Academy](https://codingasikacademy.com?utm_source=github.com/codingasik)

---

## 💬 Butuh Bantuan?

Jika mengalami kendala saat menjalankan project atau mengikuti materi, silakan hubungi CodingAsik Academy:

* 🌐 Website: [codingasikacademy.com](https://codingasikacademy.com?utm_source=github.com/codingasik)
* 💬 WhatsApp: [WhatsApp CodingAsik Academy](https://wa.me/6285713254744?utm_source=github.com/codingasik)
* 📸 Instagram: [@codingasik di Instagram](https://instagram.com/codingasik?utm_source=github.com/codingasik)
* 🎵 TikTok: [@codingasik di TikTok](https://tiktok.com/@codingasik?utm_source=github.com/codingasik)
* ▶️ YouTube: [CodingAsik di YouTube](https://youtube.com/codingasik?utm_source=github.com/codingasik)
* 📧 Email: [codingasikacademy@gmail.com](mailto:codingasikacademy@gmail.com)

---

## ❤️ Support

Jika project ini membantu proses belajar kamu:

⭐ Berikan **Star** pada repository ini
📖 Pelajari Ebook **30 Hari Belajar Laravel**
📢 Bagikan project ini kepada teman yang sedang belajar Laravel

---

**Happy Coding! 🚀**

Made with ❤️ by **CodingAsik Academy**
