<?php

namespace App\Providers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Policies\OrderItemPolicy;
use App\Policies\ProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
        OrderItem::class => OrderItemPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
