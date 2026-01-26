# Guide de Test avec Postman

## Configuration de base

**Base URL:** `http://127.0.0.1:8000/api`

Assurez-vous que le serveur Laravel est démarré :
```bash
php artisan serve
```

---

## Tests à effectuer

### 1. Test de la liste des hébergements

**Méthode:** `GET`  
**URL:** `http://127.0.0.1:8000/api/hebergements`

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Résultat attendu:**
- Status: `200 OK`
- Body: Liste de tous les hébergements avec `success: true`

---

### 2. Test des détails d'un hébergement

**Méthode:** `GET`  
**URL:** `http://127.0.0.1:8000/api/hebergements/1`

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Résultat attendu:**
- Status: `200 OK`
- Body: Détails de l'hébergement avec ID 1

**Test avec ID inexistant:**
- URL: `http://127.0.0.1:8000/api/hebergements/999`
- Status attendu: `404 Not Found`
- Message: "Hébergement non trouvé"

---

### 3. Test d'inscription (Register)

**Méthode:** `POST`  
**URL:** `http://127.0.0.1:8000/api/register`

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Body (raw JSON):**
```json
{
  "name": "Jean Dupont",
  "email": "jean.dupont@example.com",
  "password": "password123"
}
```

**Résultat attendu:**
- Status: `201 Created`
- Body: Utilisateur créé avec token d'authentification

**Test de validation (email déjà utilisé):**
- Utiliser le même email deux fois
- Status attendu: `422 Unprocessable Entity`
- Message d'erreur de validation

**Test de validation (champs manquants):**
- Envoyer un body incomplet
- Status attendu: `422 Unprocessable Entity`

---

### 4. Test de connexion (Login)

**Méthode:** `POST`  
**URL:** `http://127.0.0.1:8000/api/login`

**Headers:**
- `Accept: application/json`
- `Content-Type: application/json`

**Body (raw JSON):**
```json
{
  "email": "jean.dupont@example.com",
  "password": "password123"
}
```

**Résultat attendu:**
- Status: `200 OK`
- Body: Utilisateur avec token d'authentification

**Test avec identifiants invalides:**
- Utiliser un email ou mot de passe incorrect
- Status attendu: `401 Unauthorized`
- Message: "Identifiants invalides"

---

## Collection Postman (JSON)

Vous pouvez importer cette collection dans Postman :

```json
{
  "info": {
    "name": "API Hébergements",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Liste des hébergements",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "url": {
          "raw": "http://127.0.0.1:8000/api/hebergements",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "hebergements"]
        }
      }
    },
    {
      "name": "Détails hébergement",
      "request": {
        "method": "GET",
        "header": [
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "url": {
          "raw": "http://127.0.0.1:8000/api/hebergements/1",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "hebergements", "1"]
        }
      }
    },
    {
      "name": "Inscription",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          },
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"name\": \"Jean Dupont\",\n  \"email\": \"jean.dupont@example.com\",\n  \"password\": \"password123\"\n}"
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/register",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "register"]
        }
      }
    },
    {
      "name": "Connexion",
      "request": {
        "method": "POST",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          },
          {
            "key": "Accept",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"email\": \"jean.dupont@example.com\",\n  \"password\": \"password123\"\n}"
        },
        "url": {
          "raw": "http://127.0.0.1:8000/api/login",
          "protocol": "http",
          "host": ["127", "0", "0", "1"],
          "port": "8000",
          "path": ["api", "login"]
        }
      }
    }
  ]
}
```

---

## Vérifications à faire

✅ **Routes fonctionnelles:**
- [ ] GET /api/hebergements retourne la liste
- [ ] GET /api/hebergements/{id} retourne un hébergement
- [ ] POST /api/register crée un utilisateur
- [ ] POST /api/login authentifie un utilisateur

✅ **Gestion des erreurs:**
- [ ] 404 pour hébergement inexistant
- [ ] 422 pour validation échouée
- [ ] 401 pour identifiants invalides
- [ ] 500 pour erreurs serveur (si applicable)

✅ **Format des réponses:**
- [ ] Toutes les réponses sont en JSON
- [ ] Structure cohérente avec `success` et `data`/`message`
- [ ] Codes de statut HTTP corrects

