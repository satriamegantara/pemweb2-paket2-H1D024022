@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:focus:border-primary dark:focus:ring-primary']) }}>
