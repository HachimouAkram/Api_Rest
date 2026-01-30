<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API - Espace Partenaire</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="mb-6">
            <a href="/test-api" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">← Retour au dashboard</a>
            <h1 class="text-3xl font-bold text-purple-600">Test Espace Partenaire API</h1>
            <p class="text-gray-600 mt-2">Ces endpoints nécessitent une authentification avec un rôle partenaire ou admin</p>
        </div>

        <!-- Token Status -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-yellow-800 mb-2">Statut d'authentification</h3>
            <div id="authStatus" class="text-sm">
                <span id="tokenStatus" class="text-red-600">❌ Non authentifié</span>
                <button onclick="checkAuth()" class="ml-4 bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">
                    Vérifier l'auth
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Profile Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Gestion du profil</h2>
                <div class="space-y-4">
                    <button onclick="getProfile()" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">
                        Voir mon profil
                    </button>
                    <button onclick="showUpdateForm()" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600">
                        Modifier mon profil
                    </button>
                </div>
                
                <!-- Update Profile Form (Hidden by default) -->
                <form id="updateProfileForm" class="mt-4 space-y-4 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom:</label>
                        <input type="text" name="name" class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                        <input type="email" name="email" class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700">
                        Mettre à jour
                    </button>
                </form>
            </div>

            <!-- Create Listing -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Créer une annonce</h2>
                <form id="createListingForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre:</label>
                        <input type="text" name="title" required class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                        <textarea name="description" required class="w-full p-2 border border-gray-300 rounded-md" rows="3"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prix/nuit:</label>
                            <input type="number" name="price_per_night" required class="w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max voyageurs:</label>
                            <input type="number" name="max_guests" required class="w-full p-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville:</label>
                        <input type="text" name="city" required class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="w-full bg-purple-500 text-white py-2 rounded-md hover:bg-purple-600">
                        Créer l'annonce
                    </button>
                </form>
            </div>

            <!-- My Listings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Mes annonces</h2>
                <div class="space-y-4">
                    <button onclick="getMyListings()" class="w-full bg-indigo-500 text-white py-2 rounded-md hover:bg-indigo-600">
                        Voir mes annonces
                    </button>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Voir une annonce spécifique:</label>
                        <div class="flex gap-2">
                            <input type="number" id="listingId" placeholder="ID" class="flex-1 p-2 border border-gray-300 rounded-md">
                            <button onclick="getListing()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Voir
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bookings & Revenues -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Réservations & Revenus</h2>
                <div class="space-y-4">
                    <button onclick="getBookings()" class="w-full bg-orange-500 text-white py-2 rounded-md hover:bg-orange-600">
                        Voir mes réservations
                    </button>
                    <button onclick="getRevenues()" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700">
                        Voir mes revenus
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
                <h2 class="text-xl font-semibold mb-4">Actions rapides</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button onclick="createTestListing()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Créer annonce test
                    </button>
                    <button onclick="checkAuth()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Vérifier auth
                    </button>
                    <button onclick="getMyListings()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Mes annonces
                    </button>
                    <button onclick="getRevenues()" class="bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Mes revenus
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

        function getToken() {
            return localStorage.getItem('api_token');
        }

        function updateAuthStatus(isAuthenticated) {
            const statusElement = document.getElementById('tokenStatus');
            if (isAuthenticated) {
                statusElement.innerHTML = '✅ Authentifié';
                statusElement.className = 'text-green-600';
            } else {
                statusElement.innerHTML = '❌ Non authentifié';
                statusElement.className = 'text-red-600';
            }
        }

        async function apiCall(endpoint, method = 'GET', data = null) {
            const config = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };

            const token = getToken();
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }

            if (data) {
                config.body = JSON.stringify(data);
            }

            try {
                const response = await fetch(`${API_BASE}${endpoint}`, config);
                const result = await response.json();
                displayResponse(result);
                
                if (response.status === 401 || response.status === 403) {
                    updateAuthStatus(false);
                } else if (response.ok) {
                    updateAuthStatus(true);
                }
                
                return result;
            } catch (error) {
                displayResponse({ error: error.message });
                updateAuthStatus(false);
            }
        }

        async function checkAuth() {
            await apiCall('/auth/me');
        }

        async function getProfile() {
            await apiCall('/partner/profile');
        }

        function showUpdateForm() {
            document.getElementById('updateProfileForm').classList.toggle('hidden');
        }

        // Update profile form handler
        document.getElementById('updateProfileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            await apiCall('/partner/profile', 'PATCH', data);
        });

        // Create listing form handler
        document.getElementById('createListingForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            await apiCall('/partner/listings', 'POST', data);
        });

        async function getMyListings() {
            await apiCall('/partner/listings');
        }

        async function getListing() {
            const id = document.getElementById('listingId').value;
            if (id) {
                await apiCall(`/partner/listings/${id}`);
            }
        }

        async function getBookings() {
            await apiCall('/partner/bookings');
        }

        async function getRevenues() {
            await apiCall('/partner/revenues');
        }

        async function createTestListing() {
            const testData = {
                title: 'Appartement test ' + Date.now(),
                description: 'Superbe appartement de test avec toutes les commodités',
                price_per_night: 150,
                address: '123 Rue de la Paix',
                city: 'Paris',
                country: 'France',
                max_guests: 4,
                bedrooms: 2,
                bathrooms: 1
            };
            
            await apiCall('/partner/listings', 'POST', testData);
        }

        // Check auth on page load
        window.onload = function() {
            checkAuth();
        };
    </script>
</body>
</html>
