<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->numberBetween(10000, 500000);
        $qty = fake()->numberBetween(1, 5);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'store_id' => Store::factory(),
            'qty' => $qty,
            'price' => $price,
            'subtotal' => $price * $qty,
        ];
    }
}
