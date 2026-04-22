<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutStockTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;
    protected Store $store;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->seller = User::factory()->create(['role' => 'penjual']);
        $this->buyer = User::factory()->create(['role' => 'pembeli']);
        $this->store = Store::factory()->create(['user_id' => $this->seller->id]);
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'stock' => 5,
            'price' => 50000,
        ]);
    }

    private function createCartForBuyer(): Cart
    {
        return Cart::create(['user_id' => $this->buyer->id]);
    }

    private function addToCart(Product $product, int $qty): void
    {
        $cart = Cart::where('user_id', $this->buyer->id)->first();
        $cart->addItem($product->id, $qty);
    }

    public function test_stock_decreases_after_successful_checkout(): void
    {
        $initialStock = $this->product->stock;
        $cart = $this->createCartForBuyer();
        $this->addToCart($this->product, 2);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('success');

        $this->product->refresh();
        $this->assertEquals($initialStock - 2, $this->product->stock);
    }

    public function test_checkout_fails_when_stock_insufficient(): void
    {
        $cart = $this->createCartForBuyer();
        // Directly create cart item bypassing stock check (same as old behavior)
        $cart->items()->create([
            'product_id' => $this->product->id,
            'qty' => 10,
        ]);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');

        $this->product->refresh();
        $this->assertEquals(5, $this->product->stock);
    }

    public function test_checkout_fails_when_stock_zero(): void
    {
        $this->product->update(['stock' => 0]);

        $cart = $this->createCartForBuyer();
        $this->addToCart($this->product, 1);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');
    }

    public function test_inactive_product_cannot_be_checked_out(): void
    {
        $this->product->update(['is_active' => false]);

        $cart = $this->createCartForBuyer();
        $this->addToCart($this->product, 1);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');
    }

    public function test_checkout_requires_valid_quantity(): void
    {
        $cart = $this->createCartForBuyer();
        $cart->addItem($this->product->id, 0);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');
    }

    public function test_cart_cleared_after_successful_checkout(): void
    {
        $cart = $this->createCartForBuyer();
        $this->addToCart($this->product, 1);

        $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $cart->refresh();
        $this->assertEquals(0, $cart->items()->count());
    }

    public function test_checkout_requires_authentication(): void
    {
        $response = $this->post(route('checkout.store'), [
            'shipping_address' => 'Jl. Test No. 1',
        ]);

        $response->assertRedirect(route('login'));
    }

    private function checkoutPayload(): array
    {
        return [
            'shipping_address' => 'Jl. Test No. 1',
            'payment_method' => 'transfer_bank',
            'payment_proof' => UploadedFile::fake()->create('payment-proof.pdf', 100, 'application/pdf'),
        ];
    }
}
