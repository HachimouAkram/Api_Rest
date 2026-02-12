<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Page;
use App\Models\Review;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Liste de toutes les annonces (listings) actives
     */
    public function listings(Request $request)
    {
        $query = Listing::with(['photos', 'user'])
            ->where('is_active', true)
            ->where('is_approved', true);

        // Filtre par ville
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filtre par pays
        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        // Filtre par prix
        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', (float) $request->max_price);
        }

        // Filtre par capacité
        if ($request->filled('guests')) {
            $query->where('max_guests', '>=', (int) $request->guests);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $listings
        ]);
    }

    /**
     * Détails d'une annonce spécifique
     */
    public function showListing($id)
    {
        $listing = Listing::with(['photos', 'availabilities', 'user'])
            ->where('is_active', true)
            ->where('is_approved', true)
            ->find($id);

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $listing
        ]);
    }

    /**
     * Liste des avis pour une annonce
     */
    public function reviews($listingId)
    {
        $reviews = Review::where('listing_id', $listingId)
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Recherche filtrée d'annonces (listings)
     * Paramètres: city, destination, location | min_price, max_price | guests, travelers, capacity | type
     */
    public function search(Request $request)
    {
        $query = Listing::with(['photos', 'availabilities', 'user'])
            ->where('is_active', true)
            ->where('is_approved', true);

        $location = $request->input('city') ?: $request->input('destination') ?: $request->input('location');
        if ($location) {
            $query->where(function ($q) use ($location) {
                $q->where('city', 'like', '%' . $location . '%')
                    ->orWhere('country', 'like', '%' . $location . '%')
                    ->orWhere('address', 'like', '%' . $location . '%');
            });
        }

        $minPrice = $request->input('min_price') ?: $request->input('minPrice');
        if ($minPrice) {
            $query->where('price_per_night', '>=', (float) $minPrice);
        }

        $maxPrice = $request->input('max_price') ?: $request->input('maxPrice');
        if ($maxPrice) {
            $query->where('price_per_night', '<=', (float) $maxPrice);
        }

        $guests = $request->input('guests') ?: $request->input('travelers') ?: $request->input('capacity');
        if ($guests) {
            $query->where('max_guests', '>=', (int) $guests);
        }

        if ($request->filled('type')) {
            // Listing n'a pas de champ type - on peut filtrer via une future colonne ou ignorer
            // $query->where('type', $request->type);
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
        $offer = SpecialOffer::with('listing.photos')
            ->where('is_active', true)
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
        $offers = SpecialOffer::with('listing.photos')
            ->where('is_active', true)
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
