<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // === USERS ===

        $admin1 = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'phone' => '08123456789',
            'address' => 'Jl. Admin No. 1',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $penjual2 = User::create([
            'name' => 'Penjual',
            'email' => 'seller@example.com',
            'role' => 'penjual',
            'phone' => '08987654321',
            'address' => 'Jl. Seller No. 2',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $pembeli3 = User::create([
            'name' => 'Pembeli',
            'email' => 'buyer@example.com',
            'role' => 'pembeli',
            'phone' => '08555555555',
            'address' => 'Jl. Buyer No. 3',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $penjual4 = User::create([
            'name' => 'Wisnu Bodo',
            'email' => 'seller1@example.com',
            'role' => 'penjual',
            'phone' => '',
            'address' => '',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $penjual5 = User::create([
            'name' => 'Yusuf Zawawi',
            'email' => 'seller2@example.com',
            'role' => 'penjual',
            'phone' => '',
            'address' => '',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // === STORES ===

        $store1 = Store::create([
            'user_id' => $penjual2->id,
            'store_name' => 'Robotech Planes',
            'description' => 'Toko yang berfokus pada teknologi khususnya UAV',
            'logo' => '',
            'address' => 'Surabaya',
        ]);

        $store2 = Store::create([
            'user_id' => $penjual4->id,
            'store_name' => 'Buaya AeroTech',
            'description' => 'Perusahaan yang bergerak di bidang teknologi khususnya komponen UAV',
            'logo' => '',
            'address' => 'Yogyakarta',
        ]);

        $store3 = Store::create([
            'user_id' => $penjual5->id,
            'store_name' => 'Toko Kelontong',
            'description' => '',
            'logo' => '',
            'address' => 'Jakarta',
        ]);

        // === CATEGORIES ===

        $catMakanan = Category::create([
            'name' => 'Makanan',
            'slug' => 'makanan',
            'icon' => '🍔',
        ]);

        $catMinuman = Category::create([
            'name' => 'Minuman',
            'slug' => 'minuman',
            'icon' => '🥤',
        ]);

        $catKerajinan = Category::create([
            'name' => 'Kerajinan',
            'slug' => 'kerajinan',
            'icon' => '✂️',
        ]);

        $catTeknologi = Category::create([
            'name' => 'Teknologi',
            'slug' => 'teknologi',
            'icon' => '🤖',
        ]);

        // === PRODUCTS ===

        $product1 = Product::create([
            'store_id' => $store1->id,
            'category_id' => $catTeknologi->id,
            'primary_image_id' => null,
            'name' => 'Bimark Racing Plane',
            'slug' => 'bimark-racing-plane-2',
            'description' => '',
            'price' => '10000000.00',
            'stock' => 1,
            'weight' => 3000,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product1->id,
            'image_path' => 'products/3/EG00n5VzJfJtQtGKfM9cTtvwN4U3A8Cb4frRmltc.png',
            'is_primary' => false,
        ]);
        ProductImage::create([
            'product_id' => $product1->id,
            'image_path' => 'products/3/BpBJx7U6XgMuQzGtvHfUeXhFIIlt4GW3igaiwEgK.png',
            'is_primary' => true,
        ]);
        ProductImage::create([
            'product_id' => $product1->id,
            'image_path' => 'products/3/0kQo169NfUfJqfXq5NsrwWRCCE99gpGVCV6CX0zC.png',
            'is_primary' => false,
        ]);

        $product2 = Product::create([
            'store_id' => $store2->id,
            'category_id' => $catMakanan->id,
            'primary_image_id' => null,
            'name' => 'Pegasus Racing Plane',
            'slug' => 'pegasus-racing-plane',
            'description' => '',
            'price' => '12000000.00',
            'stock' => 1,
            'weight' => 3020,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product2->id,
            'image_path' => 'products/4/iVJfJ6Zaj8UhvurPZH5P87diKoDNdSzzuURmKUT2.png',
            'is_primary' => true,
        ]);
        ProductImage::create([
            'product_id' => $product2->id,
            'image_path' => 'products/4/YiB6gsODq1mKgxXZDvEidZV7QeVfnlHLxJmr85JG.png',
            'is_primary' => false,
        ]);
        ProductImage::create([
            'product_id' => $product2->id,
            'image_path' => 'products/4/dT0TyKPAKWHAP4hsCfEUTgy6WTg4uTrhQmbzV90D.png',
            'is_primary' => false,
        ]);

        $product3 = Product::create([
            'store_id' => $store2->id,
            'category_id' => $catKerajinan->id,
            'primary_image_id' => null,
            'name' => 'Gen1 Racing Plane',
            'slug' => 'gen1-racing-plane',
            'description' => '',
            'price' => '9500000.00',
            'stock' => 1,
            'weight' => 2900,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product3->id,
            'image_path' => 'products/5/nH6JZY8w32192dEuiq83Bpd7mLW0zde8XrFHjGc7.png',
            'is_primary' => true,
        ]);
        ProductImage::create([
            'product_id' => $product3->id,
            'image_path' => 'products/5/9vQlfKTK1SMm5sQGClwy2hb55CTin9JJFyAxWq7T.png',
            'is_primary' => false,
        ]);
        ProductImage::create([
            'product_id' => $product3->id,
            'image_path' => 'products/5/vZJqGLKKm1WTeYC8sdqIhAeCA8OnSQsrOI6pBHvq.png',
            'is_primary' => false,
        ]);

        $product4 = Product::create([
            'store_id' => $store3->id,
            'category_id' => $catKerajinan->id,
            'primary_image_id' => null,
            'name' => 'Gelang Karet',
            'slug' => 'gelang-karet',
            'description' => '',
            'price' => '10000.00',
            'stock' => 0,
            'weight' => 300,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product4->id,
            'image_path' => 'products/6/MjiNu26FKyY8gdj42rBk0rBo6kYXFbnW70SC3a4j.png',
            'is_primary' => true,
        ]);
        ProductImage::create([
            'product_id' => $product4->id,
            'image_path' => 'products/6/I2Wzd8Nm72tlHAlnAPg8YgK8fZCTRy3iyykYXL06.png',
            'is_primary' => false,
        ]);
        ProductImage::create([
            'product_id' => $product4->id,
            'image_path' => 'products/6/sxaP0TS7QkdH7qBRF5cfL7OGc32Qtbyse3AstF7h.png',
            'is_primary' => false,
        ]);

    }
}