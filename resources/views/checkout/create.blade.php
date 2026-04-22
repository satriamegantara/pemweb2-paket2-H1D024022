<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('checkoutIssues'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-800">
                            {{ __('Checkout gagal karena beberapa item berikut bermasalah:') }}
                        </p>

                        <ul class="mt-3 space-y-3">
                            @foreach (session('checkoutIssues') as $issue)
                                <li class="rounded-md bg-white/70 p-3 text-sm text-red-700 ring-1 ring-red-100">
                                    <div class="font-medium text-red-900">
                                        {{ $issue['product'] }}
                                    </div>
                                    <div class="mt-1">
                                        {{ $issue['message'] }}
                                    </div>
                                    @if (isset($issue['requested'], $issue['available']))
                                        <div class="mt-1 text-xs text-red-600">
                                            {{ __('Pesanan: :requested, stok tersedia: :available', ['requested' => $issue['requested'], 'available' => $issue['available']]) }}
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="shipping_address" :value="__('Alamat Pengiriman')" />
                        <textarea id="shipping_address" name="shipping_address"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            rows="4" required>{{ $defaultAddress }}</textarea>
                        <x-input-error :messages="$errors->get('shipping_address')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="payment_method" :value="__('Metode Pembayaran')" />
                        <select id="payment_method" name="payment_method"
                            class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                            <option value="transfer_bank" @selected(old('payment_method') === 'transfer_bank')>
                                {{ __('Transfer Bank') }}</option>
                            <option value="ewallet" @selected(old('payment_method') === 'ewallet')>{{ __('E-Wallet') }}
                            </option>
                            <option value="cod" @selected(old('payment_method') === 'cod')>{{ __('COD') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="payment_proof" :value="__('Upload Bukti Pembayaran')" />
                        <input id="payment_proof" type="file" name="payment_proof"
                            class="mt-1 block w-full text-sm text-gray-700" required>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Format: jpg/jpeg/png/webp/pdf. Max 2MB.') }}</p>
                        <x-input-error :messages="$errors->get('payment_proof')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('cart.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Kembali ke Cart') }}
                        </a>
                        <x-primary-button>
                            {{ __('Buat Pesanan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>