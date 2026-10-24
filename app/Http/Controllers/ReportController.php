<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use App\Models\ShipmentRequest;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Show report form with context.
     */
    public function create(Request $request): View
    {
        $targetUser = $request->query('user_id') ? User::find($request->query('user_id')) : null;
        $trip = $request->query('trip_id') ? Trip::find($request->query('trip_id')) : null;
        $shipmentRequest = $request->query('request_id') ? ShipmentRequest::find($request->query('request_id')) : null;

        return view('reports.create', [
            'targetUser' => $targetUser,
            'trip' => $trip,
            'shipmentRequest' => $shipmentRequest,
        ]);
    }

    /**
     * Store a newly created report.
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        Report::create([
            'reporter_id' => auth()->id(),
            'target_user_id' => $request->validated('target_user_id'),
            'trip_id' => $request->validated('trip_id'),
            'shipment_request_id' => $request->validated('shipment_request_id'),
            'reason' => $request->validated('reason'),
            'description' => $request->validated('description'),
            'status' => 'ouvert',
        ]);

        return redirect()->route('dashboard')->with('success', 'Votre signalement a été enregistré et transmis à notre équipe de modération.');
    }
}
