<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function test_product_fillable_attributes_are_defined(): void
    {
        $product = new Product();
        $fillable = $product->getFillable();

        $this->assertContains('store_id', $fillable);
        $this->assertContains('category_id', $fillable);
        $this->assertContains('primary_image_id', $fillable);
        $this->assertContains('name', $fillable);
        $this->assertContains('slug', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('price', $fillable);
        $this->assertContains('stock', $fillable);
        $this->assertContains('weight', $fillable);
        $this->assertContains('is_active', $fillable);
    }

    public function test_product_casts_is_active_to_boolean(): void
    {
        $product = new Product(['is_active' => 1]);
        $casts = $product->getCasts();

        $this->assertArrayHasKey('is_active', $casts);
        $this->assertEquals('boolean', $casts['is_active']);
    }

    public function test_product_casts_price_to_decimal(): void
    {
        $product = new Product();
        $casts = $product->getCasts();

        $this->assertArrayHasKey('price', $casts);
        $this->assertEquals('decimal:2', $casts['price']);
    }

    public function test_product_uses_soft_deletes(): void
    {
        $product = new Product();

        $this->assertContains('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($product));
    }

    public function test_product_uses_has_factory_trait(): void
    {
        $this->assertContains('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses_recursive(Product::class));
    }
}
