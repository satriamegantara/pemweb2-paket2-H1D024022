<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $role = $request->user()->role;

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'penjual') {
            return redirect()->route('seller.dashboard');
        }

        // buyer dashboard
        $buyerId = $request->user()->id;

        $ordersTotal = Order::where('buyer_id', $buyerId)->count();
        $ordersWaiting = Order::where('buyer_id', $buyerId)->where('status', Order::STATUS_MENUNGGU_PEMBAYARAN)->count();
        $ordersPaid = Order::where('buyer_id', $buyerId)->where('status', Order::STATUS_DIBAYAR)->count();
        $ordersProcessed = Order::where('buyer_id', $buyerId)->where('status', Order::STATUS_DIPROSES)->count();
        $ordersShipped = Order::where('buyer_id', $buyerId)->where('status', Order::STATUS_DIKIRIM)->count();
        $ordersDone = Order::where('buyer_id', $buyerId)->where('status', Order::STATUS_SELESAI)->count();
        $ordersCancelled = Order::where('buyer_id', $buyerId)->where('status', Order::STATUS_DIBATALKAN)->count();

        /** @var array<int, array{qty:int}> $cart */
        $cart = $request->session()->get('cart.items', []);
        $cartDistinct = count($cart);
        $cartQty = array_sum(array_map(fn ($row) => (int) ($row['qty'] ?? 0), $cart));

        return view('dashboard', compact(
            'ordersTotal',
            'ordersWaiting',
            'ordersPaid',
            'ordersProcessed',
            'ordersShipped',
            'ordersDone',
            'ordersCancelled',
            'cartDistinct',
            'cartQty',
        ));
    }
}

