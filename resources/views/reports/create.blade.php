<x-layouts.app :title="'Report Issue'">
<section class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Report Issue</h1>
        <p class="mt-2 text-slate-600">Submit a local problem with a precise location and optional image.</p>
    </div>

    @if (session('duplicateReports'))
        <div class="alert-info">
            <p class="font-semibold">Possible duplicate report found nearby.</p>
            <p class="mt-2">CivicEase found a recent report with the same category and postcode close to your selected map pin.</p>
            <div class="mt-4 space-y-3">
                @foreach (session('duplicateReports') as $duplicateReport)
                    <div class="rounded-xl border border-civic-200 bg-white p-4">
                        <p class="font-semibold text-slate-900">{{ $duplicateReport['title'] }}</p>
                        <p class="mt-1 text-sm text-slate-600">Status: {{ str_replace('_', ' ', ucfirst($duplicateReport['status'])) }} | Postcode: {{ $duplicateReport['postcode'] }} | Reported: {{ $duplicateReport['created_at'] }} | Approx. distance: {{ $duplicateReport['distance_km'] }} km</p>
                        <a href="{{ route('reports.show', $duplicateReport['id']) }}" class="mt-3 inline-flex text-sm font-medium text-civic-700 hover:text-civic-900">View existing report</a>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                <button type="submit" name="confirm_duplicate_submission" value="1" form="report-create-form" class="btn-primary">Submit anyway</button>
                <p class="text-sm text-slate-600">Use this only if your issue is genuinely different from the report above.</p>
            </div>
        </div>
    @endif

    <form id="report-create-form" method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="grid gap-8 lg:grid-cols-[1.2fr,0.8fr]">
        @csrf
        <input type="hidden" id="postcode_lookup_latitude" name="postcode_lookup_latitude" value="{{ old('postcode_lookup_latitude') }}">
        <input type="hidden" id="postcode_lookup_longitude" name="postcode_lookup_longitude" value="{{ old('postcode_lookup_longitude') }}">

        <div class="card space-y-5">
            <div>
                <label class="input-label" for="category">Issue category</label>
                <select id="category" name="category" class="text-input">
                    <option value="">Choose category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                @error('category') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="input-label" for="title">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="text-input">
                @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="input-label" for="description">Description</label>
                <textarea id="description" name="description" rows="5" class="text-input">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="input-label" for="postcode">Postcode</label>
                    <div class="flex gap-3">
                        <input id="postcode" name="postcode" type="text" value="{{ old('postcode') }}" @class([
                            'text-input',
                            'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' => $errors->has('postcode'),
                        ])>
                        <button type="button" data-postcode-lookup class="btn-secondary whitespace-nowrap">Find</button>
                    </div>
                    <p id="postcode-status" class="mt-2 text-sm text-slate-500">Use a UK postcode to place the map.</p>
                    @error('postcode') <p id="postcode-error" class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="input-label" for="address">Address or location</label>
                    <input id="address" name="address" type="text" value="{{ old('address') }}" class="text-input">
                    @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="input-label" for="latitude">Latitude</label>
                    <input id="latitude" name="latitude" type="text" value="{{ old('latitude') }}" class="text-input">
                    @error('latitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="input-label" for="longitude">Longitude</label>
                    <input id="longitude" name="longitude" type="text" value="{{ old('longitude') }}" class="text-input">
                    @error('longitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="input-label" for="image">Image upload</label>
                <input id="image" name="image" type="file" accept="image/*" class="text-input">
                <div class="mt-3 flex flex-wrap gap-3">
                    <button type="button" data-camera-open class="btn-secondary">Use device camera</button>
                    <button type="button" data-camera-hide class="hidden text-sm font-medium text-slate-600 hover:text-slate-900">Close camera panel</button>
                </div>
                <p class="mt-2 text-sm text-slate-500">You can upload a photo or capture one from your device. CivicEase only asks for camera permission after you choose to enable it.</p>
                <div data-report-image-preview-wrapper class="mt-4 hidden rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-slate-700">Selected image preview</p>
                    <img data-report-image-preview alt="Selected report preview" class="mt-3 max-h-64 rounded-2xl border border-slate-200 object-contain">
                </div>
                <div data-camera-panel class="mt-4 hidden rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div data-camera-permission-step class="space-y-3">
                        <h3 class="text-lg font-semibold text-slate-900">Camera access</h3>
                        <p class="text-sm text-slate-600">When you continue, your browser will ask whether CivicEase can use your camera. The camera is only used to capture a photo for this report.</p>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" data-camera-request class="btn-primary">Allow and open camera</button>
                            <button type="button" data-camera-cancel class="btn-secondary">Not now</button>
                        </div>
                    </div>
                    <div data-camera-live class="hidden space-y-4">
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-900">
                            <video data-camera-video class="aspect-[4/3] w-full object-cover" autoplay playsinline muted></video>
                        </div>
                        <canvas data-camera-canvas class="hidden"></canvas>
                        <div class="flex flex-wrap gap-3">
                            <button type="button" data-camera-capture class="btn-primary">Capture photo</button>
                            <button type="button" data-camera-stop class="btn-secondary">Stop camera</button>
                        </div>
                    </div>
                    <p data-camera-status class="mt-4 text-sm text-slate-600" aria-live="polite"></p>
                </div>
                @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="input-label" for="additional_notes">Additional notes</label>
                <textarea id="additional_notes" name="additional_notes" rows="4" class="text-input">{{ old('additional_notes') }}</textarea>
                @error('additional_notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <button class="btn-primary" type="submit">Submit report</button>
        </div>

        <div class="card">
            <h2 class="text-xl font-semibold">Location map</h2>
            <p class="mt-2 text-sm text-slate-600">After postcode lookup, drag the pin or click on the map to refine the location.</p>
            <div id="report-map" class="mt-4 h-[520px] rounded-2xl border border-slate-200"></div>
        </div>
    </form>
</section>
</x-layouts.app>
