<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold text-slate-900">Create your account</h1>
            <p class="text-sm text-slate-600">Set up a resident account to submit reports, follow status updates, and manage your profile.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="name" :value="__('Full name')" />
                <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="postcode" :value="__('Postcode')" />
                    <x-text-input id="postcode" class="mt-1 block w-full" type="text" name="postcode" :value="old('postcode')" autocomplete="postal-code" />
                    <x-input-error :messages="$errors->get('postcode')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="address" :value="__('Address')" />
                    <x-text-input id="address" class="mt-1 block w-full" type="text" name="address" :value="old('address')" autocomplete="street-address" />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm password')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="space-y-4 pt-2">
                <x-primary-button class="w-full justify-center">
                    {{ __('Create account') }}
                </x-primary-button>

                <p class="text-center text-sm text-slate-600">
                    {{ __('Already registered?') }}
                    <a href="{{ route('login') }}" class="font-medium text-civic-700 hover:text-civic-900">{{ __('Sign in') }}</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
