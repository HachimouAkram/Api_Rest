# Documentation des Routes API

## Base URL
```
http://localhost/api
```
(ou l'URL de votre serveur Laravel)

---

## Routes d'Authentification

### 1. Inscription (Register)
**POST** `/api/register`

**Body (JSON):**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}
```

**Réponse réussie (201):**
```json
{
  "success": true,
  "message": "Inscription réussie",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-01-24T12:00:00.000000Z",
    "updated_at": "2026-01-24T12:00:00.000000Z"
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Réponse d'erreur (422):**
```json
{
  "success": false,
  "message": "Erreur de validation",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

---

### 2. Connexion (Login)
**POST** `/api/login`

**Body (JSON):**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Réponse réussie (200):**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

**Réponse d'erreur (401):**
```json
{
  "success": false,
  "message": "Identifiants invalides"
}
```

---

## Routes des Hébergements

### 3. Liste des hébergements
**GET** `/api/hebergements`

**Réponse réussie (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Hôtel de Luxe",
      "location": "Paris, France",
      "price": "150.00",
      "rating": "4.5",
      "image": "https://example.com/image.jpg",
      "type": "hotel",
      "capacity": 2,
      "description": "Un magnifique hôtel au cœur de Paris",
      "amenities": ["WiFi", "Piscine", "Spa"],
      "created_at": "2026-01-24T12:00:00.000000Z",
      "updated_at": "2026-01-24T12:00:00.000000Z"
    }
  ]
}
```

---

### 4. Détails d'un hébergement
**GET** `/api/hebergements/{id}`

**Exemple:** `/api/hebergements/1`

**Réponse réussie (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Hôtel de Luxe",
    "location": "Paris, France",
    "price": "150.00",
    "rating": "4.5",
    "image": "https://example.com/image.jpg",
    "type": "hotel",
    "capacity": 2,
    "description": "Un magnifique hôtel au cœur de Paris",
    "amenities": ["WiFi", "Piscine", "Spa"],
    "created_at": "2026-01-24T12:00:00.000000Z",
    "updated_at": "2026-01-24T12:00:00.000000Z"
  }
}
```

**Réponse d'erreur (404):**
```json
{
  "success": false,
  "message": "Hébergement non trouvé"
}
```

---

## Types d'hébergements (enum)
Les valeurs possibles pour le champ `type` :
- `hotel`
- `appartement`
- `villa`
- `lodge`
- `camping`

---

## Codes de statut HTTP

- **200** : Succès
- **201** : Créé avec succès (inscription)
- **401** : Non autorisé (identifiants invalides)
- **404** : Ressource non trouvée
- **422** : Erreur de validation
- **500** : Erreur serveur

---

## Authentification avec Token

Pour les routes protégées (si vous en ajoutez plus tard), incluez le token dans les headers :

```
Authorization: Bearer {token}
```

---

## Exemples de requêtes avec cURL

### Inscription
```bash
curl -X POST http://localhost/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com","password":"password123"}'
```

### Connexion
```bash
curl -X POST http://localhost/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

### Liste des hébergements
```bash
curl -X GET http://localhost/api/hebergements
```

### Détails d'un hébergement
```bash
curl -X GET http://localhost/api/hebergements/1
```

