@props(['title', 'description' => null, 'kicker' => null, 'gradient' => false])

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        @if ($kicker)
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-brand-600 dark:text-brand-400">{{ $kicker }}</p>
        @endif
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white {{ $gradient ? 'text-gradient-brand' : '' }}">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
    </div>
    @if (isset($actions))
        <div class="flex shrink-0 items-center gap-3">
            {{ $actions }}
        </div>
    @elseif ($slot->isNotEmpty())
        <div class="flex shrink-0 items-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
