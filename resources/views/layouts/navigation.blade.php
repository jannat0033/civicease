<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <nav x-data="mobileMenu" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-civic-600 font-bold text-white">CE</span>
                <div>
                    <span class="block text-lg font-bold text-slate-900">CivicEase</span>
                    <span class="block text-xs text-slate-500">Community issue reporting</span>
                </div>
            </a>

            <div class="hidden items-center gap-6 md:flex">
                <a href="{{ route('home') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Home</a>
                <a href="{{ route('about') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">About</a>
                <a href="{{ route('privacy') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Privacy</a>
                <a href="{{ route('accessibility') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Accessibility</a>
                <a href="{{ route('community-map') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Community Map</a>
                <a href="{{ route('help') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Help</a>

                @auth
                    <a href="{{ route('reports.index') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">My Reports</a>
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Dashboard</a>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Admin</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-700 hover:text-civic-700">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-civic-700">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Register</a>
                @endauth
            </div>

            <button @click="open = !open" class="rounded-lg border border-slate-300 p-2 md:hidden">
                <span class="sr-only">Toggle menu</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>
        </div>

        <div x-show="open" x-cloak class="space-y-2 border-t border-slate-200 py-4 md:hidden">
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Home</a>
            <a href="{{ route('about') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">About</a>
            <a href="{{ route('privacy') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Privacy</a>
            <a href="{{ route('accessibility') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Accessibility</a>
            <a href="{{ route('community-map') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Community Map</a>
            <a href="{{ route('help') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Help</a>

            @auth
                <a href="{{ route('reports.index') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">My Reports</a>
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Dashboard</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Admin</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left hover:bg-slate-100">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Login</a>
                <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Register</a>
            @endauth
        </div>
    </nav>
</header>
