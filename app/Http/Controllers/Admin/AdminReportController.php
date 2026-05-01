<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReportStatusRequest;
use App\Models\Report;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Report::class);

        $query = Report::with('user')->latest();

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($category = $request->string('category')->value()) {
            $query->where('category', $category);
        }

        if ($search = $request->string('search')->value()) {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('postcode', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $reports = $query->paginate(12)->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'statuses' => Report::statuses(),
            'categories' => Report::categories(),
        ]);
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        $report->load(['user', 'orderedStatusHistory.updater']);

        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(UpdateReportStatusRequest $request, Report $report): RedirectResponse
    {
        $this->authorize('update', $report);

        $data = $request->validated();

        DB::transaction(function () use ($data, $report, $request): void {
            $report->update([
                'status' => $data['status'],
            ]);

            $report->statusHistory()->create([
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
                'updated_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('admin.reports.show', $report)
            ->with('status', 'Report status updated successfully.');
    }
}
