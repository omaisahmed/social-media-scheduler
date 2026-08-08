<footer class="border-t border-gray-200 dark:border-gray-800 px-4 sm:px-6 lg:px-8 py-4">
    <div class="flex items-center justify-between">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} {{ auth()->user()->business?->name ?? config('app.name') }}. All rights reserved.
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-600">Powered by {{ config('app.name') }}</p>
    </div>
</footer>
