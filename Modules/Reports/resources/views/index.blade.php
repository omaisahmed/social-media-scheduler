<x-core::layouts.app>
<x-slot name="title">Reports</x-slot>

<x-slot name="header">
    <x-core::page-header title="Reports" description="Generate and download PDF reports." />
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div>
        <x-core::card title="Generate report" description="Export your data as a PDF.">
            <form method="POST" action="{{ route('reports.generate') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="type" value="Report type" />
                    <div class="relative mt-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <x-select id="type" name="type" class="pl-9" :options="[
                            'analytics' => 'Analytics summary',
                            'content' => 'Content log',
                        ]" required />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="from" value="From" />
                        <x-text-input id="from" name="from" type="date" class="mt-1.5" :value="old('from', now()->subDays(30)->format('Y-m-d'))" required />
                    </div>
                    <div>
                        <x-input-label for="to" value="To" />
                        <x-text-input id="to" name="to" type="date" class="mt-1.5" :value="old('to', now()->format('Y-m-d'))" required />
                    </div>
                </div>
                <x-input-error :messages="$errors->all()" />

                <x-primary-button class="w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Generate PDF
                </x-primary-button>
            </form>
        </x-core::card>
    </div>

    <div class="lg:col-span-2">
        <x-core::card title="Past exports" description="Download previously generated reports.">
            @if ($exports->isEmpty())
                <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">No reports generated yet.</p>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($exports as $export)
                        <div class="flex items-center justify-between gap-3 py-3.5">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst($export->type) }} report</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $export->created_at->format('M j, Y g:i A') }}
                                        @if ($export->generated_at)
                                            · generated {{ $export->generated_at->diffForHumans() }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($export->status === 'completed')
                                    <x-core::badge color="green">Ready</x-core::badge>
                                    <a href="{{ route('reports.download', $export) }}"
                                       class="inline-flex items-center gap-1 rounded-lg bg-brand-50 px-2.5 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-100 dark:bg-brand-950/60 dark:text-brand-300 dark:hover:bg-brand-950">
                                        Download
                                    </a>
                                    <button type="button"
                                            @click="$dispatch('confirm', {
                                                action: '{{ route('reports.destroy', $export) }}',
                                                method: 'DELETE',
                                                message: 'Delete this report?'
                                            })"
                                            class="text-xs font-semibold text-red-600 transition hover:text-red-500 dark:text-red-400">Delete</button>
                                @elseif ($export->status === 'failed')
                                    <x-core::badge color="red">Failed</x-core::badge>
                                @else
                                    <x-core::badge color="blue">Processing</x-core::badge>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">{{ $exports->links() }}</div>
            @endif
        </x-core::card>
    </div>
</div>
</x-core::layouts.app>
