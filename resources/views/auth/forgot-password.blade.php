<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold text-slate-900">Reset your password</h1>
            <p class="text-sm text-slate-600">{{ __('Enter your email address and we will send you a secure reset link.') }}</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="space-y-4 pt-2">
                <x-primary-button class="w-full justify-center">
                    {{ __('Send reset link') }}
                </x-primary-button>

                <p class="text-center text-sm text-slate-600">
                    <a href="{{ route('login') }}" class="font-medium text-civic-700 hover:text-civic-900">{{ __('Back to sign in') }}</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
