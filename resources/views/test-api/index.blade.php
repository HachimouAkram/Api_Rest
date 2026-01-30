<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-8 text-blue-600">API Test Dashboard</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Authentification -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-4 text-green-600">Authentification</h2>
                <p class="text-gray-600 mb-4">Test des endpoints d'authentification</p>
                <a href="/test-api/auth" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 inline-block">
                    Tester Auth
                </a>
            </div>

            <!-- Espace Public -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-4 text-blue-600">Espace Public</h2>
                <p class="text-gray-600 mb-4">Test des endpoints publics</p>
                <a href="/test-api/public" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">
                    Tester Public
                </a>
            </div>

            <!-- Espace Partenaire -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-4 text-purple-600">Espace Partenaire</h2>
                <p class="text-gray-600 mb-4">Test des endpoints partenaire</p>
                <a href="/test-api/partner" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600 inline-block">
                    Tester Partenaire
                </a>
            </div>

            <!-- Documentation -->
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <h2 class="text-xl font-semibold mb-4 text-gray-600">Documentation</h2>
                <p class="text-gray-600 mb-4">Voir les routes API</p>
                <div class="text-sm text-gray-500">
                    <p><strong>Base URL:</strong> {{ url('/api') }}</p>
                    <p class="mt-2"><strong>Routes disponibles:</strong></p>
                    <ul class="list-disc list-inside text-xs">
                        <li>POST /register</li>
                        <li>POST /login</li>
                        <li>GET /search</li>
                        <li>GET /partner/*</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Token Storage -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Stockage du Token</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Token d'authentification:</label>
                    <textarea id="tokenStorage" class="w-full p-3 border border-gray-300 rounded-md" rows="3" placeholder="Le token sera stocké ici après connexion..."></textarea>
                </div>
                <button onclick="clearToken()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Effacer le token
                </button>
            </div>
        </div>

        <!-- API Response Display -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Réponses API</h3>
            <div id="apiResponse" class="bg-gray-900 text-green-400 p-4 rounded-md font-mono text-sm overflow-x-auto" style="min-height: 200px;">
                Les réponses de l'API apparaîtront ici...
            </div>
        </div>
    </div>

    <script>
        // Fonctions utilitaires
        function setToken(token) {
            document.getElementById('tokenStorage').value = token;
            localStorage.setItem('api_token', token);
        }

        function getToken() {
            return document.getElementById('tokenStorage').value || localStorage.getItem('api_token');
        }

        function clearToken() {
            document.getElementById('tokenStorage').value = '';
            localStorage.removeItem('api_token');
        }

        function displayResponse(response) {
            const responseDiv = document.getElementById('apiResponse');
            responseDiv.textContent = JSON.stringify(response, null, 2);
        }

        // Charger le token au démarrage
        window.onload = function() {
            const savedToken = localStorage.getItem('api_token');
            if (savedToken) {
                document.getElementById('tokenStorage').value = savedToken;
            }
        };
    </script>
</body>
</html>
