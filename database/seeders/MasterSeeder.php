<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('seed');

        $this->seedPlaceholderImages();

        $admin = User::firstOrCreate(
            ['email' => 'admin@umkmart.test'],
            [
                'name' => 'Admin UMKMart',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '080000000000',
                'address' => 'Alamat admin (seed)',
            ]
        );

        $buyer = User::firstOrCreate(
            ['email' => 'buyer@umkmart.test'],
            [
                'name' => 'Pembeli Demo',
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'phone' => '081111111111',
                'address' => 'Alamat pembeli (seed)',
            ]
        );

        $seller1 = User::firstOrCreate(
            ['email' => 'seller1@umkmart.test'],
            [
                'name' => 'Penjual Demo 1',
                'password' => Hash::make('password'),
                'role' => 'penjual',
                'phone' => '082222222222',
                'address' => 'Alamat penjual 1 (seed)',
            ]
        );

        $seller2 = User::firstOrCreate(
            ['email' => 'seller2@umkmart.test'],
            [
                'name' => 'Penjual Demo 2',
                'password' => Hash::make('password'),
                'role' => 'penjual',
                'phone' => '083333333333',
                'address' => 'Alamat penjual 2 (seed)',
            ]
        );

        $store1 = Store::firstOrCreate(
            ['user_id' => $seller1->id],
            [
                'store_name' => 'Toko Demo 1',
                'description' => 'Deskripsi toko demo 1',
                'address' => 'Alamat toko demo 1',
            ]
        );

        $store2 = Store::firstOrCreate(
            ['user_id' => $seller2->id],
            [
                'store_name' => 'Toko Demo 2',
                'description' => 'Deskripsi toko demo 2',
                'address' => 'Alamat toko demo 2',
            ]
        );

        $categories = collect([
            ['name' => 'Makanan', 'icon' => '🍱'],
            ['name' => 'Minuman', 'icon' => '🥤'],
            ['name' => 'Kerajinan', 'icon' => '🧶'],
        ])->map(function (array $row) {
            $slugBase = Str::slug($row['name']);
            $slug = $slugBase;
            $i = 2;
            while (Category::where('slug', $slug)->exists()) {
                $slug = "{$slugBase}-{$i}";
                $i++;
            }

            return Category::firstOrCreate(['slug' => $slug], [
                'name' => $row['name'],
                'icon' => $row['icon'],
            ]);
        });

        $catFood = $categories->firstWhere('name', 'Makanan');
        $catDrink = $categories->firstWhere('name', 'Minuman');
        $catCraft = $categories->firstWhere('name', 'Kerajinan');

        $productA = $this->seedProduct($store1->id, $catFood->id, 'Keripik Singkong Pedas', 15000, 50);
        $productB = $this->seedProduct($store2->id, $catDrink->id, 'Es Teh Botol UMKM', 5000, 80);
        $productC = $this->seedProduct($store1->id, $catCraft->id, 'Tas Rajut Handmade', 75000, 15);

        $this->seedProductImages($productA->id);
        $this->seedProductImages($productB->id);
        $this->seedProductImages($productC->id);

        $this->seedSampleOrderMultiSeller($buyer->id, [$productA, $productB]);
    }

    private function seedProduct(int $storeId, int $categoryId, string $name, float $price, int $stock): Product
    {
        $slugBase = Str::slug($name);
        $slug = $slugBase;
        $i = 2;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$i}";
            $i++;
        }

        return Product::firstOrCreate(['slug' => $slug], [
            'store_id' => $storeId,
            'category_id' => $categoryId,
            'name' => $name,
            'description' => "Produk demo: {$name}",
            'price' => $price,
            'stock' => $stock,
            'weight' => 500,
            'is_active' => true,
        ]);
    }

    private function seedPlaceholderImages(): void
    {
        $placeholders = [
            'seed/product-1.svg' => '#4F46E5',
            'seed/product-2.svg' => '#16A34A',
            'seed/product-3.svg' => '#DC2626',
        ];

        foreach ($placeholders as $path => $color) {
            if (Storage::disk('public')->exists($path)) {
                continue;
            }

            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600">
  <rect width="100%" height="100%" fill="{$color}"/>
  <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#FFFFFF" font-size="32" font-family="Arial, sans-serif">
    UMKMart
  </text>
</svg>
SVG;

            Storage::disk('public')->put($path, $svg);
        }
    }

    private function seedProductImages(int $productId): void
    {
        if (ProductImage::where('product_id', $productId)->count() >= 3) {
            return;
        }

        $paths = [
            'seed/product-1.svg',
            'seed/product-2.svg',
            'seed/product-3.svg',
        ];

        foreach ($paths as $idx => $path) {
            ProductImage::create([
                'product_id' => $productId,
                'image_path' => $path,
                'is_primary' => $idx === 0,
            ]);
        }
    }

    /**
     * @param  array<int, Product>  $products
     */
    private function seedSampleOrderMultiSeller(int $buyerId, array $products): void
    {
        if (Order::query()->where('buyer_id', $buyerId)->exists()) {
            return;
        }

        Storage::disk('public')->put('seed/payment-proof.txt', "Bukti pembayaran demo\n");

        DB::transaction(function () use ($buyerId, $products) {
            $total = 0;
            $items = [];

            foreach ($products as $product) {
                $qty = 1;
                $subtotal = (float) $product->price * $qty;
                $total += $subtotal;

                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'order_code' => 'SEED-'.Str::upper(Str::random(8)),
                'buyer_id' => $buyerId,
                'total_amount' => $total,
                'shipping_address' => 'Alamat pembeli (seed)',
                'payment_proof' => 'seed/payment-proof.txt',
                'status' => Order::STATUS_DIBAYAR,
            ]);

            foreach ($items as $row) {
                /** @var Product $product */
                $product = $row['product'];
                if ($product->stock > 0) {
                    $product->decrement('stock', $row['qty']);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'store_id' => $product->store_id,
                    'qty' => $row['qty'],
                    'price' => $product->price,
                    'subtotal' => $row['subtotal'],
                ]);
            }
        });
    }
}

