<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Dari') }}</label>
                        <input type="date" name="from" value="{{ request('from', $from->toDateString()) }}"
                            class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Sampai') }}</label>
                        <input type="date" name="to" value="{{ request('to', $to->toDateString()) }}"
                            class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                        {{ __('Filter') }}
                    </button>
                </form>

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-gray-100 pt-4">
                    <span class="text-sm font-medium text-gray-700">{{ __('Export:') }}</span>
                    <a href="{{ route('seller.reports.sales.pdf', request()->query()) }}"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        {{ __('Export PDF') }}
                    </a>
                    <a href="{{ route('seller.reports.sales.excel', request()->query()) }}"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        {{ __('Export Excel (.xlsx)') }}
                    </a>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-md bg-gray-50 p-4">
                        <div class="text-xs text-gray-500">{{ __('Total Qty') }}</div>
                        <div class="text-lg font-bold text-gray-900">{{ $totalQty }}</div>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <div class="text-xs text-gray-500">{{ __('Total Penjualan') }}</div>
                        <div class="text-lg font-bold text-gray-900">Rp
                            {{ number_format((float) $totalAmount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Order</th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Produk</th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Qty</th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Subtotal</th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="px-3 py-3 text-sm text-gray-900">{{ $row->order->order_code }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $row->product->name }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $row->qty }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-900 font-semibold">Rp
                                        {{ number_format((float) $row->subtotal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3 text-sm text-gray-600">
                                        {{ $row->order->created_at?->format('Y-m-d') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">
                                        {{ __('Tidak ada data.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $rows->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>