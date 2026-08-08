@props(['name' => '', 'rows' => 3])

<textarea {{ $attributes->merge([
    'name' => $name,
    'rows' => $rows,
    'class' => 'w-full rounded-lg border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-brand-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-brand-500 dark:focus:bg-gray-900',
]) }}>{{ $slot }}</textarea>
