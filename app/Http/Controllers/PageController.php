<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home(): View
    {
        $stats = [
            'reports' => Report::count(),
            'resolved' => Report::where('status', Report::STATUS_RESOLVED)->count(),
            'inReview' => Report::where('status', Report::STATUS_IN_REVIEW)->count(),
        ];

        return view('pages.home', compact('stats'));
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function accessibility(): View
    {
        return view('pages.accessibility');
    }

    public function help(): View
    {
        return view('pages.help');
    }

    public function communityMap(Request $request): View
    {
        $query = Report::query()
            ->where('status', Report::STATUS_RESOLVED)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest();

        $baseQuery = clone $query;
        $selectedCategory = $request->string('category')->value();

        if (in_array($selectedCategory, Report::categories(), true)) {
            $query->where('category', $selectedCategory);
        } else {
            $selectedCategory = '';
        }

        $reports = $query
            ->get()
            ->map(fn (Report $report): array => $this->toPublicMapReport($report));

        return view('pages.community-map', [
            'reports' => $reports,
            'categories' => Report::categories(),
            'selectedCategory' => $selectedCategory,
            'totalReportCount' => $baseQuery->count(),
        ]);
    }

    public function dashboard(): View
    {
        $user = auth()->user();
        $reports = $user->reports()->latest()->take(5)->get();

        $summary = [
            'total' => $user->reports()->count(),
            'in_review' => $user->reports()->where('status', Report::STATUS_IN_REVIEW)->count(),
            'resolved' => $user->reports()->where('status', Report::STATUS_RESOLVED)->count(),
        ];

        return view('pages.dashboard', compact('reports', 'summary'));
    }

    /**
     * @return array{id:int,title:string,category:string,status:string,latitude:float,longitude:float,created_at:string|null}
     */
    protected function toPublicMapReport(Report $report): array
    {
        return [
            'id' => $report->id,
            'title' => $report->title,
            'category' => $report->category,
            'status' => $report->status,
            'latitude' => round((float) $report->latitude, 3),
            'longitude' => round((float) $report->longitude, 3),
            'created_at' => $report->created_at?->toDateString(),
        ];
    }
}
