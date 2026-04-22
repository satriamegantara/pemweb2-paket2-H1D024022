<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Policies\OrderPolicy;
use PHPUnit\Framework\TestCase;
use Mockery;

class OrderPolicyTest extends TestCase
{
    protected OrderPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new OrderPolicy();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_buyer_can_view_own_order(): void
    {
        $buyer = Mockery::mock(User::class);
        $buyer->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('buyer_id')->andReturn(1);

        $this->assertTrue($this->policy->view($buyer, $order));
    }

    public function test_buyer_cannot_view_other_buyer_order(): void
    {
        $buyer = Mockery::mock(User::class);
        $buyer->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('buyer_id')->andReturn(2);

        $this->assertFalse($this->policy->view($buyer, $order));
    }

    public function test_buyer_can_cancel_own_order(): void
    {
        $buyer = Mockery::mock(User::class);
        $buyer->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('buyer_id')->andReturn(1);

        $this->assertTrue($this->policy->cancel($buyer, $order));
    }

    public function test_buyer_cannot_cancel_other_buyer_order(): void
    {
        $buyer = Mockery::mock(User::class);
        $buyer->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('buyer_id')->andReturn(2);

        $this->assertFalse($this->policy->cancel($buyer, $order));
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = Mockery::mock(User::class);
        $admin->shouldReceive('getAttribute')->with('role')->andReturn('admin');
        $admin->shouldReceive('getAttribute')->with('store')->andReturn(null);

        $orderItem = Mockery::mock(OrderItem::class);
        $orderItem->shouldReceive('getAttribute')->with('store_id')->andReturn(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('items')->andReturn(collect([$orderItem]));

        $this->assertTrue($this->policy->updateStatus($admin, $order));
    }

    public function test_seller_can_update_order_status_for_own_store(): void
    {
        $sellerStore = Mockery::mock(Store::class);
        $sellerStore->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $seller = Mockery::mock(User::class);
        $seller->shouldReceive('getAttribute')->with('role')->andReturn('penjual');
        $seller->shouldReceive('getAttribute')->with('store')->andReturn($sellerStore);

        $orderItem = Mockery::mock(OrderItem::class);
        $orderItem->shouldReceive('getAttribute')->with('store_id')->andReturn(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('items')->andReturn(collect([$orderItem]));

        $this->assertTrue($this->policy->updateStatus($seller, $order));
    }

    public function test_seller_cannot_update_order_status_for_other_store(): void
    {
        $sellerStore = Mockery::mock(Store::class);
        $sellerStore->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $seller = Mockery::mock(User::class);
        $seller->shouldReceive('getAttribute')->with('role')->andReturn('penjual');
        $seller->shouldReceive('getAttribute')->with('store')->andReturn($sellerStore);

        $orderItem = Mockery::mock(OrderItem::class);
        $orderItem->shouldReceive('getAttribute')->with('store_id')->andReturn(2);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('items')->andReturn(collect([$orderItem]));

        $this->assertFalse($this->policy->updateStatus($seller, $order));
    }

    public function test_buyer_cannot_update_order_status(): void
    {
        $buyer = Mockery::mock(User::class);
        $buyer->shouldReceive('getAttribute')->with('role')->andReturn('pembeli');
        $buyer->shouldReceive('getAttribute')->with('store')->andReturn(null);

        $orderItem = Mockery::mock(OrderItem::class);
        $orderItem->shouldReceive('getAttribute')->with('store_id')->andReturn(1);

        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('items')->andReturn(collect([$orderItem]));

        $this->assertFalse($this->policy->updateStatus($buyer, $order));
    }
}
