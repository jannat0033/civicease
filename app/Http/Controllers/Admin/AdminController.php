<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Contracts\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $summary = [
            [
                'label' => 'total',
                'value' => Report::count(),
                'url' => route('admin.reports.index'),
            ],
            [
                'label' => 'submitted',
                'value' => Report::where('status', Report::STATUS_SUBMITTED)->count(),
                'url' => route('admin.reports.index', ['status' => Report::STATUS_SUBMITTED]),
            ],
            [
                'label' => 'in_review',
                'value' => Report::where('status', Report::STATUS_IN_REVIEW)->count(),
                'url' => route('admin.reports.index', ['status' => Report::STATUS_IN_REVIEW]),
            ],
            [
                'label' => 'resolved',
                'value' => Report::where('status', Report::STATUS_RESOLVED)->count(),
                'url' => route('admin.reports.index', ['status' => Report::STATUS_RESOLVED]),
            ],
            [
                'label' => 'rejected',
                'value' => Report::where('status', Report::STATUS_REJECTED)->count(),
                'url' => route('admin.reports.index', ['status' => Report::STATUS_REJECTED]),
            ],
        ];

        $recentReports = Report::with('user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('summary', 'recentReports'));
    }
}
