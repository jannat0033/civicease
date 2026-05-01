<x-layouts.app :title="'Home'">
<section class="grid gap-8 lg:grid-cols-2 lg:items-center">
    <div class="space-y-6">
        <span class="inline-flex rounded-full bg-civic-100 px-3 py-1 text-sm font-semibold text-civic-700">Trusted local reporting</span>
        <div class="space-y-4">
            <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Report local issues easily with CivicEase.</h1>
            <p class="max-w-2xl text-lg text-slate-600">Help improve your neighbourhood by reporting potholes, broken streetlights, blocked drains, graffiti, and more. Track progress from submission to resolution.</p>
        </div>
        <div class="flex flex-wrap gap-4">
            @auth
                <a href="{{ route('reports.create') }}" class="btn-primary">Report an issue</a>
                <a href="{{ route('community-map') }}" class="btn-secondary">View community map</a>
            @else
                <a href="{{ route('register') }}" class="btn-primary">Create an account</a>
                <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
            @endauth
        </div>
    </div>

    <div class="card">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Reports logged</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['reports'] }}</p></div>
            <div class="rounded-2xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Resolved</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['resolved'] }}</p></div>
            <div class="rounded-2xl bg-slate-50 p-4"><p class="text-sm text-slate-500">In review</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['inReview'] }}</p></div>
        </div>
        <div class="mt-6 rounded-2xl border border-dashed border-civic-200 bg-civic-50 p-5 text-sm text-civic-900">
            A clear civic interface designed for fast reporting, safer streets, and better visibility for residents and councils.
        </div>
    </div>
</section>

<section class="mt-16 grid gap-6 md:grid-cols-3">
    <div class="card"><h2 class="text-xl font-semibold">Report issue</h2><p class="mt-3 text-slate-600">Submit a detailed issue with category, location, map pin, and optional photo.</p></div>
    <div class="card"><h2 class="text-xl font-semibold">Track progress</h2><p class="mt-3 text-slate-600">Follow status changes from submitted through review to resolution.</p></div>
    <div class="card"><h2 class="text-xl font-semibold">View community map</h2><p class="mt-3 text-slate-600">See reported issues across the wider area without exposing private details.</p></div>
</section>

<section class="mt-16">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-slate-900">How it works</h2>
        <p class="mt-2 text-slate-600">A simple three-step reporting flow.</p>
    </div>
    <div class="grid gap-6 md:grid-cols-3">
        <div class="card"><div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-civic-600 text-white">1</div><h3 class="font-semibold">Submit the issue</h3><p class="mt-2 text-slate-600">Choose a category, describe the problem, and confirm the location on the map.</p></div>
        <div class="card"><div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-civic-600 text-white">2</div><h3 class="font-semibold">Council reviews it</h3><p class="mt-2 text-slate-600">Admins check the report, add notes, and move it through the workflow.</p></div>
        <div class="card"><div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-civic-600 text-white">3</div><h3 class="font-semibold">Track updates</h3><p class="mt-2 text-slate-600">Residents can revisit their report and see a clear status timeline.</p></div>
    </div>
</section>
</x-layouts.app>
