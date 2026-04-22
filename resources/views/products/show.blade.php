<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
            <a href="{{ route('cart.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                {{ __('Cart') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div>
                        @php($primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                        @if ($primary)
                            <img src="{{ asset('storage/'.$primary->image_path) }}" class="w-full aspect-square object-cover rounded-lg" alt="Primary image">
                        @else
                            <div class="w-full aspect-square bg-gray-100 flex items-center justify-center text-sm text-gray-500 rounded-lg">No image</div>
                        @endif

                        @if ($product->images->count() > 1)
                            <div class="mt-4 grid grid-cols-4 gap-2">
                                @foreach ($product->images as $img)
                                    <a href="{{ asset('storage/'.$img->image_path) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/'.$img->image_path) }}" class="h-20 w-full object-cover rounded-md border border-gray-200" alt="Gallery image">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="text-sm text-gray-600">{{ $product->category?->name }} • {{ $product->store?->store_name }}</div>
                        <div class="mt-2 text-2xl font-bold text-gray-900">
                            Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            Stok: {{ $product->stock }}
                        </div>

                        <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-6 flex items-end gap-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Qty') }}</label>
                                <input type="number" name="qty" min="1" value="1" class="mt-1 w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            </div>
                            <button class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                {{ __('Tambah ke Cart') }}
                            </button>
                        </form>

                        @if ($product->description)
                            <div class="mt-8">
                                <h3 class="text-sm font-semibold text-gray-700">{{ __('Deskripsi') }}</h3>
                                <p class="mt-2 text-sm text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

