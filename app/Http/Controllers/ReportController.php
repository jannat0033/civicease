<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReportController extends Controller
{
    private const DUPLICATE_DISTANCE_KM = 0.35;
    private const DUPLICATE_LOOKBACK_DAYS = 60;
    private const SUPPORTED_AREA_BOUNDS = [
        'min_latitude' => 49.8,
        'max_latitude' => 61.0,
        'min_longitude' => -8.7,
        'max_longitude' => 2.1,
    ];

    public function index(Request $request): View
    {
        $query = $request->user()->reports()->latest();

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('search')->value()) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', [
            'reports' => $reports,
            'statuses' => Report::statuses(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Report::class);

        return view('reports.create', [
            'categories' => Report::categories(),
        ]);
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $this->authorize('create', Report::class);

        $data = $request->validated();
        $data['postcode'] = $this->normalizePostcode($data['postcode']);

        $reportLatitude = (float) $data['latitude'];
        $reportLongitude = (float) $data['longitude'];

        $this->ensureCoordinatesAreWithinSupportedArea($reportLatitude, $reportLongitude);

        $postcodeCoordinates = $this->lookupPostcodeCoordinates($data['postcode'])
            ?? $this->fallbackPostcodeCoordinatesFromRequest($request);

        if ($postcodeCoordinates !== null) {
            [$postcodeLatitude, $postcodeLongitude] = $postcodeCoordinates;

            $this->ensureCoordinatesAreWithinSupportedArea($postcodeLatitude, $postcodeLongitude);
            $this->ensurePinIsNearPostcode(
                $postcodeLatitude,
                $postcodeLongitude,
                $reportLatitude,
                $reportLongitude,
            );
        }

        $possibleDuplicates = $this->findPossibleDuplicates(
            $data['category'],
            $data['postcode'],
            $reportLatitude,
            $reportLongitude,
        );

        if ($possibleDuplicates->isNotEmpty() && ! $request->boolean('confirm_duplicate_submission')) {
            return back()
                ->withInput()
                ->withErrors([
                    'title' => 'A very similar report has already been submitted nearby. Please review the possible duplicate below before sending another report.',
                ])
                ->with('duplicateReports', $possibleDuplicates->map(fn (Report $report): array => [
                    'id' => $report->id,
                    'title' => $report->title,
                    'status' => $report->status,
                    'postcode' => $report->postcode,
                    'created_at' => $report->created_at?->format('d M Y H:i'),
                    'distance_km' => round($this->distanceInKm(
                        $reportLatitude,
                        $reportLongitude,
                        (float) $report->latitude,
                        (float) $report->longitude,
                    ), 2),
                ])->all());
        }

        $data['user_id'] = $request->user()->id;
        $data['status'] = Report::STATUS_SUBMITTED;

        $report = null;
        $imagePath = null;

        try {
            DB::transaction(function () use (&$data, &$imagePath, &$report, $request): void {
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('reports', 'public');
                    $data['image_path'] = $imagePath;
                }

                $report = Report::create($data);

                $report->statusHistory()->create([
                    'status' => Report::STATUS_SUBMITTED,
                    'note' => 'Report submitted by resident.',
                    'updated_by' => $request->user()->id,
                ]);
            });
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()->route('reports.show', $report)
            ->with('status', 'Your issue report has been submitted.');
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        $report->load(['user', 'orderedStatusHistory.updater']);

        return view('reports.show', compact('report'));
    }

    protected function normalizePostcode(string $postcode): string
    {
        $postcode = strtoupper(trim($postcode));

        return preg_replace('/\s+/', ' ', $postcode) ?? $postcode;
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    protected function lookupPostcodeCoordinates(string $postcode): ?array
    {
        $response = $this->performPostcodeLookupRequest($postcode);

        if ($response === null) {
            return null;
        }

        $latitude = data_get($response->json(), 'result.latitude');
        $longitude = data_get($response->json(), 'result.longitude');

        if (! $response->ok() || ! is_numeric($latitude) || ! is_numeric($longitude)) {
            throw ValidationException::withMessages([
                'postcode' => 'Please enter a valid UK postcode.',
            ]);
        }

        return [(float) $latitude, (float) $longitude];
    }

    protected function performPostcodeLookupRequest(string $postcode): ?Response
    {
        $endpoint = 'https://api.postcodes.io/postcodes/' . rawurlencode($postcode);

        try {
            return Http::timeout(10)->acceptJson()->get($endpoint);
        } catch (ConnectionException $exception) {
            Log::warning('Primary postcode lookup failed.', [
                'postcode' => $postcode,
                'message' => $exception->getMessage(),
            ]);

            if (! app()->environment('local')) {
                return null;
            }
        }

        try {
            Log::warning('Retrying postcode lookup without SSL verification in local environment.', [
                'postcode' => $postcode,
            ]);

            return Http::timeout(10)
                ->acceptJson()
                ->withoutVerifying()
                ->get($endpoint);
        } catch (ConnectionException $exception) {
            Log::warning('Local postcode lookup retry failed.', [
                'postcode' => $postcode,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    protected function fallbackPostcodeCoordinatesFromRequest(StoreReportRequest $request): ?array
    {
        $latitude = $request->input('postcode_lookup_latitude');
        $longitude = $request->input('postcode_lookup_longitude');

        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return null;
        }

        return [(float) $latitude, (float) $longitude];
    }

    protected function ensureCoordinatesAreWithinSupportedArea(float $latitude, float $longitude): void
    {
        $isWithinBounds = $latitude >= self::SUPPORTED_AREA_BOUNDS['min_latitude']
            && $latitude <= self::SUPPORTED_AREA_BOUNDS['max_latitude']
            && $longitude >= self::SUPPORTED_AREA_BOUNDS['min_longitude']
            && $longitude <= self::SUPPORTED_AREA_BOUNDS['max_longitude'];

        if ($isWithinBounds) {
            return;
        }

        throw ValidationException::withMessages([
            'latitude' => 'The selected location is outside the supported council reporting area.',
            'longitude' => 'The selected location is outside the supported council reporting area.',
        ]);
    }

    protected function ensurePinIsNearPostcode(
        float $postcodeLatitude,
        float $postcodeLongitude,
        float $reportLatitude,
        float $reportLongitude,
    ): void {
        $distanceInKm = $this->distanceInKm(
            $postcodeLatitude,
            $postcodeLongitude,
            $reportLatitude,
            $reportLongitude,
        );

        if ($distanceInKm <= 2.0) {
            return;
        }

        throw ValidationException::withMessages([
            'latitude' => 'The selected map pin is too far from the postcode location.',
            'longitude' => 'The selected map pin is too far from the postcode location.',
        ]);
    }

    /**
     * @return Collection<int, Report>
     */
    protected function findPossibleDuplicates(
        string $category,
        string $postcode,
        float $latitude,
        float $longitude,
    ): Collection {
        $coordinateDelta = 0.01;
        $cutoff = Carbon::now()->subDays(self::DUPLICATE_LOOKBACK_DAYS);

        return Report::query()
            ->where('category', $category)
            ->where('postcode', $postcode)
            ->where('status', '!=', Report::STATUS_REJECTED)
            ->where('created_at', '>=', $cutoff)
            ->whereBetween('latitude', [$latitude - $coordinateDelta, $latitude + $coordinateDelta])
            ->whereBetween('longitude', [$longitude - $coordinateDelta, $longitude + $coordinateDelta])
            ->latest()
            ->get()
            ->filter(fn (Report $report): bool => $this->distanceInKm(
                $latitude,
                $longitude,
                (float) $report->latitude,
                (float) $report->longitude,
            ) <= self::DUPLICATE_DISTANCE_KM)
            ->values();
    }

    protected function distanceInKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
