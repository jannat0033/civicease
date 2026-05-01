<x-guest-layout>
    <div class="space-y-6">
        <div class="space-y-2 text-center">
            <h1 class="text-3xl font-bold text-slate-900">Verify your email</h1>
            <p class="text-sm text-slate-600">{{ __('Before you get started, confirm your email address using the link we just sent you.') }}</p>
        </div>

        <div class="alert-info">
            {{ __('Email verification keeps updates and account recovery tied to the right resident account.') }}
        </div>

        @if (session('registration_status'))
            <div class="alert-success">
                {{ session('registration_status') }}
            </div>
        @endif

        @if (session('status') == 'verification-link-sent')
            <div class="alert-success">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <x-primary-button>
                    {{ __('Resend verification email') }}
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="text-sm font-medium text-slate-600 hover:text-slate-900">
                    {{ __('Log out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
