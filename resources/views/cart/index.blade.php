<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-200">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 dark:bg-gray-800">
                @if (count($items) === 0)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Cart masih kosong.') }}</p>
                    <a href="{{ route('products.index') }}" class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        {{ __('Belanja sekarang') }}
                    </a>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Produk</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Harga</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Qty</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Subtotal</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($items as $row)
                                    @php($product = $row['product'])
                                    @php($img = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                                    <tr>
                                        <td class="px-3 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            <div class="flex items-center gap-3">
                                                @if ($img)
                                                    <img src="{{ asset('storage/'.$img->image_path) }}" class="h-12 w-12 rounded-md object-cover border border-gray-200 dark:border-gray-600" alt="Product image">
                                                @endif
                                                <div>
                                                    <div class="font-medium">{{ $product->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-500">Stok: {{ $product->stock }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            <form method="POST" action="{{ route('cart.update', $product) }}" class="flex items-center gap-2">
                                                @csrf
                                                <input type="number" name="qty" min="1" value="{{ $row['qty'] }}" class="w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                                                <button class="rounded-md bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-700 dark:bg-primary dark:hover:bg-primary-600">
                                                    {{ __('Update') }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-900 font-semibold dark:text-gray-100">
                                            Rp {{ number_format((float) $row['subtotal'], 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-3 text-sm text-right">
                                            <form method="POST" action="{{ route('cart.remove', $product) }}">
                                                @csrf
                                                <button class="text-red-600 hover:text-red-900 font-medium dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Hapus item ini?')">
                                                    {{ __('Hapus') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('Lanjut belanja') }}
                        </a>
                        <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            Total: Rp {{ number_format((float) $total, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        @auth
                            @if (auth()->user()->role === 'pembeli')
                                <a href="{{ route('checkout.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                    {{ __('Checkout') }}
                                </a>
                            @else
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Checkout hanya untuk pembeli.') }}</span>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                {{ __('Login untuk Checkout') }}
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
