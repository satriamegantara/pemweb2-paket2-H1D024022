<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Produk') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('seller.products.update', $product) }}"
                    enctype="multipart/form-data">
                    @method('PUT')
                    @include('seller.products._form', ['product' => $product, 'categories' => $categories])

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('seller.products.index') }}"
                            class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            @if ($product->images->count())
                <div class="mt-6 bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-700">{{ __('Gambar Produk') }}</h3>
                    <div class="mt-3 flex flex-wrap gap-4">
                        @foreach ($product->images as $img)
                            <div
                                class="group relative h-24 w-36 overflow-hidden rounded-md border border-gray-200 transition sm:h-28 sm:w-40 lg:h-32 lg:w-48 {{ $img->is_primary ? 'ring-2 ring-blue-500' : 'hover:ring-2 hover:ring-gray-300' }}">
                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                    class="h-full w-full object-contain bg-gray-50 p-2 lg:p-3" alt="Product image">
                                @if ($img->is_primary)
                                    <span
                                        class="absolute left-1 top-1 rounded bg-blue-500 px-1.5 py-0.5 text-[10px] leading-none text-white">Utama</span>
                                @endif
                                @if (!$img->is_primary)
                                    <form action="{{ route('seller.products.setPrimaryImage', [$product, $img]) }}" method="POST"
                                        class="absolute inset-0">
                                        @csrf
                                        <button type="submit" class="absolute inset-0 h-full w-full cursor-pointer bg-transparent"
                                            aria-label="Jadikan gambar ini sebagai gambar utama" title="Jadikan gambar utama">
                                            <span class="sr-only">Jadikan gambar utama</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ __('Klik gambar untuk menjadikannya gambar utama produk.') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>