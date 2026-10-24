<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportManagementController extends Controller
{
    /**
     * Display a listing of reports for moderation.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'ouvert');

        $query = Report::with(['reporter', 'targetUser', 'trip', 'handler'])
            ->latest();

        if ($status !== 'tous') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(15)->withQueryString();

        $counts = [
            'ouvert' => Report::where('status', 'ouvert')->count(),
            'en_cours' => Report::where('status', 'en_cours')->count(),
            'traite' => Report::where('status', 'traite')->count(),
            'rejete' => Report::where('status', 'rejete')->count(),
            'tous' => Report::count(),
        ];

        return view('admin.reports.index', [
            'reports' => $reports,
            'currentStatus' => $status,
            'counts' => $counts,
        ]);
    }

    /**
     * Display detailed report with moderation tools.
     */
    public function show(Report $report): View
    {
        $report->load(['reporter', 'targetUser', 'trip.groupeur', 'shipmentRequest', 'handler']);

        return view('admin.reports.show', [
            'report' => $report,
        ]);
    }

    /**
     * Update report status and moderation notes.
     */
    public function update(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:ouvert,en_cours,traite,rejete'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'],
            'handled_by' => auth()->id(),
        ]);

        return back()->with('success', 'Le signalement a été mis à jour avec succès.');
    }
}
