<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Listing;
use App\Models\Booking;
use App\Models\Review;
use App\Models\SpecialOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    /**
     * Statistiques du dashboard admin
     */
    public function dashboardStats()
    {
        try {
            // Nombre total d'utilisateurs
            $totalUsers = User::count();
            
            // Nombre de partenaires
            $totalPartners = User::where('role', 'partenaire')->count();
            
            // Nombre total d'annonces
            $totalListings = Listing::count();
            
            // Nombre d'annonces actives
            $activeListings = Listing::where('is_active', true)->count();
            
            // Nombre total de réservations
            $totalBookings = Booking::count();
            
            // Réservations en attente
            $pendingBookings = Booking::where('status', 'pending')->count();
            
            // Réservations confirmées
            $confirmedBookings = Booking::where('status', 'confirmed')->count();
            
            // Réservations terminées
            $completedBookings = Booking::where('status', 'completed')->count();
            
            // Revenus totaux
            $totalRevenue = Booking::whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price');
            
            // Revenus du mois en cours
            $currentMonthRevenue = Booking::whereIn('status', ['confirmed', 'completed'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_price');

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => [
                        'total' => $totalUsers,
                        'partners' => $totalPartners,
                        'admins' => User::where('role', 'admin')->count()
                    ],
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

    /**
     * Liste des utilisateurs
     */
    public function users(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $users = User::withCount(['listings', 'bookings'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détails d'un utilisateur
     */
    public function showUser($id)
    {
        try {
            $user = User::with(['listings', 'bookings'])->find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'role' => 'sometimes|in:admin,partenaire,client'
            ]);

            $user->update($request->only(['name', 'email', 'role']));

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }

            // Ne pas permettre la suppression de soi-même
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste de toutes les annonces
     */
    public function listings(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $listings = Listing::with(['user', 'photos', 'availabilities'])
                ->paginate($perPage);

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
     * Activer/Désactiver une annonce
     */
    public function toggleListingStatus($id)
    {
        try {
            $listing = Listing::find($id);

            if (!$listing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Annonce non trouvée'
                ], 404);
            }

            $listing->is_active = !$listing->is_active;
            $listing->save();

            return response()->json([
                'success' => true,
                'message' => 'Statut de l\'annonce modifié',
                'data' => $listing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du statut',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approuver/Valider une annonce (is_approved)
     */
    public function approveListing($id)
    {
        try {
            $listing = Listing::find($id);

            if (!$listing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Annonce non trouvée'
                ], 404);
            }

            $listing->is_approved = !$listing->is_approved;
            $listing->save();

            return response()->json([
                'success' => true,
                'message' => $listing->is_approved ? 'Annonce approuvée' : 'Approbation retirée',
                'data' => $listing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une annonce
     */
    public function deleteListing($id)
    {
        try {
            $listing = Listing::find($id);

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste de toutes les réservations
     */
    public function bookings(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $bookings = Booking::with(['listing', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $bookings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des réservations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détails d'une annonce
     */
    public function showListing($id)
    {
        try {
            $listing = Listing::with(['user', 'photos', 'availabilities', 'reviews', 'specialOffers'])
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'annonce',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste de tous les avis
     */
    public function reviews(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $reviews = Review::with(['user', 'listing'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reviews
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des avis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détails d'un avis
     */
    public function showReview($id)
    {
        try {
            $review = Review::with(['user', 'listing'])->find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Avis non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $review
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'avis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un avis
     */
    public function deleteReview($id)
    {
        try {
            $review = Review::find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Avis non trouvé'
                ], 404);
            }

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Avis supprimé'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'avis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste de toutes les offres spéciales
     */
    public function specialOffers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $offers = SpecialOffer::with(['listing', 'listing.user'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $offers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des offres spéciales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détails d'une offre spéciale
     */
    public function showSpecialOffer($id)
    {
        try {
            $offer = SpecialOffer::with(['listing', 'listing.user'])->find($id);

            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offre spéciale non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $offer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'offre spéciale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activer/Désactiver une offre spéciale
     */
    public function toggleSpecialOffer($id)
    {
        try {
            $offer = SpecialOffer::find($id);

            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offre spéciale non trouvée'
                ], 404);
            }

            $offer->is_active = !$offer->is_active;
            $offer->save();

            return response()->json([
                'success' => true,
                'message' => $offer->is_active ? 'Offre spéciale activée' : 'Offre spéciale désactivée',
                'data' => $offer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification de l\'offre spéciale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une offre spéciale
     */
    public function deleteSpecialOffer($id)
    {
        try {
            $offer = SpecialOffer::find($id);

            if (!$offer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offre spéciale non trouvée'
                ], 404);
            }

            $offer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Offre spéciale supprimée'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'offre spéciale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le statut d'une réservation
     */
    public function updateBookingStatus(Request $request, $id)
    {
        try {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non trouvée'
                ], 404);
            }

            $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled'
            ]);

            $booking->status = $request->status;
            $booking->save();

            return response()->json([
                'success' => true,
                'message' => 'Statut de la réservation mis à jour',
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
