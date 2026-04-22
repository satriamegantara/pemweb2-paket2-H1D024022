<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_code' => 'UMK-' . now()->format('Ymd') . '-' . strtoupper(fake()->unique()->bothify('????????')),
            'buyer_id' => User::factory()->state(['role' => 'pembeli']),
            'total_amount' => fake()->numberBetween(50000, 500000),
            'shipping_address' => fake()->address(),
            'payment_proof' => fake()->optional()->imageUrl(),
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ];
    }

    public function menungguPembayaran(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);
    }

    public function dibayar(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_DIBAYAR,
        ]);
    }

    public function diproses(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_DIPROSES,
        ]);
    }

    public function dikirim(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_DIKIRIM,
        ]);
    }

    public function selesai(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_SELESAI,
        ]);
    }

    public function dibatalkan(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => Order::STATUS_DIBATALKAN,
        ]);
    }
}
