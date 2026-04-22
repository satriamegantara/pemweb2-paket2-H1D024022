<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellerStoreRequest;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function create(): View
    {
        $user = auth()->user();

        if ($user->store) {
            return redirect()->route('seller.store.edit');
        }

        return view('seller.store.create');
    }

    public function store(SellerStoreRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'penjual') {
            abort(403, 'Hanya penjual yang bisa membuat toko.');
        }

        if ($user->store) {
            return redirect()->route('seller.store.edit')->with('error', 'Toko sudah ada.');
        }

        Store::create([
            'user_id' => $user->id,
            'store_name' => $request->store_name,
            'description' => $request->description,
            'address' => $request->address,
        ]);

        return redirect()->route('seller.dashboard')->with('success', 'Toko berhasil dibuat.');
    }

    public function edit(): View
    {
        $store = auth()->user()->store;

        if (! $store) {
            return redirect()->route('seller.store.create');
        }

        return view('seller.store.edit', compact('store'));
    }

    public function update(SellerStoreRequest $request): RedirectResponse
    {
        $store = $request->user()->store;

        if (! $store) {
            return redirect()->route('seller.store.create')->with('error', 'Silakan buat toko dulu.');
        }

        $store->update($request->validated());

        return redirect()->route('seller.store.edit')->with('success', 'Toko berhasil diupdate.');
    }
}

