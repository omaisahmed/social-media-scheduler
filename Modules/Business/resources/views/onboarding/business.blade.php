<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <x-core::application-logo class="mx-auto h-12 w-12" />
            <h1 class="mt-4 text-2xl font-semibold text-gray-900 dark:text-white">
                Welcome! Let's set up your business
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Tell us a little about your business so we can get your scheduler ready.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-6">
            <form method="POST" action="{{ route('onboarding.business.store') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" value="Business name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                  :value="old('name')" required autofocus autocomplete="organization" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="website_url" value="Website URL (optional)" />
                    <x-text-input id="website_url" name="website_url" type="url" class="mt-1 block w-full"
                                  :value="old('website_url')" placeholder="https://example.com" />
                    <x-input-error class="mt-2" :messages="$errors->get('website_url')" />
                </div>

                <div>
                    <x-input-label for="industry" value="Industry (optional)" />
                    <x-text-input id="industry" name="industry" type="text" class="mt-1 block w-full"
                                  :value="old('industry')" placeholder="SaaS, Retail, Agency..." />
                    <x-input-error class="mt-2" :messages="$errors->get('industry')" />
                </div>

                <div>
                    <x-input-label for="description" value="Description (optional)" />
                    <x-textarea id="description" name="description" class="mt-1 block w-full"
                                rows="3" :value="old('description')" />
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <x-primary-button class="w-full justify-center">
                    Continue
                </x-primary-button>
            </form>
        </div>
    </div>
</div>
