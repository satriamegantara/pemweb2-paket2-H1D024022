<?php

namespace App\Http\Controllers\Admin;

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
        $q = trim((string) $request->query('q', ''));

        $orders = Order::query()
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($q !== '', fn ($query) => $query->where('order_code', 'like', "%{$q}%"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'status', 'q'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('updateStatus', $order);

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Order::allowedTransitions()))],
        ]);

        $next = $request->status;
        if (! $order->canTransitionTo($next)) {
            return back()->with('error', 'Transisi status tidak valid.');
        }

        $order->update(['status' => $next]);

        return back()->with('success', 'Status pesanan berhasil diupdate.');
    }
}

