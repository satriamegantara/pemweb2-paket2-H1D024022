<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $seller = User::factory()->create(['role' => 'penjual']);
        $store = Store::factory()->create(['user_id' => $seller->id]);
        $category = Category::factory()->create();

        $this->buyer = User::factory()->create(['role' => 'pembeli']);
        $this->product = Product::factory()->create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'stock' => 10,
            'price' => 50000,
        ]);
    }

    public function test_authenticated_user_can_view_cart(): void
    {
        Cart::create(['user_id' => $this->buyer->id]);

        $response = $this->actingAs($this->buyer)->get(route('cart.index'));

        $response->assertStatus(200);
    }

    public function test_guest_can_view_empty_cart(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $response = $this->actingAs($this->buyer)->post(
            route('cart.add', $this->product),
            ['qty' => 2]
        );

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);
    }

    public function test_adding_product_creates_cart_if_not_exists(): void
    {
        $this->assertNull(Cart::where('user_id', $this->buyer->id)->first());

        $this->actingAs($this->buyer)->post(
            route('cart.add', $this->product),
            ['qty' => 1]
        );

        $this->assertNotNull(Cart::where('user_id', $this->buyer->id)->first());
    }

    public function test_adding_same_product_increases_quantity(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);

        $this->actingAs($this->buyer)->post(
            route('cart.add', $this->product),
            ['qty' => 3]
        );

        $this->assertEquals(5, CartItem::where('product_id', $this->product->id)->first()->qty);
    }

    public function test_user_can_update_cart_item_quantity(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);

        $response = $this->actingAs($this->buyer)->post(
            route('cart.update', $this->product),
            ['qty' => 5]
        );

        $response->assertSessionHas('success');
        $this->assertEquals(5, CartItem::where('product_id', $this->product->id)->first()->qty);
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);

        $response = $this->actingAs($this->buyer)->post(route('cart.remove', $this->product));

        $response->assertSessionHas('success');
        $this->assertEquals(0, CartItem::where('product_id', $this->product->id)->count());
    }

    public function test_cart_quantity_is_limited_by_stock(): void
    {
        $this->product->update(['stock' => 5]);

        $this->actingAs($this->buyer)->post(
            route('cart.add', $this->product),
            ['qty' => 10]
        );

        $cartItem = CartItem::where('product_id', $this->product->id)->first();
        $this->assertEquals(5, $cartItem->qty);
    }

    public function test_user_can_see_own_cart(): void
    {
        $cart = Cart::create(['user_id' => $this->buyer->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);

        $response = $this->actingAs($this->buyer)->get(route('cart.index'));

        $response->assertStatus(200);
    }
}
