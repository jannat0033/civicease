<x-layouts.app :title="'Community Map'">
<section class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Community Map</h1>
        <p class="mt-2 text-slate-600">A public view of issue locations and statuses. No private resident details are shown.</p>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('community-map') }}" data-community-filter-form class="grid gap-4 md:grid-cols-[1fr,auto,auto]">
            <div>
                <label class="input-label" for="community-category">Category</label>
                <select id="community-category" name="category" data-community-filter="category" class="text-input">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full md:w-auto">Apply filters</button>
            </div>

            <div class="flex items-end">
                <a href="{{ route('community-map') }}" class="btn-secondary w-full md:w-auto">Reset filters</a>
            </div>
        </form>

        <div class="mt-4 flex flex-col gap-2 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
            <p data-community-map-results data-total-report-count="{{ $totalReportCount }}" aria-live="polite">Showing {{ $reports->count() }} of {{ $totalReportCount }} resolved public reports.</p>
            <p>Only resolved reports are shown, with approximate public map locations.</p>
        </div>
    </div>

    <div class="card">
        <div id="community-map" data-reports='@json($reports)' class="h-[620px] rounded-2xl border border-slate-200"></div>
        <p data-community-map-empty class="mt-4 hidden text-sm text-slate-600">No reports match the current filters.</p>
    </div>
</section>
</x-layouts.app>
