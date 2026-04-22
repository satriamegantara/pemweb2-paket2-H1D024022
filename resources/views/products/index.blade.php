<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Katalog Produk') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                        <input name="q" value="{{ $search }}"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Cari produk..." />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Kategori') }}</label>
                        <select name="category"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Semua') }}</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>
                                    {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Min Harga') }}</label>
                        <input name="min_price" inputmode="numeric" value="{{ $minPrice }}"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="0" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Max Harga') }}</label>
                        <input name="max_price" inputmode="numeric" value="{{ $maxPrice }}"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="100000" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Sort') }}</label>
                        <select name="sort"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="latest" @selected($sort === 'latest')>{{ __('Terbaru') }}</option>
                            <option value="price_asc" @selected($sort === 'price_asc')>{{ __('Harga termurah') }}</option>
                            <option value="price_desc" @selected($sort === 'price_desc')>{{ __('Harga termahal') }}
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-6 flex flex-wrap items-center gap-3">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="in_stock" value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                @checked((bool) $inStock)>
                            <span>{{ __('Hanya stok tersedia') }}</span>
                        </label>

                        <button
                            class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            {{ __('Terapkan') }}
                        </button>
                        <a href="{{ route('products.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Reset') }}
                        </a>
                        <a href="{{ route('cart.index') }}"
                            class="ms-auto inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            {{ __('Lihat Cart') }}
                        </a>
                    </div>
                </form>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                @forelse ($products as $product)
                @php($img = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        @if ($img)
                            <img src="{{ asset('storage/' . $img->image_path) }}"
                                class="h-36 w-full object-cover lg:h-32 xl:h-36" alt="Product image">
                        @else
                            <div
                                class="flex h-36 w-full items-center justify-center bg-gray-100 text-sm text-gray-500 lg:h-32 xl:h-36">
                                No image</div>
                        @endif
                    </a>
                    <div class="p-4">
                        <a href="{{ route('products.show', $product) }}"
                            class="text-sm font-semibold text-gray-900 hover:text-indigo-600 lg:text-base">
                            {{ $product->name }}
                        </a>
                        <div class="mt-1 text-xs text-gray-600 lg:text-sm">
                            {{ $product->category?->name }}
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <div class="text-sm font-semibold text-gray-900 lg:text-base">
                                Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                            </div>
                            <form method="POST" action="{{ route('cart.add', $product) }}">
                                @csrf
                                <input type="hidden" name="qty" value="1">
                                <button
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-2.5 py-1.5 text-[11px] font-semibold text-white hover:bg-indigo-500 lg:px-3 lg:py-2 lg:text-xs">
                                    {{ __('+ Cart') }}
                                </button>
                            </form>
                        </div>
                        <div class="mt-2 text-[11px] text-gray-500 lg:text-xs">
                            Stok: {{ $product->stock }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-sm text-gray-500">
                    {{ __('Tidak ada produk.') }}
                </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>