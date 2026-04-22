<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
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

    public function test_stock_decreases_after_successful_checkout(): void
    {
        $initialStock = $this->product->stock;

        Session::put('cart.items', [
            $this->product->id => ['qty' => 2],
        ]);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('success');

        $this->product->refresh();
        $this->assertEquals($initialStock - 2, $this->product->stock);
    }

    public function test_checkout_fails_when_stock_insufficient(): void
    {
        Session::put('cart.items', [
            $this->product->id => ['qty' => 10],
        ]);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');

        $this->product->refresh();
        $this->assertEquals(5, $this->product->stock);
    }

    public function test_checkout_fails_when_stock_zero(): void
    {
        $this->product->update(['stock' => 0]);

        Session::put('cart.items', [
            $this->product->id => ['qty' => 1],
        ]);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');
    }

    public function test_inactive_product_cannot_be_checked_out(): void
    {
        $this->product->update(['is_active' => false]);

        Session::put('cart.items', [
            $this->product->id => ['qty' => 1],
        ]);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');
    }

    public function test_checkout_requires_valid_quantity(): void
    {
        Session::put('cart.items', [
            $this->product->id => ['qty' => 0],
        ]);

        $response = $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertSessionHas('checkoutIssues');
    }

    public function test_cart_cleared_after_successful_checkout(): void
    {
        Session::put('cart.items', [
            $this->product->id => ['qty' => 1],
        ]);

        $this->actingAs($this->buyer)->post(route('checkout.store'), $this->checkoutPayload());

        $this->assertNull(Session::get('cart.items'));
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
