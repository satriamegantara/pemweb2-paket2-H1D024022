<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Seller Dashboard') }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('seller.products.index') }}"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    {{ __('Produk') }}
                </a>
                <a href="{{ route('seller.orders.index') }}"
                    class="rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                    {{ __('Pesanan') }}
                </a>
                @if ($hasStore)
                    <a href="{{ route('seller.reports.sales.index') }}"
                        class="rounded-md bg-emerald-700 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                        {{ __('Laporan') }}
                    </a>
                    <a href="{{ route('seller.reports.sales.excel') }}"
                        class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                        {{ __('Export Excel') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (!$hasStore)
                <div class="rounded-lg bg-amber-50 p-6 shadow-sm">
                    <div class="text-sm font-semibold text-amber-800">{{ __('Kamu belum punya toko.') }}</div>
                    <div class="mt-1 text-sm text-amber-700">{{ __('Buat toko dulu supaya bisa kelola produk & pesanan.') }}
                    </div>
                    <a href="{{ route('seller.store.create') }}"
                        class="mt-4 inline-flex items-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-500">
                        {{ __('Buat Toko') }}
                    </a>
                </div>
            @endif

            <div class="mt-0 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-xs text-gray-500">{{ __('Total Produk') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $productsTotal }}</div>
                    <div class="mt-2 text-sm text-gray-600">Aktif: {{ $productsActive }} • Nonaktif:
                        {{ $productsInactive }}
                    </div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-xs text-gray-500">{{ __('Total Item Pesanan') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $itemsTotal }}</div>
                    <div class="mt-2 text-sm text-gray-600">{{ __('Hanya item milik toko kamu') }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-xs text-gray-500">{{ __('Menunggu Pembayaran') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $itemsWaiting }}</div>
                    <div class="mt-2 text-sm text-gray-600">{{ __('Butuh verifikasi admin') }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-xs text-gray-500">{{ __('Dibayar') }}</div>
                    <div class="mt-1 text-2xl font-bold text-gray-900">{{ $itemsPaid }}</div>
                    <div class="mt-2 text-sm text-gray-600">{{ __('Siap diproses') }}</div>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700">{{ __('Ringkasan Status (item pesanan)') }}</h3>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-md bg-amber-50 p-4">
                        <div class="text-xs text-amber-700">{{ __('menunggu_pembayaran') }}</div>
                        <div class="mt-1 text-xl font-bold text-amber-900">{{ $itemsWaiting }}</div>
                    </div>
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="text-xs text-blue-700">{{ __('dibayar') }}</div>
                        <div class="mt-1 text-xl font-bold text-blue-900">{{ $itemsPaid }}</div>
                    </div>
                    <div class="rounded-md bg-indigo-50 p-4">
                        <div class="text-xs text-indigo-700">{{ __('diproses') }}</div>
                        <div class="mt-1 text-xl font-bold text-indigo-900">{{ $itemsProcessed }}</div>
                    </div>
                    <div class="rounded-md bg-purple-50 p-4">
                        <div class="text-xs text-purple-700">{{ __('dikirim') }}</div>
                        <div class="mt-1 text-xl font-bold text-purple-900">{{ $itemsShipped }}</div>
                    </div>
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="text-xs text-green-700">{{ __('selesai') }}</div>
                        <div class="mt-1 text-xl font-bold text-green-900">{{ $itemsDone }}</div>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <div class="text-xs text-gray-700">{{ __('dibatalkan') }}</div>
                        <div class="mt-1 text-xl font-bold text-gray-900">{{ $itemsCancelled }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-gray-700">{{ __('Notifikasi Pesanan') }}</h3>
                    @if ($unreadNotificationsCount > 0)
                        <span
                            class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">
                            {{ $unreadNotificationsCount }} {{ __('belum dibaca') }}
                        </span>
                    @endif
                </div>

                @if ($recentNotifications->isEmpty())
                    <p class="mt-4 text-sm text-gray-500">{{ __('Belum ada notifikasi untuk saat ini.') }}</p>
                @else
                <div class="mt-4 divide-y divide-gray-100 rounded-md border border-gray-100">
                    @foreach ($recentNotifications as $notification)
                    @php($message = $notification->data['message'] ?? 'Ada pembaruan pesanan baru')
                    @php($orderCode = $notification->data['order_code'] ?? null)

                    <div class="flex flex-col gap-1 px-4 py-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p
                                class="text-sm font-medium {{ is_null($notification->read_at) ? 'text-gray-900' : 'text-gray-700' }}">
                                {{ $message }}
                            </p>
                            @if ($orderCode)
                                <p class="mt-0.5 text-xs text-gray-500">{{ __('Kode Pesanan') }}: {{ $orderCode }}</p>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 sm:whitespace-nowrap">
                            {{ $notification->created_at?->diffForHumans() }}
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>