@csrf

@php($category = $category ?? null)

<div>
    <x-input-label for="name" :value="__('Nama')" />
    <x-text-input id="name" name="name" class="block mt-1 w-full" type="text" :value="old('name', $category?->name ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="icon" :value="__('Icon (opsional)')" />
    <x-text-input id="icon" name="icon" class="block mt-1 w-full" type="text" :value="old('icon', $category?->icon ?? '')" />
    <x-input-error :messages="$errors->get('icon')" class="mt-2" />
</div>

