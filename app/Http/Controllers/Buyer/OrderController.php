<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $orders = Order::query()
            ->where('buyer_id', $request->user()->id)
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('buyer.orders.index', compact('orders', 'status'));
    }

    public function show(Request $request, Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['items.product.images', 'items.store']);

        return view('buyer.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        if (! $order->canTransitionTo(Order::STATUS_DIBATALKAN)) {
            return back()->with('error', 'Status pesanan tidak bisa dibatalkan.');
        }

        $order->update(['status' => Order::STATUS_DIBATALKAN]);

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}

