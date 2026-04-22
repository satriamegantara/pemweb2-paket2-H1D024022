<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerOrderTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected Store $store;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['role' => 'penjual']);
        $this->buyer = User::factory()->create(['role' => 'pembeli']);
        $this->store = Store::factory()->create(['user_id' => $this->seller->id]);
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'stock' => 10,
            'price' => 50000,
        ]);
    }

    public function test_buyer_can_view_orders_list(): void
    {
        $order = Order::factory()->create(['buyer_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.orders.index'));

        $response->assertStatus(200);
        $response->assertSee($order->order_code);
    }

    public function test_buyer_can_view_order_detail(): void
    {
        $order = Order::factory()->create(['buyer_id' => $this->buyer->id]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'store_id' => $this->store->id,
            'qty' => 2,
            'price' => 50000,
            'subtotal' => 100000,
        ]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee($order->order_code);
        $response->assertSee('Item Pesanan');
    }

    public function test_buyer_cannot_view_other_buyer_order(): void
    {
        $otherBuyer = User::factory()->create(['role' => 'pembeli']);
        $order = Order::factory()->create(['buyer_id' => $otherBuyer->id]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.orders.show', $order));

        $response->assertForbidden();
    }

    public function test_buyer_can_cancel_pending_order(): void
    {
        $order = Order::factory()->menungguPembayaran()->create(['buyer_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)->post(route('buyer.orders.cancel', $order));

        $response->assertSessionHas('success');
        $this->assertEquals(Order::STATUS_DIBATALKAN, $order->fresh()->status);
    }

    public function test_buyer_cannot_cancel_completed_order(): void
    {
        $order = Order::factory()->selesai()->create(['buyer_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)->post(route('buyer.orders.cancel', $order));

        $response->assertSessionHas('error');
        $this->assertEquals(Order::STATUS_SELESAI, $order->fresh()->status);
    }

    public function test_buyer_cannot_cancel_other_buyer_order(): void
    {
        $otherBuyer = User::factory()->create(['role' => 'pembeli']);
        $order = Order::factory()->menungguPembayaran()->create(['buyer_id' => $otherBuyer->id]);

        $response = $this->actingAs($this->buyer)->post(route('buyer.orders.cancel', $order));

        $response->assertForbidden();
    }

    public function test_orders_list_can_be_filtered_by_status(): void
    {
        Order::factory()->menungguPembayaran()->create(['buyer_id' => $this->buyer->id]);
        Order::factory()->dibayar()->create(['buyer_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)
            ->get(route('buyer.orders.index', ['status' => 'menunggu_pembayaran']));

        $response->assertStatus(200);
        $response->assertSee('menunggu_pembayaran');
    }

    public function test_orders_are_paginated(): void
    {
        Order::factory()->count(15)->create(['buyer_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)->get(route('buyer.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('order');
    }

    public function test_guest_cannot_access_orders(): void
    {
        $response = $this->get(route('buyer.orders.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_seller_cannot_access_buyer_orders(): void
    {
        $response = $this->actingAs($this->seller)->get(route('buyer.orders.index'));

        $response->assertForbidden();
    }
}
