<x-layouts.app :title="'Admin Report Detail'">
<section class="space-y-8">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">{{ $report->title }}</h1>
            <p class="mt-2 text-slate-600">Submitted by {{ $report->user->name }} &middot; {{ $report->user->email }}</p>
        </div>
        <x-status-badge :status="$report->status" />
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.05fr,0.95fr]">
        <div class="space-y-6">
            <div class="card space-y-4">
                <h2 class="text-xl font-semibold">Full report</h2>
                <p class="text-slate-700">{{ $report->description }}</p>
                <p class="text-slate-600"><strong>Category:</strong> {{ $report->category }}</p>
                <p class="text-slate-600"><strong>Postcode:</strong> {{ $report->postcode }}</p>
                @if($report->address)
                    <p class="text-slate-600"><strong>Address:</strong> {{ $report->address }}</p>
                @endif
                @if($report->image_path)
                    <img src="{{ Storage::url($report->image_path) }}" alt="Report image" class="w-full rounded-2xl border border-slate-200 object-cover">
                @endif
            </div>

            <div class="card">
                <h2 class="text-xl font-semibold">Status history</h2>
                <div class="mt-5 space-y-4">
                    @foreach($report->orderedStatusHistory as $history)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between">
                                <x-status-badge :status="$history->status" />
                                <span class="text-sm text-slate-500">{{ $history->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($history->note)
                                <p class="mt-3 text-slate-700">{{ $history->note }}</p>
                            @endif
                            <p class="mt-2 text-sm text-slate-500">{{ $history->updater?->name ?? 'System' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <h2 class="text-xl font-semibold">Update status</h2>
                <form method="POST" action="{{ route('admin.reports.status.update', $report) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="input-label" for="status">Status</label>
                        <select id="status" name="status" class="text-input">
                            @foreach(\App\Models\Report::statuses() as $status)
                                <option value="{{ $status }}" @selected(old('status', $report->status) === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="input-label" for="note">Admin note</label>
                        <textarea id="note" name="note" rows="5" class="text-input">{{ old('note') }}</textarea>
                        @error('note') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn-primary">Save update</button>
                </form>
            </div>

            <div class="card">
                <h2 class="text-xl font-semibold">Location</h2>
                <div id="report-detail-map" data-lat="{{ $report->latitude }}" data-lng="{{ $report->longitude }}" class="mt-4 h-[360px] rounded-2xl border border-slate-200"></div>
            </div>
        </div>
    </div>
</section>
</x-layouts.app>
