<x-layouts.app :title="'Manage Reports'">
<section class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Manage Reports</h1>
            <p class="mt-2 text-slate-600">Filter, search, and review all submitted reports.</p>
        </div>
    </div>

    <div class="card">
        <p class="text-sm text-slate-500">
            Showing {{ $reports->total() }} report{{ $reports->total() === 1 ? '' : 's' }}
            @if (request('status'))
                for status: <span class="font-semibold text-slate-700">{{ str(request('status'))->replace('_', ' ')->title() }}</span>
            @else
                across all statuses
            @endif
        </p>
    </div>

    <div class="card">
        <form method="GET" class="grid gap-4 lg:grid-cols-[1fr,220px,220px,auto]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, postcode, resident" class="text-input">
            <select name="status" class="text-input">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <select name="category" class="text-input">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <button class="btn-secondary" type="submit">Apply</button>
        </form>
    </div>

    <div class="space-y-4">
        @foreach($reports as $report)
            <div class="card flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $report->title }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $report->user->name }} &middot; {{ $report->category }} &middot; {{ $report->postcode }} &middot; {{ $report->created_at->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-status-badge :status="$report->status" />
                    <a href="{{ route('admin.reports.show', $report) }}" class="btn-secondary">Open</a>
                </div>
            </div>
        @endforeach
    </div>

    {{ $reports->links() }}
</section>
</x-layouts.app>
