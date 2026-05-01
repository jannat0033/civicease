<x-layouts.app :title="'My Reports'">
<section class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">My Reports</h1>
            <p class="mt-2 text-slate-600">Review your submitted issues and track progress.</p>
        </div>
        <a href="{{ route('reports.create') }}" class="btn-primary">New report</a>
    </div>

    <div class="card">
        <form method="GET" class="grid gap-4 md:grid-cols-[1fr,220px,auto]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or category" class="text-input">
            <select name="status" class="text-input">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <button class="btn-secondary" type="submit">Apply</button>
        </form>
    </div>

    <div class="space-y-4">
        @forelse($reports as $report)
            <div class="card flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex gap-4">
                    @if($report->image_path)
                        <img src="{{ Storage::url($report->image_path) }}" alt="Report image" class="h-20 w-20 rounded-xl object-cover">
                    @endif
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ $report->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $report->category }} &middot; {{ $report->postcode }} &middot; {{ $report->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-status-badge :status="$report->status" />
                    <a href="{{ route('reports.show', $report) }}" class="btn-secondary">View</a>
                </div>
            </div>
        @empty
            <div class="card"><p class="text-slate-600">You have not submitted any reports yet.</p></div>
        @endforelse
    </div>

    {{ $reports->links() }}
</section>
</x-layouts.app>
