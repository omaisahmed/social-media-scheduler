@props(['title' => null, 'description' => null, 'padding' => true, 'hover' => false])

<div {{ $attributes->merge(['class' => 'rounded-xl bg-white shadow-card ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800 '.($hover ? 'transition duration-200 hover:shadow-card-hover hover:ring-gray-300 dark:hover:ring-gray-700' : '')]) }}>
    @if ($title || isset($heading))
        <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
            <div class="min-w-0">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    @isset($heading)
                        {{ $heading }}
                    @else
                        {{ $title }}
                    @endisset
                </h3>
                @if ($description)
                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="shrink-0">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding ? 'p-5' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-100 px-5 py-3 dark:border-gray-800">
            {{ $footer }}
        </div>
    @endisset
</div>
