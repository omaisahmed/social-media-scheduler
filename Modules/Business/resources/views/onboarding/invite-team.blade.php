<div class="min-h-screen flex items-center justify-center bg-mesh-light px-4 py-12 dark:bg-mesh-dark">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-purple-600 text-lg font-bold text-white shadow-lg shadow-brand-600/30">
                {{ strtoupper(substr($business->name, 0, 1)) }}
            </span>
            <h1 class="mt-4 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Invite your team
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                You're working within <strong>{{ $business->name }}</strong>. Invite teammates to collaborate on your social posts.
            </p>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-card ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
            <form method="POST" action="#" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" value="Teammate email" />
                    <div class="relative mt-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <x-text-input id="email" name="email" type="email" class="pl-9"
                                      placeholder="teammate@example.com" />
                    </div>
                </div>

                <x-primary-button class="w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    Send invitation
                </x-primary-button>

                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('dashboard.index') }}" class="font-medium text-brand-600 hover:text-brand-500">Skip for now</a>
                </p>
            </form>
        </div>
    </div>
</div>
