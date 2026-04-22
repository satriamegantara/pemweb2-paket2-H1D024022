<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckoutStockException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function create(): View
    {
        $user = auth()->user();

        return view('checkout.create', [
            'defaultAddress' => old('shipping_address', $user->address ?? ''),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        /** @var Cart $cart */
        $cart = $request->get('cart');

        if ($cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Cart kosong.');
        }

        $buyerId = $request->user()->id;

        $cart->load('items.product');

        try {
            DB::transaction(function () use ($request, $buyerId, $cart) {
                $productIds = $cart->items->pluck('product_id')->toArray();

                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $itemsPayload = [];
                $total = 0;
                $checkoutIssues = [];

                foreach ($cart->items as $item) {
                    $product = $products->get($item->product_id);
                    if (!$product || !$product->is_active) {
                        $checkoutIssues[] = [
                            'product' => $product ? $product->name : 'Produk #' . $item->product_id,
                            'message' => 'Produk sudah tidak tersedia atau nonaktif.',
                        ];

                        continue;
                    }

                    $qty = $item->qty;
                    if ($qty < 1) {
                        $checkoutIssues[] = [
                            'product' => $product->name,
                            'message' => 'Jumlah pesanan tidak valid.',
                        ];

                        continue;
                    }

                    if ($product->stock < $qty) {
                        $checkoutIssues[] = [
                            'product' => $product->name,
                            'message' => 'Stok tersedia hanya ' . $product->stock . '.',
                            'requested' => $qty,
                            'available' => $product->stock,
                        ];

                        continue;
                    }

                    $subtotal = (float) $product->price * $qty;
                    $total += $subtotal;

                    $itemsPayload[] = [
                        'product' => $product,
                        'qty' => $qty,
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                        'store_id' => $product->store_id,
                    ];
                }

                if ($checkoutIssues !== []) {
                    throw new CheckoutStockException($checkoutIssues);
                }

                $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

                $orderCode = $this->generateOrderCode();
                while (Order::where('order_code', $orderCode)->exists()) {
                    $orderCode = $this->generateOrderCode();
                }

                $order = Order::create([
                    'order_code' => $orderCode,
                    'buyer_id' => $buyerId,
                    'total_amount' => $total,
                    'shipping_address' => $request->shipping_address,
                    'payment_proof' => $proofPath,
                    'status' => 'menunggu_pembayaran',
                ]);

                foreach ($itemsPayload as $row) {
                    /** @var Product $product */
                    $product = $row['product'];

                    $product->decrement('stock', $row['qty']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'store_id' => $row['store_id'],
                        'qty' => $row['qty'],
                        'price' => $row['price'],
                        'subtotal' => $row['subtotal'],
                    ]);
                }

                $this->notifySellers($order);

                $cart->clear();
            }, 3);
        } catch (CheckoutStockException $e) {
            return redirect()
                ->route('checkout.create')
                ->withInput()
                ->with('checkoutIssues', $e->issues());
        } catch (Throwable $e) {
            return redirect()
                ->route('checkout.create')
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Checkout berhasil. Pesanan dibuat.');
    }

    private function generateOrderCode(): string
    {
        return 'UMK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8));
    }

    private function notifySellers(Order $order): void
    {
        $sellerUsers = User::whereHas('store', fn($q) => $q->whereIn('id', $order->items->pluck('store_id')))
            ->with('store')
            ->get();

        foreach ($sellerUsers as $seller) {
            $seller->notify(new NewOrderNotification($order));
        }
    }
}
