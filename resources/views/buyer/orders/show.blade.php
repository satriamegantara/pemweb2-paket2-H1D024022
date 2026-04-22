<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pesanan') }}
            </h2>
            <a href="{{ route('buyer.orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; {{ __('Kembali ke Daftar Pesanan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Order Info Card --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Kode Pesanan') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->order_code }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $statusClasses = [
                                'menunggu_pembayaran' => 'bg-yellow-100 text-yellow-800',
                                'dibayar' => 'bg-blue-100 text-blue-800',
                                'diproses' => 'bg-purple-100 text-purple-800',
                                'dikirim' => 'bg-indigo-100 text-indigo-800',
                                'selesai' => 'bg-green-100 text-green-800',
                                'dibatalkan' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'menunggu_pembayaran' => 'Menunggu Pembayaran',
                                'dibayar' => 'Dibayar',
                                'diproses' => 'Diproses',
                                'dikirim' => 'Dikirim',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Tanggal Pesanan') }}</p>
                        <p class="text-gray-900">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ __('Alamat Pengiriman') }}</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $order->shipping_address }}</p>
                    </div>
                    @if($order->payment_proof)
                    <div class="sm:col-span-2">
                        <p class="text-sm text-gray-500 mb-2">{{ __('Bukti Pembayaran') }}</p>
                        <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-sm text-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat Bukti Pembayaran
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Order Items Card --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Item Pesanan') }}</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row gap-4">
                                {{-- Product Image --}}
                                <div class="flex-shrink-0">
                                    @php
                                        $primaryImage = $item->product?->primaryImage();
                                        $imageUrl = $primaryImage
                                            ? asset('storage/' . $primaryImage->image_path)
                                            : 'https://via.placeholder.com/100x100?text=No+Image';
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $item->product?->name }}"
                                         class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                </div>

                                {{-- Product Details --}}
                                <div class="flex-1 min-w-0">
                                    <a href="{{ $item->product ? route('products.show', $item->product) : '#' }}"
                                       class="text-lg font-medium text-gray-900 hover:text-indigo-600 truncate block">
                                        {{ $item->product?->name ?? 'Produk tidak ditemukan' }}
                                    </a>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $item->store?->name ?? 'Toko tidak ditemukan' }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-600">
                                        <span>{{ $item->qty }} x Rp {{ number_format((float) $item->price, 0, ',', '.') }}</span>
                                        <span class="font-medium text-gray-900">
                                            = Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:justify-end">
                        <div class="w-full sm:w-64 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal ({{ $order->items->count() }} item)</span>
                                <span>Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Ongkos Kirim</span>
                                <span>Rp 0</span>
                            </div>
                            <div class="flex justify-between text-base font-semibold text-gray-900 pt-2 border-t border-gray-200">
                                <span>Total</span>
                                <span>Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            @if($order->canTransitionTo(\App\Models\Order::STATUS_DIBATALKAN))
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <form method="POST" action="{{ route('buyer.orders.cancel', $order) }}" class="flex justify-end">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition"
                                onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
