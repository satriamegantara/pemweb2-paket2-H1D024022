@csrf

@php($product = $product ?? null)

<div>
    <x-input-label for="category_id" :value="__('Kategori')" />
    <select id="category_id" name="category_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
        <option value="">{{ __('Pilih kategori') }}</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $cat->id)>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="name" :value="__('Nama Produk')" />
    <x-text-input id="name" name="name" class="block mt-1 w-full" type="text" :value="old('name', $product?->name ?? '')" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Deskripsi (opsional)')" />
    <textarea id="description" name="description" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="5">{{ old('description', $product?->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
    <div>
        <x-input-label for="price" :value="__('Harga')" />
        <x-text-input id="price" name="price" class="block mt-1 w-full" type="number" min="0" step="0.01" :value="old('price', $product?->price ?? 0)" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="stock" :value="__('Stok')" />
        <x-text-input id="stock" name="stock" class="block mt-1 w-full" type="number" min="0" step="1" :value="old('stock', $product?->stock ?? 0)" required />
        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="weight" :value="__('Berat (gram)')" />
        <x-text-input id="weight" name="weight" class="block mt-1 w-full" type="number" min="0" step="1" :value="old('weight', $product?->weight ?? '')" />
        <x-input-error :messages="$errors->get('weight')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            @checked(old('is_active', $product?->is_active ?? true))>
        <span class="text-sm text-gray-700">{{ __('Aktifkan produk') }}</span>
    </label>
    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
</div>

<div class="mt-6">
    <x-input-label for="images" :value="__('Foto Produk (3-5 gambar)')" />
    <input id="images" type="file" name="images[]" multiple accept="image/*" class="mt-1 block w-full text-sm text-gray-700" />
    <p class="mt-1 text-xs text-gray-500">{{ __('Format: jpg/jpeg/png/webp. Max 2MB per file.') }}</p>
    <x-input-error :messages="$errors->get('images')" class="mt-2" />
    <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
</div>

