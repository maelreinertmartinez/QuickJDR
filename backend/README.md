# Documentation API QuickJDR

## URL de base
```
http://localhost:8000
```

## Authentification
Toutes les routes sauf `/auth/register` et `/auth/login` nécessitent un token Bearer dans les headers :
```
Authorization: Bearer <votre_token>
```
Le token est retourné lors du login ou de l'inscription.

---

## Routes Auth
Toutes les routes auth utilisent un body `x-www-form-urlencoded`.

### Inscription
```
POST /auth/register
```
| Champ | Type | Requis |
|-------|------|--------|
| username | string | ✅ |
| password | string | ✅ |
| confirm_password | string | ✅ |
| as_game_master | boolean | ❌ |

Réponse :
```json
{
    "message": "Registration successful",
    "username": "testdm",
    "session": "<token>",
    "roles": [{"label": "game_master"}]
}
```

### Connexion
```
POST /auth/login
```
| Champ | Type | Requis |
|-------|------|--------|
| username | string | ✅ |
| password | string | ✅ |

Réponse :
```json
{
    "message": "Login successful",
    "username": "testdm",
    "session": "<token>",
    "roles": [{"label": "game_master"}]
}
```

### Déconnexion
```
POST /auth/logout
```

---

## Routes Party
Nécessite un token Bearer. La création de party nécessite le rôle `game_master`.

### Créer une party
```
POST /party/create
```
Réponse :
```json
{
    "message": "Party created",
    "id": 1
}
```

### Lister toutes les parties
```
GET /party/list
```
Réponse :
```json
[
    {"id": 1, "mj_id": 1}
]
```

### Récupérer les joueurs d'une party
```
GET /party/players?id={party_id}
```
Réponse :
```json
[
    {
        "character_id": 1,
        "name": "Test Warrior",
        "health": 100,
        "max_health": 100,
        "armor": 0,
        "max_armor": 0,
        "mana": 0,
        "max_mana": 50
    }
]
```

---

## Routes Personnages
Nécessite un token Bearer. Tous les bodies sont en JSON.

### Créer un personnage
```
POST /characters
```
| Champ | Type | Requis |
|-------|------|--------|
| party_id | int | ✅ |
| name | string | ✅ |
| health | int | ✅ |
| max_health | int | ✅ |
| max_mana | int | ✅ |

Réponse :
```json
{"id": 1}
```

### Récupérer un personnage par id
```
GET /characters/{id}
```

### Récupérer les personnages d'une party
```
GET /characters?party_id={party_id}
```

### Mettre à jour la vie
```
PATCH /characters/health
```
| Champ | Type | Requis |
|-------|------|--------|
| character_id | int | ✅ |
| value | int (+ ou -) | ✅ |

Réponse :
```json
{
    "character_id": 1,
    "new_health": 90
}
```

### Mettre à jour le mana
```
PATCH /characters/mana
```
| Champ | Type | Requis |
|-------|------|--------|
| character_id | int | ✅ |
| value | int (+ ou -) | ✅ |

Réponse :
```json
{
    "character_id": 1,
    "new_mana": 45
}
```

### Mettre à jour l'armure
```
PATCH /characters/armor
```
| Champ | Type | Requis |
|-------|------|--------|
| character_id | int | ✅ |
| value | int (+ ou -) | ✅ |

Réponse :
```json
{
    "character_id": 1,
    "new_armor": 10
}
```

---

## Routes Compétences
Nécessite un token Bearer. Tous les bodies sont en JSON.

### Créer une compétence
```
POST /skills/create
```
| Champ | Type | Requis |
|-------|------|--------|
| label | string | ✅ |
| description | string | ✅ |
| mana_cost | int | ✅ |
| dice_cost | int | ✅ |
| damage | int | ❌ |
| healing | int | ❌ |

Réponse :
```json
{"id": 1}
```

### Lister toutes les compétences
```
GET /skills/list
```

### Assigner une compétence à un personnage
```
POST /skills/assign
```
| Champ | Type | Requis |
|-------|------|--------|
| character_id | int | ✅ |
| skill_id | int | ✅ |

Réponse :
```json
{
    "message": "Skill assigned to character",
    "id": 1
}
```

### Récupérer les compétences d'un personnage
```
GET /skills/character?character_id={character_id}
```
Réponse :
```json
[
    {
        "id": 1,
        "label": "Boule de feu",
        "description": "Lance une boule de feu",
        "mana_cost": 10,
        "healing": null,
        "damage": 50,
        "dice_cost": 2
    }
]
```

---

## Routes Utilisateurs
Nécessite un token Bearer.

### Lister tous les utilisateurs
```
GET /users/list
```
Réponse :
```json
[
    {"id": 1, "username": "admin"},
    {"id": 2, "username": "testdm"}
]
```

---

## Routes Dés
Nécessite un token Bearer. Body en JSON.

### Lancer un dé pour un personnage
```
POST /dice/launch
```
| Champ | Type | Requis |
|-------|------|--------|
| character_id | int | ✅ |
| max_value | int | ✅ |

Réponse :
```json
{
    "message": "Dice launched",
    "value": 4
}
```

### Lancer un dé sans personnage
```
POST /dice/roll/blank
```
| Champ | Type | Requis |
|-------|------|--------|
| max_value | int | ✅ |

Réponse :
```json
{
    "value": 3
}
```