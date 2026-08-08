<x-core::layouts.app>
<x-slot name="title">Contacts</x-slot>

<x-slot name="header">
    <x-core::page-header title="Contacts" description="People you can @mention in your posts.">
        <a href="{{ route('contacts.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
            New contact
        </a>
    </x-core::page-header>
</x-slot>

<div class="mb-6">
    <form method="GET" action="{{ route('contacts.index') }}" class="flex flex-wrap items-center gap-3">
        <x-text-input name="search" type="text" placeholder="Search by name or handle..." class="w-64"
                      :value="$filters['search'] ?? ''" />
        @if ($filters['search'] ?? null)
            <a href="{{ route('contacts.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400">Clear search</a>
        @endif
    </form>
</div>

@if ($connectedAccounts->isNotEmpty())
    <x-core::card class="mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Import mentionable people</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pull followers from a connected account into your contacts, or just type
                    <span class="font-mono text-xs">@name</span> in the post editor to search platforms live.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @foreach ($connectedAccounts as $account)
                    <form method="POST" action="{{ route('contacts.import') }}">
                        @csrf
                        <input type="hidden" name="account_id" :value="{{ $account->getKey() }}">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-700 ring-1 ring-inset ring-gray-200 transition hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700 dark:hover:bg-gray-700">
                            <x-core::platform-icon :platform="$account->platform" class="h-3.5 w-3.5" />
                            Import {{ $account->account_name }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </x-core::card>
@endif

<div class="space-y-4">
    @forelse ($contacts as $contact)
        <x-core::card>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if ($contact->avatar_url)
                        <img src="{{ $contact->avatar_url }}" alt="" class="h-12 w-12 rounded-xl object-cover ring-1 ring-gray-100 dark:ring-gray-700">
                    @else
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-50 text-lg font-semibold text-gray-400 ring-1 ring-gray-100 dark:bg-gray-800 dark:ring-gray-700">
                            {{ mb_substr($contact->name, 0, 1) }}
                        </span>
                    @endif
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $contact->name }}</h3>
                        <div class="mt-0.5 flex flex-wrap items-center gap-1.5">
                            @forelse ($contact->handles as $handle)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                    <x-core::platform-icon :platform="$handle->platform" class="h-3 w-3" />
                                    {{ $handle->handleAt() }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400 dark:text-gray-500">No platform handles yet</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('contacts.edit', $contact) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Edit</a>
                    <button type="button"
                            @click="$dispatch('confirm', {
                                action: '{{ route('contacts.destroy', $contact) }}',
                                method: 'DELETE',
                                message: 'Delete {{ $contact->name }}? Posts mentioning them will keep the mention text but it will no longer tag anyone.'
                            })"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                        Delete
                    </button>
                </div>
            </div>
        </x-core::card>
    @empty
        <x-core::card>
            <div class="text-center py-12">
                <h3 class="font-semibold text-gray-900 dark:text-white">No contacts found</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Add the people and pages you want to @mention in posts. Type <span class="font-mono text-xs">@</span> in the post editor to pick one.</p>
                <a href="{{ route('contacts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
                    Add your first contact
                </a>
            </div>
        </x-core::card>
    @endforelse
</div>

@if ($contacts->hasPages())
    <div class="mt-6">
        {{ $contacts->links() }}
    </div>
@endif
</x-core::layouts.app>
