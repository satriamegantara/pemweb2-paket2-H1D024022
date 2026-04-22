@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary text-start text-base font-medium text-primary bg-primary-50 focus:outline-none focus:text-primary-700 focus:bg-primary-100 focus:border-primary transition duration-150 ease-in-out dark:text-primary-400 dark:bg-primary-900/20 dark:focus:text-primary-300 dark:focus:bg-primary-900/30 dark:focus:border-primary'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-800 dark:hover:border-gray-600 dark:focus:text-gray-200 dark:focus:bg-gray-800 dark:focus:border-gray-600';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
