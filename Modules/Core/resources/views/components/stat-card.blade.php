@props(['label', 'value', 'icon' => null, 'trend' => null, 'trendUp' => true, 'color' => 'brand', 'sub' => null])

@php
    $colors = [
        'brand'  => 'bg-brand-50 text-brand-600 dark:bg-brand-950/60 dark:text-brand-400',
        'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400',
        'blue'   => 'bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400',
        'green'  => 'bg-green-50 text-green-600 dark:bg-green-950/60 dark:text-green-400',
        'red'    => 'bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400',
        'amber'  => 'bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400',
        'purple' => 'bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400',
        'pink'   => 'bg-pink-50 text-pink-600 dark:bg-pink-950/60 dark:text-pink-400',
        'sky'    => 'bg-sky-50 text-sky-600 dark:bg-sky-950/60 dark:text-sky-400',
    ];

    $accentDots = [
        'brand'  => 'from-brand-500 to-purple-500',
        'indigo' => 'from-indigo-500 to-blue-500',
        'blue'   => 'from-blue-500 to-sky-500',
        'green'  => 'from-green-500 to-emerald-500',
        'red'    => 'from-red-500 to-rose-500',
        'amber'  => 'from-amber-500 to-orange-500',
        'purple' => 'from-purple-500 to-fuchsia-500',
        'pink'   => 'from-pink-500 to-rose-500',
        'sky'    => 'from-sky-500 to-cyan-500',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'group relative overflow-hidden rounded-xl bg-white p-5 shadow-card ring-1 ring-gray-200 transition duration-200 hover:shadow-card-hover hover:ring-gray-300 dark:bg-gray-900 dark:ring-gray-800 dark:hover:ring-gray-700']) }}>
    <div class="absolute inset-x-0 top-0 h-0.5 bg-gradient-to-r opacity-0 transition-opacity duration-200 group-hover:opacity-100 {{ $accentDots[$color] ?? $accentDots['brand'] }}"></div>

    <div class="flex items-start justify-between">
        <div class="min-w-0">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ $label }}</p>
            <p class="mt-1.5 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $value }}</p>
            @if ($sub)
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">{{ $sub }}</p>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @if ($trend)
                <span class="inline-flex items-center gap-0.5 text-xs font-semibold {{ $trendUp ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                        @if ($trendUp)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25" />
                        @endif
                    </svg>
                    {{ $trend }}
                </span>
            @endif

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $colors[$color] ?? $colors['brand'] }}">
                @isset($icon)
                    {{ $icon }}
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                    </svg>
                @endisset
            </div>
        </div>
    </div>
</div>
