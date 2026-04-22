<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pesanan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <select name="status" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all" @selected($status === 'all')>{{ __('Semua') }}</option>
                            @foreach (\App\Models\Order::allowedTransitions() as $s => $_)
                                <option value="{{ $s }}" @selected($status === $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                        {{ __('Filter') }}
                    </button>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Order</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Produk</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Qty</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Subtotal</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orderItems as $item)
                                <tr>
                                    <td class="px-3 py-3 text-sm text-gray-900">{{ $item->order->order_code }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $item->product->name }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $item->qty }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-900 font-semibold">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $item->order->status }}</td>
                                    <td class="px-3 py-3 text-sm text-right whitespace-nowrap">
                                        @php($order = $item->order)
                                        @foreach (\App\Models\Order::allowedTransitions()[$order->status] ?? [] as $next)
                                            <form method="POST" action="{{ route('seller.orders.status', $order) }}" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="{{ $next }}">
                                                <button class="ms-2 rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">
                                                    {{ $next }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-6 text-center text-sm text-gray-500">
                                        {{ __('Belum ada pesanan.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orderItems->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

