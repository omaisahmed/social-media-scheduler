@props(['checked' => false])

<input type="checkbox" @checked($checked) {{ $attributes->merge(['class' => 'h-4 w-4 shrink-0 rounded border-gray-300 bg-gray-50 text-brand-600 shadow-sm transition focus:ring-2 focus:ring-brand-500/40 focus:ring-offset-0 dark:border-gray-600 dark:bg-gray-800 dark:text-brand-500 dark:checked:border-brand-500']) }}>
