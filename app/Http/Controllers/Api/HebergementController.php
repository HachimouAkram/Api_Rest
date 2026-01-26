<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hebergement;
use Illuminate\Http\Request;

class HebergementController extends Controller
{
    /**
     * Liste de tous les hébergements
     */
    public function index()
    {
        try {
            $hebergements = Hebergement::all();
            return response()->json([
                'success' => true,
                'data' => $hebergements
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des hébergements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Détails d'un hébergement spécifique
     */
    public function show($id)
    {
        try {
            $hebergement = Hebergement::find($id);
            
            if (!$hebergement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hébergement non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $hebergement
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'hébergement',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
