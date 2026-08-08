<x-core::layouts.app>
<x-slot name="title">New contact</x-slot>

<x-slot name="header">
    <x-core::page-header title="New contact" description="Add a person or page you want to @mention in posts." />
</x-slot>

<form method="POST" action="{{ route('contacts.store') }}" class="mx-auto max-w-3xl space-y-6">
    @csrf

    <x-core::card title="Details">
        <div class="space-y-5">
            <div>
                <x-input-label for="name" value="Name" required />
                <x-text-input id="name" name="name" class="mt-1.5"
                              :value="old('name')" placeholder="e.g. John Doe" required />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="avatar_url" value="Avatar URL (optional)" />
                <x-text-input id="avatar_url" name="avatar_url" type="url" class="mt-1.5"
                              :value="old('avatar_url')" placeholder="https://..." />
                <x-input-error class="mt-2" :messages="$errors->get('avatar_url')" />
            </div>
        </div>
    </x-core::card>

    <x-core::card title="Platform handles" description="Optional. Filling at least a handle (or platform id) lets the app tag this contact when publishing.">
        <div class="space-y-6">
            @include('contacts::partials.handle-fields', ['platforms' => $platforms, 'contact' => null])
        </div>
    </x-core::card>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('contacts.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Cancel</a>
        <x-primary-button>Create contact</x-primary-button>
    </div>
</form>
</x-core::layouts.app>
