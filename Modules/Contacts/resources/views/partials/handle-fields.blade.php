@php
    $contact ??= null;
@endphp

@foreach ($platforms as $platform)
    @php
        $handle = $contact?->handleFor($platform);
    @endphp
    <div>
        <div class="flex items-center gap-2">
            <x-core::platform-icon :platform="$platform" class="h-4 w-4 text-gray-400" />
            <x-input-label :for="'handle-'.$platform" :value="ucfirst($platform)" />
        </div>
        <div class="mt-1.5 grid gap-3 sm:grid-cols-2">
            <div>
                <x-text-input :id="'handle-'.$platform" name="handles[{{ $platform }}][handle]" class="w-full"
                              :value="old('handles.'.$platform.'.handle', $handle?->handle ?? '')"
                              placeholder="@username" />
                <x-input-error class="mt-1" :messages="$errors->get('handles.'.$platform.'.handle')" />
            </div>
            <div>
                <x-text-input name="handles[{{ $platform }}][platform_uid]" class="w-full"
                              :value="old('handles.'.$platform.'.platform_uid', $handle?->platform_uid ?? '')"
                              placeholder="Platform id / URN" />
                <x-input-error class="mt-1" :messages="$errors->get('handles.'.$platform.'.platform_uid')" />
            </div>
        </div>
        <div class="mt-1.5">
            <x-text-input name="handles[{{ $platform }}][profile_url]" type="url" class="w-full"
                          :value="old('handles.'.$platform.'.profile_url', $handle?->profile_url ?? '')"
                          placeholder="https://..." />
            <x-input-error class="mt-1" :messages="$errors->get('handles.'.$platform.'.profile_url')" />
        </div>
    </div>
@endforeach
