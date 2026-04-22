<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalSellers = User::where('role', 'penjual')->count();
        $totalBuyers = User::where('role', 'pembeli')->count();

        $totalCategories = Category::count();
        $totalProducts = Product::count();

        $ordersTotal = Order::count();
        $ordersWaiting = Order::where('status', Order::STATUS_MENUNGGU_PEMBAYARAN)->count();
        $ordersPaid = Order::where('status', Order::STATUS_DIBAYAR)->count();
        $ordersProcessed = Order::where('status', Order::STATUS_DIPROSES)->count();
        $ordersShipped = Order::where('status', Order::STATUS_DIKIRIM)->count();
        $ordersDone = Order::where('status', Order::STATUS_SELESAI)->count();
        $ordersCancelled = Order::where('status', Order::STATUS_DIBATALKAN)->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmins',
            'totalSellers',
            'totalBuyers',
            'totalCategories',
            'totalProducts',
            'ordersTotal',
            'ordersWaiting',
            'ordersPaid',
            'ordersProcessed',
            'ordersShipped',
            'ordersDone',
            'ordersCancelled',
        ));
    }
}

