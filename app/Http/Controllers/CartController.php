<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Cart $cart */
        $cart = $request->get('cart');

        $cart->load('items.product.images');

        $items = [];
        $total = 0;

        foreach ($cart->items as $item) {
            $product = $item->product;
            $subtotal = (float) $product->price * $item->qty;
            $total += $subtotal;

            $items[] = [
                'product' => $product,
                'qty' => $item->qty,
                'subtotal' => $subtotal,
            ];
        }

        return view('cart.index', compact('items', 'total'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        abort_if(! $product->is_active || $product->stock <= 0, 404, 'Produk tidak tersedia.');

        $request->validate([
            'qty' => ['nullable', 'integer', 'min:1'],
        ]);

        /** @var Cart $cart */
        $cart = $request->get('cart');

        $qty = (int) ($request->input('qty', 1));

        if ($product->stock <= 0) {
            return back()->with('error', 'Stok habis.');
        }

        $cart->addItem($product->id, $qty);

        return back()->with('success', 'Produk ditambahkan ke cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        /** @var Cart $cart */
        $cart = $request->get('cart');

        if (!$cart->items()->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'Item tidak ditemukan di cart.');
        }

        $qty = (int) $request->qty;

        $cart->updateItem($product->id, $qty);

        return back()->with('success', 'Cart berhasil diupdate.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        /** @var Cart $cart */
        $cart = $request->get('cart');

        $cart->removeItem($product->id);

        return back()->with('success', 'Item dihapus dari cart.');
    }
}
