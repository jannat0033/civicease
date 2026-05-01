<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">{{ __('Profile') }}</h1>
            <p class="mt-2 text-slate-600">Manage your contact details, password, and account security settings.</p>
        </div>
    </x-slot>

    <section class="space-y-6">
        <div class="card">
            <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="card">
            <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="card border-red-100">
            <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
            </div>
        </div>
    </section>
</x-app-layout>
