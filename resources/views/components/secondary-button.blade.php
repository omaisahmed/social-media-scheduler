@props(['type' => 'button'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-card ring-1 ring-gray-200 transition duration-150 hover:bg-gray-50 hover:ring-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-700 dark:hover:bg-gray-800 disabled:pointer-events-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
