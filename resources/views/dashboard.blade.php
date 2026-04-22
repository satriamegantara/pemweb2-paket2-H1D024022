<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
                {{ __('Buyer Dashboard') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.index') }}" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    {{ __('Belanja') }}
                </a>
                <a href="{{ route('buyer.orders.index') }}" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-primary dark:hover:bg-primary-600">
                    {{ __('Pesanan Saya') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Cart (produk)') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $cartDistinct }}</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Total qty: {{ $cartQty }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Total Pesanan') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $ordersTotal }}</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Semua status') }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Menunggu Pembayaran') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $ordersWaiting }}</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Cek bukti/konfirmasi') }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('Dikirim') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $ordersShipped }}</div>
                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Dalam pengiriman') }}</div>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Ringkasan Status Pesanan') }}</h3>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-md bg-amber-50 p-4 dark:bg-amber-900/20">
                        <div class="text-xs text-amber-700 dark:text-amber-400">{{ __('menunggu_pembayaran') }}</div>
                        <div class="mt-1 text-xl font-bold text-amber-900 dark:text-amber-300">{{ $ordersWaiting }}</div>
                    </div>
                    <div class="rounded-md bg-blue-50 p-4 dark:bg-blue-900/20">
                        <div class="text-xs text-blue-700 dark:text-blue-400">{{ __('dibayar') }}</div>
                        <div class="mt-1 text-xl font-bold text-blue-900 dark:text-blue-300">{{ $ordersPaid }}</div>
                    </div>
                    <div class="rounded-md bg-indigo-50 p-4 dark:bg-indigo-900/20">
                        <div class="text-xs text-indigo-700 dark:text-indigo-400">{{ __('diproses') }}</div>
                        <div class="mt-1 text-xl font-bold text-indigo-900 dark:text-indigo-300">{{ $ordersProcessed }}</div>
                    </div>
                    <div class="rounded-md bg-purple-50 p-4 dark:bg-purple-900/20">
                        <div class="text-xs text-purple-700 dark:text-purple-400">{{ __('dikirim') }}</div>
                        <div class="mt-1 text-xl font-bold text-purple-900 dark:text-purple-300">{{ $ordersShipped }}</div>
                    </div>
                    <div class="rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                        <div class="text-xs text-green-700 dark:text-green-400">{{ __('selesai') }}</div>
                        <div class="mt-1 text-xl font-bold text-green-900 dark:text-green-300">{{ $ordersDone }}</div>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4 dark:bg-gray-700">
                        <div class="text-xs text-gray-700 dark:text-gray-300">{{ __('dibatalkan') }}</div>
                        <div class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100">{{ $ordersCancelled }}</div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        {{ __('Buka Cart') }}
                    </a>
                    <a href="{{ route('checkout.create') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-primary dark:hover:bg-primary-600">
                        {{ __('Checkout') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
