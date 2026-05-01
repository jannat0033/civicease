<x-layouts.app :title="'Dashboard'">
<section class="space-y-8">
    @if (session('verification_status'))
        <div class="alert-success">
            {{ session('verification_status') }}
        </div>
    @endif

    @if (session('registration_status'))
        <div class="alert-success">
            {{ session('registration_status') }}
        </div>
    @endif

    <div class="card flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-slate-600">Manage your reports and check progress from one place.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('reports.create') }}" class="btn-primary">Report Issue</a>
            <a href="{{ route('reports.index') }}" class="btn-secondary">My Reports</a>
            <a href="{{ route('community-map') }}" class="btn-secondary">Community Map</a>
            <a href="{{ route('help') }}" class="btn-secondary">Help</a>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="card"><p class="text-sm text-slate-500">Total reports</p><p class="mt-2 text-3xl font-bold">{{ $summary['total'] }}</p></div>
        <div class="card"><p class="text-sm text-slate-500">In review</p><p class="mt-2 text-3xl font-bold">{{ $summary['in_review'] }}</p></div>
        <div class="card"><p class="text-sm text-slate-500">Resolved</p><p class="mt-2 text-3xl font-bold">{{ $summary['resolved'] }}</p></div>
    </div>

    <div class="card">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold">Recent reports</h2>
            <a href="{{ route('reports.index') }}" class="text-sm font-semibold text-civic-700">View all</a>
        </div>
        <div class="space-y-4">
            @forelse($reports as $report)
                <div class="flex flex-col gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-slate-900">{{ $report->title }}</p>
                        <p class="text-sm text-slate-500">{{ $report->category }} &middot; {{ $report->postcode }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-status-badge :status="$report->status" />
                        <a href="{{ route('reports.show', $report) }}" class="btn-secondary">Open</a>
                    </div>
                </div>
            @empty
                <p class="text-slate-600">No reports yet. Submit your first issue to get started.</p>
            @endforelse
        </div>
    </div>
</section>
</x-layouts.app>
