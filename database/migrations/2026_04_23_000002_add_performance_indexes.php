<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'orders_status_index');
            $table->index(['buyer_id', 'status'], 'orders_buyer_status_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['store_id', 'order_id'], 'order_items_store_order_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active', 'products_is_active_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_buyer_status_index');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_store_order_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_is_active_index');
        });
    }
};
