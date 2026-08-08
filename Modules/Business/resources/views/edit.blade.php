<x-core::layouts.app>
<x-slot name="title">Edit business</x-slot>

<x-slot name="header">
    <x-core::page-header title="{{ $business->name }}" description="Update your business details." />
</x-slot>

<x-core::card title="Business details" description="Keep your workspace information up to date.">
    <form method="POST" action="{{ route('business.update', $business) }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <x-input-label for="name" value="Business name" required />
                <x-text-input id="name" name="name" class="mt-1.5" :value="old('name', $business->name)" required />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="website_url" value="Website URL (optional)" />
                <div class="relative mt-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    <x-text-input id="website_url" name="website_url" type="url" class="pl-9"
                                  :value="old('website_url', $business->website_url)" />
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('website_url')" />
            </div>

            <div>
                <x-input-label for="industry" value="Industry (optional)" />
                <x-text-input id="industry" name="industry" class="mt-1.5" :value="old('industry', $business->industry)" />
                <x-input-error class="mt-2" :messages="$errors->get('industry')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="description" value="Description (optional)" />
                <x-textarea id="description" name="description" rows="3" class="mt-1.5"
                            :value="old('description', $business->description)" />
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>
        </div>

        <div class="flex items-center gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
            <x-primary-button>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                </svg>
                Save changes
            </x-primary-button>
            <a href="{{ route('business.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Cancel</a>
        </div>
    </form>
</x-core::card>
</x-core::layouts.app>
