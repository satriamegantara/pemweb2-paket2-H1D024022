<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Order $order
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->order->loadMissing(['buyer', 'items.product']);

        $storeId = $notifiable->store?->id;
        $storeItems = $storeId
            ? $this->order->items->where('store_id', $storeId)->values()
            : $this->order->items;

        $items = $storeItems->take(3);
        $itemCount = $storeItems->count();
        $more = max($itemCount - 3, 0);
        $storeTotalAmount = $storeItems->sum('subtotal');

        $message = $itemCount > 0
            ? "Pesanan baru #{$this->order->order_code} dari {$this->order->buyer->name} ({$itemCount} item untuk toko kamu)"
            : "Pesanan baru #{$this->order->order_code} dari {$this->order->buyer->name}";

        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'total_amount' => $this->order->total_amount,
            'store_total_amount' => $storeTotalAmount,
            'buyer_name' => $this->order->buyer->name,
            'item_summary' => $items->map(fn($item) => "{$item->product->name} ({$item->qty}x)")->join(', '),
            'more_items' => $more > 0 ? " dan {$more} item lainnya" : '',
            'item_count' => $itemCount,
            'store_id' => $storeId,
            'message' => $message,
        ];
    }
}
