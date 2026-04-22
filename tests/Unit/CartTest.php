<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    public function test_cart_fillable_attributes_are_defined(): void
    {
        $cart = new Cart();
        $fillable = $cart->getFillable();

        $this->assertContains('user_id', $fillable);
        $this->assertContains('guest_id', $fillable);
    }

    public function test_cart_uses_has_factory_trait(): void
    {
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses_recursive(Cart::class));
    }

    public function test_cart_item_fillable_attributes_are_defined(): void
    {
        $cartItem = new CartItem();
        $fillable = $cartItem->getFillable();

        $this->assertContains('cart_id', $fillable);
        $this->assertContains('product_id', $fillable);
        $this->assertContains('qty', $fillable);
    }

    public function test_cart_item_casts_qty_to_integer(): void
    {
        $cartItem = new CartItem();
        $casts = $cartItem->getCasts();

        $this->assertArrayHasKey('qty', $casts);
        $this->assertEquals('integer', $casts['qty']);
    }

    public function test_cart_item_uses_has_factory_trait(): void
    {
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses_recursive(CartItem::class));
    }
}
