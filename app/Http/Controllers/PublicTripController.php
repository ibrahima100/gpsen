<?php

namespace App\Http\Controllers;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Services\TripCapacityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicTripController extends Controller
{
    public function __construct(
        protected TripCapacityService $capacityService
    ) {}

    /**
     * Page d'accueil publique complète selon UI/UX §5.1.
     */
    public function home(): View
    {
        $recentTrips = Trip::with(['groupeur.groupeurProfile'])
            ->where('status', TripStatus::ouvert)
            ->where('departure_date', '>=', now()->toDateString())
            ->orderBy('departure_date', 'asc')
            ->take(6)
            ->get();

        return view('welcome', [
            'recentTrips' => $recentTrips,
            'capacityService' => $this->capacityService,
        ]);
    }

    /**
     * Liste des trajets avec filtres et pagination selon UI/UX §5.4.
     */
    public function index(Request $request): View
    {
        $query = Trip::with(['groupeur.groupeurProfile'])
            ->where('status', TripStatus::ouvert)
            ->where('departure_date', '>=', now()->toDateString());

        if ($origin = $request->input('origin')) {
            $query->where(function ($q) use ($origin) {
                $q->where('origin_city', 'like', "%{$origin}%")
                    ->orWhere('origin_country', 'like', "%{$origin}%");
            });
        }

        if ($destination = $request->input('destination')) {
            $query->where(function ($q) use ($destination) {
                $q->where('destination_city', 'like', "%{$destination}%")
                    ->orWhere('destination_country', 'like', "%{$destination}%");
            });
        }

        if ($date = $request->input('date')) {
            $query->where('departure_date', '>=', $date);
        }

        $trips = $query->orderBy('departure_date', 'asc')->paginate(9)->withQueryString();

        return view('trajets.index', [
            'trips' => $trips,
            'capacityService' => $this->capacityService,
            'filters' => $request->only(['origin', 'destination', 'date']),
        ]);
    }

    /**
     * Détail d'un trajet selon UI/UX §5.5.
     */
    public function show(Trip $trip): View
    {
        $trip->load(['groupeur.groupeurProfile']);
        $remainingCapacity = $this->capacityService->getRemainingCapacity($trip);
        $recentReviews = \App\Models\Review::with('author')
            ->where('target_id', $trip->groupeur_id)
            ->latest()
            ->take(3)
            ->get();

        return view('trajets.show', [
            'trip' => $trip,
            'remainingCapacity' => $remainingCapacity,
            'capacityService' => $this->capacityService,
            'recentReviews' => $recentReviews,
        ]);
    }
}
