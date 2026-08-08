<x-core::layouts.app>
<x-slot name="title">Profile</x-slot>

<x-slot name="header">
    <x-core::page-header title="Profile" description="Manage your account settings." />
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <x-core::card title="Profile information" description="Update your account's name and email address.">
            @include('profile.partials.update-profile-information-form')
        </x-core::card>

        <x-core::card title="Update password" description="Ensure your account is using a long, random password.">
            @include('profile.partials.update-password-form')
        </x-core::card>
    </div>

    <div>
        <x-core::card class="lg:sticky lg:top-24">
            @include('profile.partials.delete-user-form')
        </x-core::card>
    </div>
</div>
</x-core::layouts.app>
