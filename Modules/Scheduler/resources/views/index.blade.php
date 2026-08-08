<x-core::layouts.app>
<x-slot name="title">Scheduler</x-slot>

<x-slot name="header">
    <x-core::page-header title="Scheduler" description="Define the best times to publish on each platform." />
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <x-core::card title="Best time windows" description="Optimize when your posts go live.">
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($windows as $window)
                    <div class="group flex items-center justify-between gap-3 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                                <x-core::platform-icon :platform="$window->platform" class="h-4.5 w-4.5" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $window->dayLabel() }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ substr($window->start_time, 0, 5) }} – {{ substr($window->end_time, 0, 5) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($window->score)
                                <x-core::badge color="blue">score {{ $window->score }}</x-core::badge>
                            @endif
                            <button type="button"
                                    @click="$dispatch('confirm', {
                                        action: '{{ route('scheduler.destroy', $window) }}',
                                        method: 'DELETE',
                                        message: 'Delete this best time window?'
                                    })"
                                    class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                        No best-time windows yet. Add one to start optimizing when you publish.
                    </p>
                @endforelse
            </div>
        </x-core::card>
    </div>

    <div>
        <x-core::card title="Add window" description="Pick a platform and time range.">
            <form method="POST" action="{{ route('scheduler.store') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="platform" value="Platform" />
                    <div class="relative mt-1.5">
                        <x-core::platform-icon :platform="old('platform', 'facebook')" class="pointer-events-none absolute left-3 top-1/2 h-4.5 w-4.5 -translate-y-1/2" />
                        <x-select id="platform" name="platform" class="pl-9" required>
                            @foreach ($platforms as $platform)
                                <option value="{{ $platform }}" @selected(old('platform') === $platform)>{{ ucfirst($platform) }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('platform')" />
                </div>

                <div>
                    <x-input-label for="day_of_week" value="Day" />
                    <x-select id="day_of_week" name="day_of_week" class="mt-1.5" required>
                        @foreach ($days as $index => $day)
                            <option value="{{ $index }}" @selected(old('day_of_week') == $index)>{{ $day }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error class="mt-2" :messages="$errors->get('day_of_week')" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input-label for="start_time" value="Start" />
                        <x-text-input id="start_time" name="start_time" type="time" class="mt-1.5"
                                      :value="old('start_time', '09:00')" />
                        <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
                    </div>
                    <div>
                        <x-input-label for="end_time" value="End" />
                        <x-text-input id="end_time" name="end_time" type="time" class="mt-1.5"
                                      :value="old('end_time', '17:00')" />
                        <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
                    </div>
                </div>

                <x-primary-button class="w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add window
                </x-primary-button>
            </form>
        </x-core::card>
    </div>
</div>
</x-core::layouts.app>
