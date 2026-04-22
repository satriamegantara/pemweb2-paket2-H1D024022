<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $storeId = $request->user()->store?->id;

        $hasStore = (bool) $storeId;
        $productsTotal = 0;
        $productsActive = 0;
        $productsInactive = 0;

        $itemsTotal = 0;
        $itemsWaiting = 0;
        $itemsPaid = 0;
        $itemsProcessed = 0;
        $itemsShipped = 0;
        $itemsDone = 0;
        $itemsCancelled = 0;

        if ($storeId) {
            $productsTotal = Product::where('store_id', $storeId)->count();
            $productsActive = Product::where('store_id', $storeId)->where('is_active', true)->count();
            $productsInactive = Product::where('store_id', $storeId)->where('is_active', false)->count();

            $itemsTotal = OrderItem::where('store_id', $storeId)->count();
            $itemsWaiting = OrderItem::where('store_id', $storeId)->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_MENUNGGU_PEMBAYARAN))->count();
            $itemsPaid = OrderItem::where('store_id', $storeId)->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_DIBAYAR))->count();
            $itemsProcessed = OrderItem::where('store_id', $storeId)->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_DIPROSES))->count();
            $itemsShipped = OrderItem::where('store_id', $storeId)->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_DIKIRIM))->count();
            $itemsDone = OrderItem::where('store_id', $storeId)->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_SELESAI))->count();
            $itemsCancelled = OrderItem::where('store_id', $storeId)->whereHas('order', fn ($q) => $q->where('status', Order::STATUS_DIBATALKAN))->count();
        }

        return view('seller.dashboard', compact(
            'hasStore',
            'productsTotal',
            'productsActive',
            'productsInactive',
            'itemsTotal',
            'itemsWaiting',
            'itemsPaid',
            'itemsProcessed',
            'itemsShipped',
            'itemsDone',
            'itemsCancelled',
        ));
    }
}

