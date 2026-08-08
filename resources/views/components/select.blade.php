@props(['options' => [], 'placeholder' => null])

<select {{ $attributes->merge(['class' => 'w-full rounded-lg border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-brand-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:focus:border-brand-500 dark:focus:bg-gray-900']) }}>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach ($options as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
    {{ $slot }}
</select>
