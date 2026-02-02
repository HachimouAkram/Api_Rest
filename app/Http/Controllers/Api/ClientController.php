<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\Review;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Récupère le profil de l'utilisateur connecté
     */
    public function profile(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour le profil de l'utilisateur
     */
    public function updateProfile(Request $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|nullable|string|max:20',
                'avatar' => 'sometimes|nullable|string|max:255'
            ]);
            
            $user->name = $request->name ?? $user->name;
            $user->phone = $request->phone ?? $user->phone;
            $user->avatar = $request->avatar ?? $user->avatar;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des réservations du client
     */
    public function bookings(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $bookings = Booking::with(['listing'])
                ->where('user_id', Auth::id())
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
     * Détails d'une réservation
     */
    public function showBooking($id)
    {
        try {
            $booking = Booking::with(['listing', 'listing.photos'])
                ->where('user_id', Auth::id())
                ->find($id);
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non trouvée'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la réservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée une nouvelle réservation
     */
    public function createBooking(Request $request)
    {
        try {
            $request->validate([
                'listing_id' => 'required|exists:listings,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date',
                'guests' => 'required|integer|min:1'
            ]);
            
            $listing = Listing::findOrFail($request->listing_id);
            
            // Vérifier la disponibilité
            $hasOverlap = Booking::where('listing_id', $request->listing_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                          ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                          });
                })->exists();
            
            if ($hasOverlap) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les dates sélectionnées ne sont pas disponibles'
                ], 400);
            }
            
            // Calculer le prix total
            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $nights = $endDate->diff($startDate)->days;
            $totalPrice = $nights * $listing->price_per_night;
            
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'listing_id' => $request->listing_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'guests' => $request->guests,
                'total_price' => $totalPrice,
                'status' => 'pending'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Réservation créée avec succès',
                'data' => $booking
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la réservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annule une réservation
     */
    public function cancelBooking(Request $request, $id)
    {
        try {
            $booking = Booking::where('user_id', Auth::id())
                ->where('id', $id)
                ->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non trouvée'
                ], 404);
            }
            
            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette réservation est déjà annulée'
                ], 400);
            }
            
            $booking->status = 'cancelled';
            $booking->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Réservation annulée',
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée une intention de paiement Stripe
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $request->validate([
                'booking_id' => 'required|exists:bookings,id'
            ]);
            
            $booking = Booking::where('user_id', Auth::id())
                ->where('id', $request->booking_id)
                ->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Réservation non trouvée'
                ], 404);
            }
            
            // TODO: Implémenter avec Stripe
            // Pour l'instant, on retourne une réponse factice
            return response()->json([
                'success' => true,
                'data' => [
                    'client_secret' => 'pi_' . time() . '_secret_' . $booking->id,
                    'amount' => $booking->total_price * 100
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du paiement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée un avis sur un hébergement
     */
    public function createReview(Request $request)
    {
        try {
            $request->validate([
                'listing_id' => 'required|exists:listings,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|max:1000'
            ]);
            
            // Vérifier que le client a bien réservé cet hébergement
            $hasBooking = Booking::where('user_id', Auth::id())
                ->where('listing_id', $request->listing_id)
                ->where('status', 'completed')
                ->exists();
            
            if (!$hasBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez avoir séjourné dans cet hébergement pour laisser un avis'
                ], 403);
            }
            
            $review = Review::create([
                'user_id' => Auth::id(),
                'listing_id' => $request->listing_id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Avis créé avec succès',
                'data' => $review
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'avis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des favoris du client
     */
    public function favorites(Request $request)
    {
        try {
            $favorites = Favorite::with(['listing', 'listing.photos'])
                ->where('user_id', Auth::id())
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $favorites
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des favoris',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ajoute un hébergement aux favoris
     */
    public function addFavorite(Request $request, $listingId)
    {
        try {
            $listing = Listing::find($listingId);
            
            if (!$listing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hébergement non trouvé'
                ], 404);
            }
            
            $exists = Favorite::where('user_id', Auth::id())
                ->where('listing_id', $listingId)
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet hébergement est déjà dans vos favoris'
                ], 400);
            }
            
            $favorite = Favorite::create([
                'user_id' => Auth::id(),
                'listing_id' => $listingId
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ajouté aux favoris',
                'data' => $favorite
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout aux favoris',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retire un hébergement des favoris
     */
    public function removeFavorite(Request $request, $listingId)
    {
        try {
            $deleted = Favorite::where('user_id', Auth::id())
                ->where('listing_id', $listingId)
                ->delete();
            
            if ($deleted === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Favori non trouvé'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Retiré des favoris'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression des favoris',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
