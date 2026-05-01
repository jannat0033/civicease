<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold text-slate-900">Sign in</h1>
            <p class="text-sm text-slate-600">Access your CivicEase dashboard to report issues and track progress.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')" />
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-civic-700 hover:text-civic-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-civic-600 shadow-sm focus:ring-civic-500" name="remember">
                <span class="text-sm text-slate-600">{{ __('Keep me signed in on this device') }}</span>
            </label>

            <div class="space-y-4 pt-2">
                <x-primary-button class="w-full justify-center">
                    {{ __('Log in') }}
                </x-primary-button>

                <p class="text-center text-sm text-slate-600">
                    {{ __('New to CivicEase?') }}
                    <a href="{{ route('register') }}" class="font-medium text-civic-700 hover:text-civic-900">{{ __('Create an account') }}</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
