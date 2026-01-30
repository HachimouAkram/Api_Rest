<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Page;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Recherche filtrée d'annonces
     */
    public function search(Request $request)
    {
        $query = Listing::with(['photos', 'user'])
            ->where('is_active', true);

        // Filtrage par ville
        if ($request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filtrage par prix
        if ($request->min_price) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        // Filtrage par nombre de voyageurs
        if ($request->guests) {
            $query->where('max_guests', '>=', $request->guests);
        }

        $listings = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $listings
        ]);
    }

    /**
     * Afficher une page légale par son slug
     */
    public function page($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    /**
     * Détails d'une offre spéciale
     */
    public function specialOffer($id)
    {
        $offer = SpecialOffer::where('is_active', true)
            ->where('id', $id)
            ->first();

        if (!$offer) {
            return response()->json([
                'success' => false,
                'message' => 'Offre non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $offer
        ]);
    }

    /**
     * Liste des offres spéciales actives
     */
    public function specialOffers()
    {
        $offers = SpecialOffer::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $offers
        ]);
    }
}
