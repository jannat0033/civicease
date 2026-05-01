<x-layouts.app :title="'Report Details'">
<section class="space-y-8">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">{{ $report->title }}</h1>
            <p class="mt-2 text-slate-600">{{ $report->category }} &middot; {{ $report->postcode }} &middot; submitted {{ $report->created_at->format('d M Y H:i') }}</p>
        </div>
        <x-status-badge :status="$report->status" />
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.1fr,0.9fr]">
        <div class="space-y-6">
            <div class="card space-y-4">
                <h2 class="text-xl font-semibold">Issue details</h2>
                <p class="text-slate-700">{{ $report->description }}</p>
                @if($report->address)
                    <p class="text-slate-600"><strong>Address:</strong> {{ $report->address }}</p>
                @endif
                @if($report->additional_notes)
                    <p class="text-slate-600"><strong>Additional notes:</strong> {{ $report->additional_notes }}</p>
                @endif
                @if($report->image_path)
                    <img src="{{ Storage::url($report->image_path) }}" alt="Uploaded issue image" class="w-full rounded-2xl border border-slate-200 object-cover">
                @endif
            </div>

            <div class="card">
                <h2 class="text-xl font-semibold">Status timeline</h2>
                <div class="mt-5 space-y-4">
                    @foreach($report->orderedStatusHistory as $history)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <x-status-badge :status="$history->status" />
                                <p class="text-sm text-slate-500">{{ $history->created_at->format('d M Y H:i') }}</p>
                            </div>
                            @if($history->note)
                                <p class="mt-3 text-slate-700">{{ $history->note }}</p>
                            @endif
                            @if($history->updater)
                                <p class="mt-2 text-sm text-slate-500">Updated by {{ $history->updater->name }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold">Map</h2>
            <div id="report-detail-map" data-lat="{{ $report->latitude }}" data-lng="{{ $report->longitude }}" class="mt-4 h-[420px] rounded-2xl border border-slate-200"></div>
        </div>
    </div>
</section>
</x-layouts.app>
