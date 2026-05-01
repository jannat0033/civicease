<x-layouts.app :title="'Admin Dashboard'">
<section class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Admin Dashboard</h1>
            <p class="mt-2 text-slate-600">Review overall system activity and report workflow.</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="btn-primary">Manage reports</a>
    </div>

    <div class="grid gap-6 md:grid-cols-5">
        @foreach($summary as $item)
            <a href="{{ $item['url'] }}" class="card block transition hover:-translate-y-1 hover:border-civic-300 hover:shadow-lg">
                <p class="text-sm text-slate-500">{{ str($item['label'])->replace('_', ' ')->title() }}</p>
                <p class="mt-2 text-3xl font-bold">{{ $item['value'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="card">
        <h2 class="text-xl font-semibold">Recent activity</h2>
        <div class="mt-4 space-y-4">
            @foreach($recentReports as $report)
                <div class="flex flex-col gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-slate-900">#{{ $report->id }}. {{ $report->title }}</p>
                        <p class="text-sm text-slate-500">{{ $report->user->name }} &middot; {{ $report->postcode }} &middot; Submitted {{ $report->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-status-badge :status="$report->status" />
                        <a href="{{ route('admin.reports.show', $report) }}" class="btn-secondary">Review</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
</x-layouts.app>
