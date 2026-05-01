<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CivicEase') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="mx-auto w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-3">
                    <x-application-logo class="h-12 w-12 text-white" />
                    <div class="text-left">
                        <span class="block text-lg font-bold text-slate-900">CivicEase</span>
                        <span class="block text-xs text-slate-500">Community issue reporting</span>
                    </div>
                </a>
            </div>

            <div class="mx-auto w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-soft sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
