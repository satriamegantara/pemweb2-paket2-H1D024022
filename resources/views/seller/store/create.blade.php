<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Toko') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('seller.store.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="store_name" :value="__('Nama Toko')" />
                        <x-text-input id="store_name" name="store_name" class="block mt-1 w-full" type="text" :value="old('store_name')" required autofocus />
                        <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
                        <textarea id="description" name="description" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="address" :value="__('Alamat (opsional)')" />
                        <textarea id="address" name="address" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('seller.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>
                            {{ __('Simpan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

