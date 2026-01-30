<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API - Espace Public</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="mb-6">
            <a href="/test-api" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">← Retour au dashboard</a>
            <h1 class="text-3xl font-bold text-blue-600">Test Espace Public API</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Search Listings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Recherche d'annonces</h2>
                <form id="searchForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville:</label>
                        <input type="text" name="city" placeholder="Paris" class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix min:</label>
                            <input type="number" name="min_price" placeholder="50" class="w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix max:</label>
                            <input type="number" name="max_price" placeholder="500" class="w-full p-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de voyageurs:</label>
                        <input type="number" name="guests" placeholder="2" class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">
                        Rechercher
                    </button>
                </form>
            </div>

            <!-- Get Page -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Pages légales</h2>
                <form id="pageForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug de la page:</label>
                        <select name="slug" class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="mentions-legales">Mentions légales</option>
                            <option value="cgu">Conditions générales</option>
                            <option value="confidentialite">Politique de confidentialité</option>
                            <option value="cookies">Politique de cookies</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600">
                        Afficher la page
                    </button>
                </form>
            </div>

            <!-- Special Offers List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Offres spéciales</h2>
                <p class="text-gray-600 mb-4">Liste de toutes les offres spéciales actives</p>
                <button onclick="getSpecialOffers()" class="w-full bg-purple-500 text-white py-2 rounded-md hover:bg-purple-600">
                    Voir les offres spéciales
                </button>
            </div>

            <!-- Get Specific Special Offer -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Détails d'une offre spéciale</h2>
                <form id="specialOfferForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID de l'offre:</label>
                        <input type="number" name="id" placeholder="1" class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="w-full bg-orange-500 text-white py-2 rounded-md hover:bg-orange-600">
                        Voir les détails
                    </button>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
                <h2 class="text-xl font-semibold mb-4">Actions rapides</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button onclick="quickSearch()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Recherche rapide
                    </button>
                    <button onclick="getMentionsLegales()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Mentions légales
                    </button>
                    <button onclick="getCGU()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        CGU
                    </button>
                    <button onclick="getAllSpecialOffers()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Toutes les offres
                    </button>
                </div>
            </div>
        </div>

        <!-- Response Display -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Réponse API</h3>
            <div id="apiResponse" class="bg-gray-900 text-green-400 p-4 rounded-md font-mono text-sm overflow-x-auto" style="min-height: 200px;">
                Les réponses de l'API apparaîtront ici...
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '{{ url("/api") }}';

        function displayResponse(response) {
            const responseDiv = document.getElementById('apiResponse');
            responseDiv.textContent = JSON.stringify(response, null, 2);
        }

        async function apiCall(endpoint, method = 'GET', data = null) {
            const config = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };

            if (data) {
                config.body = JSON.stringify(data);
            }

            try {
                const response = await fetch(`${API_BASE}${endpoint}`, config);
                const result = await response.json();
                displayResponse(result);
                return result;
            } catch (error) {
                displayResponse({ error: error.message });
            }
        }

        // Search form handler
        document.getElementById('searchForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const params = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }
            
            await apiCall(`/search?${params.toString()}`);
        });

        // Page form handler
        document.getElementById('pageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const slug = formData.get('slug');
            
            await apiCall(`/pages/${slug}`);
        });

        // Special offer form handler
        document.getElementById('specialOfferForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const id = formData.get('id');
            
            await apiCall(`/special-offers/${id}`);
        });

        // Quick actions
        async function quickSearch() {
            await apiCall('/search?city=Paris&max_guests=2');
        }

        async function getSpecialOffers() {
            await apiCall('/special-offers');
        }

        async function getAllSpecialOffers() {
            await apiCall('/public/special-offers');
        }

        async function getMentionsLegales() {
            await apiCall('/pages/mentions-legales');
        }

        async function getCGU() {
            await apiCall('/pages/cgu');
        }
    </script>
</body>
</html>
