@props(['color' => 'gray', 'type' => null, 'platform' => null])

@php
    $typeColors = [
        'success' => 'green',
        'danger' => 'red',
        'info' => 'blue',
        'warning' => 'amber',
        'default' => 'gray',
    ];

    $color = $type ? ($typeColors[$type] ?? 'gray') : $color;

    $colors = [
        'gray'    => 'bg-gray-100 text-gray-700 ring-gray-600/10 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-500/20',
        'blue'    => 'bg-blue-50 text-blue-700 ring-blue-600/10 dark:bg-blue-950/50 dark:text-blue-300 dark:ring-blue-500/30',
        'green'   => 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-950/50 dark:text-green-300 dark:ring-green-500/30',
        'red'     => 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-950/50 dark:text-red-300 dark:ring-red-500/30',
        'amber'   => 'bg-amber-50 text-amber-700 ring-amber-600/10 dark:bg-amber-950/50 dark:text-amber-300 dark:ring-amber-500/30',
        'brand'   => 'bg-brand-50 text-brand-700 ring-brand-600/10 dark:bg-brand-950/50 dark:text-brand-300 dark:ring-brand-500/30',
        'indigo'  => 'bg-indigo-50 text-indigo-700 ring-indigo-600/10 dark:bg-indigo-950/50 dark:text-indigo-300 dark:ring-indigo-500/30',
        'purple'  => 'bg-purple-50 text-purple-700 ring-purple-600/10 dark:bg-purple-950/50 dark:text-purple-300 dark:ring-purple-500/30',
        'sky'     => 'bg-sky-50 text-sky-700 ring-sky-600/10 dark:bg-sky-950/50 dark:text-sky-300 dark:ring-sky-500/30',
        'pink'    => 'bg-pink-50 text-pink-700 ring-pink-600/10 dark:bg-pink-950/50 dark:text-pink-300 dark:ring-pink-500/30',
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10 dark:bg-emerald-950/50 dark:text-emerald-300 dark:ring-emerald-500/30',
    ];

    $platformColors = [
        'facebook'  => 'bg-[#1877F2]/10 text-[#1877F2] ring-[#1877F2]/20 dark:text-[#4a9bf5] dark:ring-[#1877F2]/30',
        'instagram' => 'bg-[#E1306C]/10 text-[#E1306C] ring-[#E1306C]/20 dark:text-[#f26a9b] dark:ring-[#E1306C]/30',
        'linkedin'  => 'bg-[#0A66C2]/10 text-[#0A66C2] ring-[#0A66C2]/20 dark:text-[#4a8fd4] dark:ring-[#0A66C2]/30',
        'twitter'   => 'bg-gray-900/5 text-gray-900 ring-gray-900/10 dark:bg-white/10 dark:text-white dark:ring-white/20',
        'pinterest' => 'bg-[#E60023]/10 text-[#E60023] ring-[#E60023]/20 dark:text-[#f25a73] dark:ring-[#E60023]/30',
    ];

    $resolved = $platform
        ? ($platformColors[strtolower($platform)] ?? $colors['gray'])
        : ($colors[$color] ?? $colors['gray']);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset '.$resolved])->except('type', 'platform') }}>
    {{ $slot }}
</span>
