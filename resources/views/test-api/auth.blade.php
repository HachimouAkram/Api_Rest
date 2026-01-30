<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API - Authentification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="mb-6">
            <a href="/test-api" class="text-blue-600 hover:text-blue-800 mb-4 inline-block">← Retour au dashboard</a>
            <h1 class="text-3xl font-bold text-green-600">Test Authentification API</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Register Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Inscription</h2>
                <form id="registerForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom:</label>
                        <input type="text" name="name" required class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                        <input type="email" name="email" required class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe:</label>
                        <input type="password" name="password" required class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rôle:</label>
                        <select name="role" class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="client">Client</option>
                            <option value="partenaire">Partenaire</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600">
                        S'inscrire
                    </button>
                </form>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Connexion</h2>
                <form id="loginForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                        <input type="email" name="email" required class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe:</label>
                        <input type="password" name="password" required class="w-full p-2 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">
                        Se connecter
                    </button>
                </form>
            </div>

            <!-- Get Profile -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Voir mon profil</h2>
                <p class="text-gray-600 mb-4">Récupérer les informations de l'utilisateur connecté</p>
                <button onclick="getProfile()" class="w-full bg-purple-500 text-white py-2 rounded-md hover:bg-purple-600">
                    Voir mon profil
                </button>
            </div>

            <!-- Quick Test Users -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Test rapide</h2>
                <p class="text-gray-600 mb-4">Créer des utilisateurs de test rapidement</p>
                <div class="space-y-2">
                    <button onclick="createTestUser('client')" class="w-full bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Créer un client de test
                    </button>
                    <button onclick="createTestUser('partenaire')" class="w-full bg-gray-500 text-white py-2 rounded-md hover:bg-gray-600">
                        Créer un partenaire de test
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

        function setToken(token) {
            localStorage.setItem('api_token', token);
            if (window.opener && !window.opener.closed) {
                window.opener.document.getElementById('tokenStorage').value = token;
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

            if (data) {
                config.body = JSON.stringify(data);
            }

            const token = localStorage.getItem('api_token');
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }

            try {
                const response = await fetch(`${API_BASE}${endpoint}`, config);
                const result = await response.json();
                displayResponse(result);
                
                if (result.token) {
                    setToken(result.token);
                }
                
                return result;
            } catch (error) {
                displayResponse({ error: error.message });
            }
        }

        // Register form handler
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            await apiCall('/register', 'POST', data);
        });

        // Login form handler
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            await apiCall('/login', 'POST', data);
        });

        // Get profile
        async function getProfile() {
            await apiCall('/auth/me');
        }

        // Create test users
        async function createTestUser(role) {
            const testData = {
                name: `Test ${role}`,
                email: `test${role}${Date.now()}@example.com`,
                password: 'password123',
                role: role
            };
            
            await apiCall('/register', 'POST', testData);
            
            // Auto login after registration
            const loginData = {
                email: testData.email,
                password: testData.password
            };
            
            await apiCall('/login', 'POST', loginData);
        }
    </script>
</body>
</html>
