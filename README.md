# Marketplace UMKM - Laravel 13

Platform marketplace untuk pelaku UMKM dengan sistem multi-seller, shopping cart berbasis session, dan workflow pesanan lengkap.

## Tech Stack

- **Laravel 13** - PHP Framework
- **Blade** - Template Engine (Tanpa Livewire)
- **Tailwind CSS** - Styling + Dark Mode
- **MySQL** - Database
- **Breeze** - Autentikasi
- **Dompdf** - Export PDF
- **Maatwebsite Excel** - Export Excel

## Prerequisites

- PHP 8.3+
- Composer 2.x
- Node.js 18+
- MySQL 8.0+

## Installation

```bash
# Clone repository
git clone <repo-url>
cd marketplace

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Buat database MySQL
# mysql -u root -p
# CREATE DATABASE umkmart;

# Update .env dengan credentials database
# DB_DATABASE=umkmart
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations & seeders
php artisan migrate:fresh --seed

# Link storage
php artisan storage:link

# Run development server
composer run dev
```

Buka `http://localhost:8000` di browser.

## Default User Accounts

| Role    | Email               | Password |
| ------- | ------------------- | -------- |
| Admin   | admin@example.com   | password |
| Penjual | seller@example.com  | password |
| Penjual | seller1@example.com | password |
| Penjual | seller2@example.com | password |
| Pembeli | buyer@example.com   | password |

## Fitur Utama

### Autentikasi Multi-Role

- **Admin** - Kelola kategori, lihat semua pesanan
- **Penjual** - Kelola toko, produk, pesanan masuk, laporan penjualan
- **Pembeli** - Browse katalog, shopping cart, checkout, lacak pesanan

### Katalog Produk

- Halaman katalog dengan search, filter kategori, sort harga
- Filter stok tersedia
- Pagination otomatis

### Shopping Cart

- Berbasis session (guest cart + user cart)
- Cart guest otomatis merge saat login
- Update quantity, hapus item
- Checkout hanya untuk role pembeli

### Checkout & Pesanan

- Input alamat pengiriman
- Upload bukti transfer
- Validasi stok dengan race condition handling (lockForUpdate)
- DB transaction untuk atomic checkout
- Workflow: Menunggu Pembayaran → Dibayar → Diproses → Dikirim → Selesai

### Multi-Seller Order Split

- Satu order bisa berisi produk dari penjual berbeda
- Notifikasi terpisah ke masing-masing penjual
- Penjual hanya melihat order_items miliknya

### Laporan Penjualan

- Filter berdasarkan tanggal
- Export PDF (DomPDF)
- Export Excel (Maatwebsite)

### Upload Multi-Gambar

- Min 3, maks 5 gambar per produk
- Tandai gambar utama
- Galeri foto di halaman detail produk

## Struktur Route

```
/ (Homepage)
├── /products (Katalog)
│   └── /products/{slug} (Detail Produk)
├── /cart (Shopping Cart)
├── /checkout (Checkout - requires auth + role:pembeli)
├── /my-orders (Pesanan Saya - requires auth + role:pembeli)
│   └── /my-orders/{id}
├── /dashboard (Dashboard - role-based)
├── /admin/* (Admin routes)
│   ├── /admin/categories/*
│   └── /admin/orders
├── /seller/* (Seller routes)
│   ├── /seller/store
│   ├── /seller/products/*
│   ├── /seller/orders
│   └── /seller/reports/sales
└── /profile (User profile)
```

## Database Schema

### Tables

- `users` - User dengan role (admin/penjual/pembeli)
- `stores` - Data toko penjual
- `categories` - Kategori produk
- `products` - Produk dengan soft deletes
- `product_images` - Galeri gambar produk
- `carts` - Cart (user_id atau guest_id)
- `cart_items` - Item dalam cart
- `orders` - Header pesanan
- `order_items` - Detail item pesanan (per penjual)
- `notifications` - Notifikasi sistem

### Relasi Utama

```
User 1:1 Store
Store 1:N Product
Category 1:N Product
Product 1:N ProductImage
Product 1:N CartItem
Cart 1:N CartItem
Order 1:N OrderItem
OrderItem N:1 Product
OrderItem N:1 Store
```

## Environment Variables

```env
APP_NAME="Marketplace UMKM"
APP_ENV=local
APP_KEY=
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=umkmart
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

## Development Commands

```bash
# Start dev server
composer run dev

# Run tests
composer run test

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Reset database
php artisan migrate:fresh --seed

# Create new seeder
php artisan make:seeder SomeSeeder
```

## Project Structure

```
app/
├── Exports/              # Excel export classes
├── Http/
│   ├── Controllers/      # All controllers
│   │   ├── Admin/
│   │   ├── Auth/
│   │   ├── Buyer/
│   │   └── Seller/
│   ├── Middleware/       # Role, Cart middleware
│   ├── Requests/         # Form request validations
│   └── Policies/         # Authorization policies
├── Models/               # Eloquent models
├── Notifications/        # Notification classes
└── Traits/               # Reusable traits

database/
├── migrations/           # Database migrations
└── seeders/             # Database seeders

resources/views/
├── admin/                # Admin views
├── auth/                 # Auth views
├── buyer/                # Buyer views
├── cart/                 # Cart views
├── checkout/             # Checkout views
├── components/           # Blade components
├── layouts/              # Layout templates
├── products/             # Product views
├── profile/              # Profile views
└── seller/               # Seller views

routes/
├── web.php               # Main routes
└── auth.php              # Auth routes (Breeze)
```

## License

MIT License
