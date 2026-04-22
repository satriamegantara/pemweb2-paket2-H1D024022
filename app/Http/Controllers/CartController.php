<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * @return array<int, array{qty:int}>
     */
    private function getCart(Request $request): array
    {
        /** @var array<int, array{qty:int}> $cart */
        $cart = $request->session()->get('cart.items', []);

        return $cart;
    }

    /**
     * @param  array<int, array{qty:int}>  $cart
     */
    private function putCart(Request $request, array $cart): void
    {
        $request->session()->put('cart.items', $cart);
    }

    public function index(Request $request): View
    {
        $cart = $this->getCart($request);
        $ids = array_keys($cart);

        $products = $ids === []
            ? collect()
            : Product::query()
                ->with('images')
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $row) {
            $product = $products->get((int) $productId);
            if (! $product) {
                continue;
            }

            $qty = (int) $row['qty'];
            $subtotal = (float) $product->price * $qty;
            $total += $subtotal;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];
        }

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        abort_if(! $product->is_active, 404);

        $request->validate([
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $qty = (int) ($request->input('qty', 1));
        $cart = $this->getCart($request);

        $current = (int) ($cart[$product->id]['qty'] ?? 0);
        $newQty = min($current + $qty, (int) $product->stock);

        if ($product->stock <= 0) {
            return back()->with('error', 'Stok habis.');
        }

        $cart[$product->id] = ['qty' => $newQty];
        $this->putCart($request, $cart);

        return back()->with('success', 'Produk ditambahkan ke cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        $qty = (int) $request->qty;
        $cart = $this->getCart($request);

        if (! isset($cart[$product->id])) {
            return back()->with('error', 'Item tidak ditemukan di cart.');
        }

        $cart[$product->id]['qty'] = min($qty, (int) $product->stock);
        $this->putCart($request, $cart);

        return back()->with('success', 'Cart berhasil diupdate.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $this->getCart($request);
        unset($cart[$product->id]);
        $this->putCart($request, $cart);

        return back()->with('success', 'Item dihapus dari cart.');
    }
}

