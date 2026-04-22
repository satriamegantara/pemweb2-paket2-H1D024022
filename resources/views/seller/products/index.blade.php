<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Produk Saya') }}
            </h2>

            <a href="{{ route('seller.products.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                {{ __('Tambah Produk') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                        <input name="q" value="{{ $search }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cari nama produk..." />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Filter') }}</label>
                        <select name="active" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all" @selected($active === 'all')>{{ __('Semua') }}</option>
                            <option value="1" @selected($active === '1')>{{ __('Aktif') }}</option>
                            <option value="0" @selected($active === '0')>{{ __('Nonaktif') }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex items-center gap-3">
                        <button class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            {{ __('Terapkan') }}
                        </button>
                        <a href="{{ route('seller.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Harga</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Stok</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-3 py-3 text-sm text-gray-900">{{ $product->name }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $product->stock }}</td>
                                    <td class="px-3 py-3 text-sm">
                                        @if ($product->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 text-sm text-right whitespace-nowrap">
                                        <a href="{{ route('seller.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('seller.products.destroy', $product) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Hapus produk ini?')" class="ms-3 text-red-600 hover:text-red-900 font-medium">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">
                                        {{ __('Belum ada data.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

