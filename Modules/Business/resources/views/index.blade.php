<x-core::layouts.app>
<x-slot name="title">Businesses</x-slot>

<x-slot name="header">
    <x-core::page-header title="Businesses" description="Manage the businesses you belong to.">
        <a href="{{ route('business.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
            New business
        </a>
    </x-core::page-header>
</x-slot>

<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($businesses as $business)
        <x-core::card>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    @if ($business->logo_path)
                        <img src="{{ asset('storage/'.$business->logo_path) }}" alt="" class="h-10 w-10 rounded-lg object-cover">
                    @else
                        <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-semibold">
                            {{ strtoupper(substr($business->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $business->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $business->industry ?? 'General' }}</p>
                    </div>
                </div>
                @if ($business->getKey() === auth()->user()->business_id)
                    <x-core::badge color="green">Active</x-core::badge>
                @endif
            </div>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                {{ $business->description ?? 'No description provided.' }}
            </p>

            <div class="mt-4 flex gap-3">
                @if ($business->getKey() !== auth()->user()->business_id)
                    <button class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Switch</button>
                @endif
                <a href="{{ route('business.edit', $business) }}" class="text-sm font-medium text-gray-600 hover:text-gray-500 dark:text-gray-400">Edit</a>
            </div>
        </x-core::card>
    @empty
        <div class="sm:col-span-2 lg:col-span-3">
            <x-core::card>
                <div class="text-center py-12">
                    <h3 class="font-semibold text-gray-900 dark:text-white">No businesses yet</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Create your first business to get started.</p>
                    <a href="{{ route('business.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
                        Create business
                    </a>
                </div>
            </x-core::card>
        </div>
    @endforelse
</div>
</x-core::layouts.app>
