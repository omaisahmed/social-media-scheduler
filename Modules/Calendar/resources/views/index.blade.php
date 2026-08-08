<x-core::layouts.app>
<x-slot name="title">Calendar</x-slot>

<x-slot name="header">
    <x-core::page-header title="Calendar" description="See your scheduled posts across the month.">
        <div class="flex items-center gap-3">
            <a href="{{ route('calendar.index', ['month' => $month->subMonth()->format('Y-m')]) }}"
               class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                &larr; Previous
            </a>
            <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $month->format('F Y') }}</span>
            <a href="{{ route('calendar.index', ['month' => $month->addMonth()->format('Y-m')]) }}"
               class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                Next &rarr;
            </a>
        </div>
    </x-core::page-header>
</x-slot>

<div class="overflow-hidden rounded-xl ring-1 ring-gray-200 dark:ring-gray-800">
    <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-900 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
            <div class="py-2">{{ $day }}</div>
        @endforeach
    </div>

    @foreach ($weeks as $week)
        <div class="grid grid-cols-7 border-t border-gray-200 dark:border-gray-800">
            @foreach ($week as $day)
                @php
                    $key = $day->format('Y-m-d');
                    $inMonth = $day->month === $month->month;
                    $isToday = $day->isToday();
                @endphp
                <div class="min-h-28 border-r border-gray-200 dark:border-gray-800 last:border-r-0 p-2 {{ $inMonth ? 'bg-white dark:bg-gray-900' : 'bg-gray-50 dark:bg-gray-950' }}">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-medium {{ $isToday ? 'inline-flex items-center justify-center h-6 w-6 rounded-full bg-indigo-600 text-white' : ($inMonth ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600') }}">
                            {{ $day->format('j') }}
                        </span>
                    </div>

                    @if (! empty($posts[$key]))
                        <div class="mt-1 space-y-1">
                            @foreach ($posts[$key] as $post)
                                <a href="{{ route('posts.show', $post) }}"
                                   class="block truncate rounded bg-indigo-50 dark:bg-indigo-950/50 px-1.5 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/50"
                                   title="{{ $post->title ?? 'Untitled' }}">
                                    {{ $post->scheduled_at->format('g:i A') }} · {{ Str::limit($post->title ?? 'Untitled', 18) }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>
</x-core::layouts.app>
