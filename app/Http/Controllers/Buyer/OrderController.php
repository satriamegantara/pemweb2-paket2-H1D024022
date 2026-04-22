<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_if($order->buyer_id !== $request->user()->id, 403);

        if (! $order->canTransitionTo(Order::STATUS_DIBATALKAN)) {
            return back()->with('error', 'Status pesanan tidak bisa dibatalkan.');
        }

        $order->update(['status' => Order::STATUS_DIBATALKAN]);

        return back()->with('success', 'Pesanan dibatalkan.');
    }
}

