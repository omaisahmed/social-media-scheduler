<x-core::layouts.app>
<x-slot name="title">Team</x-slot>

<x-slot name="header">
    <x-core::page-header title="Team" description="Manage your team members and invitations." />
</x-slot>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <x-core::card title="Members" description="People with access to this workspace.">
            <x-slot name="actions">
                <x-core::badge>{{ $members->count() }} total</x-core::badge>
            </x-slot>

            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($members as $member)
                    <div class="flex items-center justify-between gap-3 py-3.5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $member->user->avatar() }}" alt="" class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-800">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $member->user->name }}
                                    @if ($member->user->getKey() === auth()->id())
                                        <span class="text-gray-400">(you)</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($canManage && ! $member->isOwner())
                                <form method="POST" action="{{ route('teams.role', $member->user_id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <x-select name="role" class="!w-auto !py-1.5 text-xs" onchange="this.form.submit()">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" @selected($member->role === $role)>{{ ucfirst($role) }}</option>
                                        @endforeach
                                    </x-select>
                                </form>
                            @else
                                <x-core::badge color="brand">{{ ucfirst($member->role) }}</x-core::badge>
                            @endif

                            @if ($canManage && ! $member->isOwner())
                                <button type="button"
                                        @click="$dispatch('confirm', {
                                            action: '{{ route('teams.remove', $member->user_id) }}',
                                            method: 'DELETE',
                                            message: 'Remove {{ $member->user->name }} from this workspace?'
                                        })"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-12m12 0v6m-6 6h9m-9-12l3-3m0 0l3 3m-3-3V4.5" /></svg>
                                    Remove
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">No team members yet.</p>
                @endforelse
            </div>
        </x-core::card>

        @if ($canManage)
            <x-core::card title="Pending invitations" description="Invites that haven't been accepted yet.">
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($invitations as $invitation)
                        <div class="flex items-center justify-between gap-3 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $invitation->email }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Invited by {{ $invitation->inviter?->name ?? 'someone' }}
                                    @if ($invitation->isExpired())
                                        · <span class="font-medium text-amber-600">expired</span>
                                    @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('teams.invite.revoke', $invitation) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs font-semibold text-red-600 transition hover:text-red-500 dark:text-red-400">Revoke</button>
                            </form>
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">No pending invitations.</p>
                    @endforelse
                </div>
            </x-core::card>
        @endif
    </div>

    @if ($canManage)
        <div>
            <x-core::card title="Invite teammate" description="Send an invitation to join your workspace.">
                <form method="POST" action="{{ route('teams.invite') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="email" value="Email address" required />
                        <div class="relative mt-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <x-text-input id="email" name="email" type="email" class="pl-9"
                                          :value="old('email')" placeholder="teammate@example.com" required />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="role" value="Role" />
                        <x-select id="role" name="role" class="mt-1.5">
                            @foreach (['admin', 'member', 'viewer'] as $role)
                                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error class="mt-2" :messages="$errors->get('role')" />
                    </div>

                    <x-primary-button class="w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        Send invitation
                    </x-primary-button>
                </form>
            </x-core::card>
        </div>
    @endif
</div>
</x-core::layouts.app>
