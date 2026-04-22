<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="shipping_address" :value="__('Alamat Pengiriman')" />
                        <textarea id="shipping_address" name="shipping_address" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4" required>{{ $defaultAddress }}</textarea>
                        <x-input-error :messages="$errors->get('shipping_address')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="payment_method" :value="__('Metode Pembayaran')" />
                        <select id="payment_method" name="payment_method" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="transfer_bank" @selected(old('payment_method') === 'transfer_bank')>{{ __('Transfer Bank') }}</option>
                            <option value="ewallet" @selected(old('payment_method') === 'ewallet')>{{ __('E-Wallet') }}</option>
                            <option value="cod" @selected(old('payment_method') === 'cod')>{{ __('COD') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="payment_proof" :value="__('Upload Bukti Pembayaran')" />
                        <input id="payment_proof" type="file" name="payment_proof" class="mt-1 block w-full text-sm text-gray-700" required>
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

