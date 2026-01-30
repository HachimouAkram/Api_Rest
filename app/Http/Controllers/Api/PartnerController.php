<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Photo;
use App\Models\Availability;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:partenaire,admin');
    }

    /**
     * Profil du partenaire
     */
    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Modifier profil du partenaire
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour',
            'data' => $user
        ]);
    }

    /**
     * Créer une annonce
     */
    public function createListing(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'max_guests' => 'required|integer|min:1',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
        ]);

        $listing = Listing::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price_per_night' => $request->price_per_night,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'max_guests' => $request->max_guests,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Annonce créée',
            'data' => $listing
        ], 201);
    }

    /**
     * Mes annonces
     */
    public function listings()
    {
        $listings = Listing::where('user_id', Auth::id())
            ->with(['photos', 'availabilities'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $listings
        ]);
    }

    /**
     * Détails d'une annonce
     */
    public function showListing($id)
    {
        $listing = Listing::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['photos', 'availabilities'])
            ->first();

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
     * Modifier une annonce
     */
    public function updateListing(Request $request, $id)
    {
        $listing = Listing::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée'
            ], 404);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price_per_night' => 'sometimes|numeric|min:0',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string',
            'country' => 'sometimes|string',
            'max_guests' => 'sometimes|integer|min:1',
            'bedrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
        ]);

        $listing->update($request->only([
            'title', 'description', 'price_per_night', 'address', 
            'city', 'country', 'max_guests', 'bedrooms', 'bathrooms'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Annonce mise à jour',
            'data' => $listing
        ]);
    }

    /**
     * Supprimer une annonce
     */
    public function deleteListing($id)
    {
        $listing = Listing::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée'
            ], 404);
        }

        $listing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Annonce supprimée'
        ]);
    }

    /**
     * Upload photos
     */
    public function uploadPhotos(Request $request, $id)
    {
        $listing = Listing::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée'
            ], 404);
        }

        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions.*' => 'sometimes|string|max:255'
        ]);

        $photos = [];
        foreach ($request->file('photos') as $index => $photo) {
            $path = $photo->store('listings', 'public');
            $photoModel = Photo::create([
                'listing_id' => $listing->id,
                'path' => $path,
                'caption' => $request->captions[$index] ?? null,
                'order' => $index
            ]);
            $photos[] = $photoModel;
        }

        return response()->json([
            'success' => true,
            'message' => 'Photos uploadées',
            'data' => $photos
        ], 201);
    }

    /**
     * Supprimer photo
     */
    public function deletePhoto($photoId)
    {
        $photo = Photo::whereHas('listing', function($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $photoId)->first();

        if (!$photo) {
            return response()->json([
                'success' => false,
                'message' => 'Photo non trouvée'
            ], 404);
        }

        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Photo supprimée'
        ]);
    }

    /**
     * Définir disponibilités
     */
    public function setAvailability(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_available' => 'boolean',
            'price_per_night' => 'sometimes|numeric|min:0'
        ]);

        $listing = Listing::where('user_id', Auth::id())
            ->where('id', $request->listing_id)
            ->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée'
            ], 404);
        }

        $availability = Availability::create([
            'listing_id' => $request->listing_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_available' => $request->is_available ?? true,
            'price_per_night' => $request->price_per_night
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Disponibilité définie',
            'data' => $availability
        ], 201);
    }

    /**
     * Calendrier disponibilités
     */
    public function getAvailability($listingId)
    {
        $listing = Listing::where('user_id', Auth::id())
            ->where('id', $listingId)
            ->first();

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce non trouvée'
            ], 404);
        }

        $availabilities = Availability::where('listing_id', $listingId)
            ->orderBy('start_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $availabilities
        ]);
    }

    /**
     * Supprimer disponibilité
     */
    public function deleteAvailability($id)
    {
        $availability = Availability::whereHas('listing', function($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $id)->first();

        if (!$availability) {
            return response()->json([
                'success' => false,
                'message' => 'Disponibilité non trouvée'
            ], 404);
        }

        $availability->delete();

        return response()->json([
            'success' => true,
            'message' => 'Disponibilité supprimée'
        ]);
    }

    /**
     * Réservations de mes annonces
     */
    public function bookings()
    {
        $bookings = Booking::whereHas('listing', function($query) {
            $query->where('user_id', Auth::id());
        })->with(['listing', 'user'])
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Vue revenus
     */
    public function revenues()
    {
        $bookings = Booking::whereHas('listing', function($query) {
            $query->where('user_id', Auth::id());
        })->where('status', 'confirmed')
        ->with('listing')
        ->get();

        $totalRevenue = $bookings->sum('total_price');
        $monthlyRevenue = $bookings->groupBy(function($booking) {
            return $booking->created_at->format('Y-m');
        })->map(function($monthBookings) {
            return $monthBookings->sum('total_price');
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'booking_count' => $bookings->count(),
                'recent_bookings' => $bookings->take(10)
            ]
        ]);
    }
}
