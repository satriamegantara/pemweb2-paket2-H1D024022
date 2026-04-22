<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $storeId = $request->user()->store?->id;
        if (! $storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $status = $request->query('status', 'all');

        $orderItems = \App\Models\OrderItem::query()
            ->with(['order', 'product'])
            ->where('store_id', $storeId)
            ->when($status !== 'all', fn ($q) => $q->whereHas('order', fn ($q) => $q->where('status', $status)))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('seller.orders.index', compact('orderItems', 'status'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $storeId = $request->user()->store?->id;
        if (! $storeId) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $hasItem = $order->items()->where('store_id', $storeId)->exists();
        abort_if(! $hasItem, 403);

        $next = $request->status;
        if (! $order->canTransitionTo($next)) {
            return back()->with('error', 'Transisi status tidak valid.');
        }

        $order->update(['status' => $next]);

        return back()->with('success', 'Status pesanan berhasil diupdate.');
    }
}

