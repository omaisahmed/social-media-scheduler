@props(['type' => 'submit'])

<button {{ $attributes->merge(['type' => $type, 'class' => 'inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-brand-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition duration-150 hover:shadow-lg hover:shadow-brand-600/30 hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:pointer-events-none disabled:opacity-50']) }}>
    {{ $slot }}
</button>
