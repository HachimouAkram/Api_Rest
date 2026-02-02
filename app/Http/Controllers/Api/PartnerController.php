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
        /** @var \App\Models\User $user */
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        $user->save();

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
        try {
            $listings = Listing::where('user_id', Auth::id())
                ->with(['photos', 'availabilities'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $listings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des annonces',
                'error' => $e->getMessage()
            ], 500);
        }
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
        })->whereIn('status', ['confirmed', 'completed'])
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

    /**
     * Statistiques du dashboard
     */
    public function dashboardStats()
    {
        try {
            $userId = Auth::id();
            
            // Nombre total d'annonces
            $totalListings = Listing::where('user_id', $userId)->count();
            
            // Nombre d'annonces actives
            $activeListings = Listing::where('user_id', $userId)
                ->where('is_active', true)
                ->count();
            
            // Nombre total de réservations
            $totalBookings = Booking::whereHas('listing', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->count();
            
            // Réservations en attente
            $pendingBookings = Booking::whereHas('listing', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->where('status', 'pending')->count();
            
            // Réservations confirmées
            $confirmedBookings = Booking::whereHas('listing', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->where('status', 'confirmed')->count();
            
            // Réservations terminées
            $completedBookings = Booking::whereHas('listing', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->where('status', 'completed')->count();
            
            // Revenus totaux
            $totalRevenue = Booking::whereHas('listing', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');
            
            // Revenus du mois en cours
            $currentMonthRevenue = Booking::whereHas('listing', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereIn('status', ['confirmed', 'completed'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

            return response()->json([
                'success' => true,
                'data' => [
                    'listings' => [
                        'total' => $totalListings,
                        'active' => $activeListings,
                        'inactive' => $totalListings - $activeListings
                    ],
                    'bookings' => [
                        'total' => $totalBookings,
                        'pending' => $pendingBookings,
                        'confirmed' => $confirmedBookings,
                        'completed' => $completedBookings
                    ],
                    'revenue' => [
                        'total' => (float) $totalRevenue,
                        'current_month' => (float) $currentMonthRevenue
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
