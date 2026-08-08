@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red-600 to-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-red-600/25 transition duration-150 hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:pointer-events-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
