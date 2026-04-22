<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kategori') }}
            </h2>

            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                {{ __('Tambah') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                        <input name="q" value="{{ $search }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cari nama kategori..." />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Filter') }}</label>
                        <select name="icon" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="all" @selected($iconFilter === 'all')>{{ __('Semua') }}</option>
                            <option value="with" @selected($iconFilter === 'with')>{{ __('Ada icon') }}</option>
                            <option value="without" @selected($iconFilter === 'without')>{{ __('Tanpa icon') }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex items-center gap-3">
                        <button class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">
                            {{ __('Terapkan') }}
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </form>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Slug</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Icon</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-3 py-3 text-sm text-gray-900">{{ $category->name }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $category->slug }}</td>
                                    <td class="px-3 py-3 text-sm text-gray-600">{{ $category->icon ?: '-' }}</td>
                                    <td class="px-3 py-3 text-sm text-right whitespace-nowrap">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Hapus kategori ini?')" class="ms-3 text-red-600 hover:text-red-900 font-medium">
                                                {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500">
                                        {{ __('Belum ada data.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

