<x-core::layouts.app>
<x-slot name="title">New template</x-slot>

<x-slot name="header">
    <x-core::page-header title="New template" description="Create a reusable post template." />
</x-slot>

<form method="POST" action="{{ route('templates.store') }}" class="space-y-6">
    @csrf

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-core::card title="Template details" description="Save content you reuse often.">
                <div class="space-y-5">
                    <div>
                        <x-input-label for="name" value="Name" required />
                        <x-text-input id="name" name="name" class="mt-1.5" :value="old('name')" required autofocus
                                      placeholder="e.g. Product launch" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label for="content" value="Content" />
                            <span class="text-xs text-gray-400 dark:text-gray-500">Markdown supported</span>
                        </div>
                        <x-textarea id="content" name="content" rows="10" class="mt-1.5 font-normal leading-relaxed"
                                    :value="old('content')" placeholder="The reusable post content..." />
                        <x-input-error class="mt-2" :messages="$errors->get('content')" />
                    </div>
                </div>
            </x-core::card>
        </div>

        <div>
            <x-core::card title="Organization" description="Categorize your template with tags."
                          class="lg:sticky lg:top-24">
                <div>
                    <x-input-label for="tags" value="Tags" />
                    <div class="relative mt-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                        </svg>
                        <x-text-input id="tags" name="tags" class="pl-9" :value="old('tags')" placeholder="promo, launch, blog" />
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Separate tags with commas.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('tags')" />
                </div>

                <x-slot name="footer">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('templates.index') }}" class="text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Cancel</a>
                        <x-primary-button>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Create template
                        </x-primary-button>
                    </div>
                </x-slot>
            </x-core::card>
        </div>
    </div>
</form>
</x-core::layouts.app>
