<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold text-slate-900">Confirm your password</h1>
            <p class="text-sm text-slate-600">{{ __('This is a secure area of CivicEase. Re-enter your password before continuing.') }}</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                {{ __('Confirm') }}
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
