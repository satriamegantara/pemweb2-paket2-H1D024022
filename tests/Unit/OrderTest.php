<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function test_status_constants_are_defined(): void
    {
        $this->assertEquals('menunggu_pembayaran', Order::STATUS_MENUNGGU_PEMBAYARAN);
        $this->assertEquals('dibayar', Order::STATUS_DIBAYAR);
        $this->assertEquals('diproses', Order::STATUS_DIPROSES);
        $this->assertEquals('dikirim', Order::STATUS_DIKIRIM);
        $this->assertEquals('selesai', Order::STATUS_SELESAI);
        $this->assertEquals('dibatalkan', Order::STATUS_DIBATALKAN);
    }

    public function test_allowed_transitions_returns_correct_structure(): void
    {
        $transitions = Order::allowedTransitions();

        $this->assertArrayHasKey('menunggu_pembayaran', $transitions);
        $this->assertArrayHasKey('dibayar', $transitions);
        $this->assertArrayHasKey('diproses', $transitions);
        $this->assertArrayHasKey('dikirim', $transitions);
        $this->assertArrayHasKey('selesai', $transitions);
        $this->assertArrayHasKey('dibatalkan', $transitions);
    }

    public function test_menunggu_pembayaran_can_transition_to_dibayar(): void
    {
        $order = new Order(['status' => Order::STATUS_MENUNGGU_PEMBAYARAN]);
        $this->assertTrue($order->canTransitionTo(Order::STATUS_DIBAYAR));
    }

    public function test_menunggu_pembayaran_can_transition_to_dibatalkan(): void
    {
        $order = new Order(['status' => Order::STATUS_MENUNGGU_PEMBAYARAN]);
        $this->assertTrue($order->canTransitionTo(Order::STATUS_DIBATALKAN));
    }

    public function test_menunggu_pembayaran_cannot_transition_to_diproses(): void
    {
        $order = new Order(['status' => Order::STATUS_MENUNGGU_PEMBAYARAN]);
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIPROSES));
    }

    public function test_dibayar_can_transition_to_diproses(): void
    {
        $order = new Order(['status' => Order::STATUS_DIBAYAR]);
        $this->assertTrue($order->canTransitionTo(Order::STATUS_DIPROSES));
    }

    public function test_dibayar_can_transition_to_dibatalkan(): void
    {
        $order = new Order(['status' => Order::STATUS_DIBAYAR]);
        $this->assertTrue($order->canTransitionTo(Order::STATUS_DIBATALKAN));
    }

    public function test_dibayar_cannot_transition_to_dikirim(): void
    {
        $order = new Order(['status' => Order::STATUS_DIBAYAR]);
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIKIRIM));
    }

    public function test_diproses_can_transition_to_dikirim(): void
    {
        $order = new Order(['status' => Order::STATUS_DIPROSES]);
        $this->assertTrue($order->canTransitionTo(Order::STATUS_DIKIRIM));
    }

    public function test_diproses_cannot_transition_to_selesai(): void
    {
        $order = new Order(['status' => Order::STATUS_DIPROSES]);
        $this->assertFalse($order->canTransitionTo(Order::STATUS_SELESAI));
    }

    public function test_dikirim_can_transition_to_selesai(): void
    {
        $order = new Order(['status' => Order::STATUS_DIKIRIM]);
        $this->assertTrue($order->canTransitionTo(Order::STATUS_SELESAI));
    }

    public function test_selesai_cannot_transition_to_any_status(): void
    {
        $order = new Order(['status' => Order::STATUS_SELESAI]);

        $this->assertFalse($order->canTransitionTo(Order::STATUS_MENUNGGU_PEMBAYARAN));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIBAYAR));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIPROSES));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIKIRIM));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIBATALKAN));
    }

    public function test_dibatalkan_cannot_transition_to_any_status(): void
    {
        $order = new Order(['status' => Order::STATUS_DIBATALKAN]);

        $this->assertFalse($order->canTransitionTo(Order::STATUS_MENUNGGU_PEMBAYARAN));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIBAYAR));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIPROSES));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_DIKIRIM));
        $this->assertFalse($order->canTransitionTo(Order::STATUS_SELESAI));
    }

    public function test_can_transition_to_invalid_status_returns_false(): void
    {
        $order = new Order(['status' => Order::STATUS_MENUNGGU_PEMBAYARAN]);
        $this->assertFalse($order->canTransitionTo('invalid_status'));
    }

    public function test_fillable_attributes_are_defined(): void
    {
        $order = new Order();
        $fillable = $order->getFillable();

        $this->assertContains('order_code', $fillable);
        $this->assertContains('buyer_id', $fillable);
        $this->assertContains('total_amount', $fillable);
        $this->assertContains('shipping_address', $fillable);
        $this->assertContains('payment_proof', $fillable);
        $this->assertContains('status', $fillable);
    }
}
