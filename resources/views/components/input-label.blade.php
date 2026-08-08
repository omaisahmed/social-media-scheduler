@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="ml-0.5 text-red-500">*</span>
    @endif
</label>
