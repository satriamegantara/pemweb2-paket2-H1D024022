<x-app-layout>
    <div class="min-h-screen bg-white">
        <!-- Hero Section with Search -->
        <div class="bg-primary">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="text-center">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
                        Temukan Produk Berkualitas
                    </h1>
                    <p class="text-lg sm:text-xl text-white/80 mb-8 max-w-2xl mx-auto">
                        Jelajahi ribuan produk dari toko-toko terpercaya di UMKMart
                    </p>

                    <!-- Search Bar -->
                    <form action="{{ route('products.index') }}" method="GET" class="max-w-2xl mx-auto">
                        <div class="relative">
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Cari produk..."
                                class="w-full px-6 py-4 pl-12 rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white/30 shadow-lg"
                            >
                            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-6 py-2 rounded-full hover:bg-gray-800 transition">
                                Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Categories Quick Links -->
        <div class="border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-full text-sm font-medium {{ !request('category') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua
                    </a>
                    @foreach($categories->take(6) as $category)
                        <a href="{{ route('products.index', ['category' => $category->id]) }}"
                           class="px-4 py-2 rounded-full text-sm font-medium {{ request('category') == $category->id ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Product Catalog -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Katalog Produk</h2>
                <a href="{{ route('products.index') }}" class="text-sm font-medium text-primary hover:text-primary-600">
                    Lihat semua
                </a>
            </div>

            @if($products->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                    @foreach($products as $product)
                        <a href="{{ route('products.show', $product) }}" class="group block">
                            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg hover:border-primary/20 transition-all duration-200">
                                <!-- Product Image -->
                                <div class="aspect-square bg-gray-100 overflow-hidden">
                                    @php
                                        $image = $product->primaryImage() ?? $product->images->first();
                                    @endphp
                                    @if($image)
                                        <img src="{{ asset('storage/'.$image->image_path) }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             alt="{{ $product->name }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="p-3 sm:p-4">
                                    <h3 class="text-sm sm:text-base font-medium text-gray-900 line-clamp-2 group-hover:text-primary transition">
                                        {{ $product->name }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                        {{ $product->store->store_name ?? 'Toko' }}
                                    </p>
                                    <div class="mt-2 flex items-baseline justify-between">
                                        <span class="text-sm sm:text-base font-bold text-primary">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                        @if($product->stock < 5 && $product->stock > 0)
                                            <span class="text-xs text-orange-600">Sisa {{ $product->stock }}</span>
                                        @elseif($product->stock == 0)
                                            <span class="text-xs text-red-600">Habis</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-10 flex justify-center">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada produk</h3>
                    <p class="mt-2 text-sm text-gray-500">Mulai tambahkan produk untuk ditampilkan di katalog.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
