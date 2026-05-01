<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim(($title ?? '') . ' | ' . config('app.name', 'CivicEase'), ' |') }}</title>
    <meta name="description" content="CivicEase helps residents report local issues and track progress.">
    <link rel="preconnect" href="https://unpkg.com">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen flex-col">
        @include('layouts.navigation')

        @isset($header)
            <header class="border-b border-slate-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="alert-success mb-6">{{ session('status') }}</div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>&copy; {{ now()->year }} CivicEase. Local issue reporting made simpler.</p>
                <div class="flex gap-4">
                    <a href="{{ route('about') }}" class="hover:text-civic-700">About</a>
                    <a href="{{ route('privacy') }}" class="hover:text-civic-700">Privacy</a>
                    <a href="{{ route('terms') }}" class="hover:text-civic-700">Terms</a>
                    <a href="{{ route('accessibility') }}" class="hover:text-civic-700">Accessibility</a>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
</body>
</html>
