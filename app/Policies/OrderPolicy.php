<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id;
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->role === 'admin' || $user->store?->id === $order->items->first()?->store_id;
    }
}
