<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pesanan Saya') }}
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
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kode</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="px-3 py-3 text-sm text-gray-900">
                                        <a href="{{ route('buyer.orders.show', $order) }}" class="hover:text-indigo-600 hover:underline">
                                            {{ $order->order_code }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-600">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-sm">
                                        @php
                                            $statusClasses = [
                                                'menunggu_pembayaran' => 'bg-yellow-100 text-yellow-800',
                                                'dibayar' => 'bg-blue-100 text-blue-800',
                                                'diproses' => 'bg-purple-100 text-purple-800',
                                                'dikirim' => 'bg-indigo-100 text-indigo-800',
                                                'selesai' => 'bg-green-100 text-green-800',
                                                'dibatalkan' => 'bg-red-100 text-red-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-sm text-right whitespace-nowrap">
                                        @if ($order->canTransitionTo(\App\Models\Order::STATUS_DIBATALKAN))
                                            <form method="POST" action="{{ route('buyer.orders.cancel', $order) }}" class="inline">
                                                @csrf
                                                <button class="text-red-600 hover:text-red-900 font-medium" onclick="return confirm('Batalkan pesanan ini?')">
                                                    {{ __('Batalkan') }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500">
                                        {{ __('Belum ada pesanan.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

