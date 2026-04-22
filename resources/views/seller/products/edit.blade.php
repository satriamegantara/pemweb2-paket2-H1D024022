<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Produk') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('seller.products.update', $product) }}" enctype="multipart/form-data">
                    @method('PUT')
                    @include('seller.products._form', ['product' => $product, 'categories' => $categories])

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('seller.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>

                @if ($product->images->count())
                    <div class="mt-8">
                        <h3 class="text-sm font-semibold text-gray-700">{{ __('Gambar saat ini') }}</h3>
                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($product->images as $img)
                                <div class="rounded-md border border-gray-200 overflow-hidden">
                                    <img src="{{ asset('storage/'.$img->image_path) }}" class="h-32 w-full object-cover" alt="Product image">
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-gray-500">{{ __('Upload ulang 3-5 gambar untuk mengganti seluruh galeri.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

