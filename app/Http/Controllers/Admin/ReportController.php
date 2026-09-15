<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const STATUSES = ['OUVERT', 'EN_COURS', 'RESOLU', 'REJETE'];

    public function index(Request $request): View
    {
        $reports = Report::with(['client.user', 'order'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'statuses' => self::STATUSES,
            'currentStatus' => $request->string('status')->toString(),
        ]);
    }

    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:'.implode(',', self::STATUSES)],
        ]);

        $report->update(['status' => $validated['status']]);

        return back()->with('status', 'Statut du signalement mis à jour.');
    }
}
