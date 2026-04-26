# QuickJDR API Documentation

## Base URL
```
http://localhost:8000
```

## Authentication
All routes except `/auth/register` and `/auth/login` require a Bearer token in the request headers:
```
Authorization: Bearer <your_token>
```
The token is returned on login or register.

---

## Auth Routes
All auth routes use `x-www-form-urlencoded` body.

### Register
```
POST /auth/register
```
| Field | Type | Required |
|-------|------|----------|
| username | string | ✅ |
| password | string | ✅ |
| confirm_password | string | ✅ |
| as_game_master | boolean | ❌ |

Response:
```json
{
    "message": "Registration successful",
    "username": "testdm",
    "session": "<token>",
    "roles": [{"label": "game_master"}]
}
```

### Login
```
POST /auth/login
```
| Field | Type | Required |
|-------|------|----------|
| username | string | ✅ |
| password | string | ✅ |

Response:
```json
{
    "message": "Login successful",
    "username": "testdm",
    "session": "<token>",
    "roles": [{"label": "game_master"}]
}
```

### Logout
```
POST /auth/logout
```

---

## Party Routes
Requires Bearer token. Create party requires `game_master` role.

### Create party
```
POST /party/create
```
Response:
```json
{
    "message": "Party created",
    "id": 1
}
```

### List all parties
```
GET /party/list
```
Response:
```json
[
    {"id": 1, "mj_id": 1}
]
```

### Get players in a party
```
GET /party/players?id={party_id}
```
Response:
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

## Character Routes
Requires Bearer token. All bodies are JSON.

### Create character
```
POST /characters
```
| Field | Type | Required |
|-------|------|----------|
| party_id | int | ✅ |
| name | string | ✅ |
| health | int | ✅ |
| max_health | int | ✅ |
| max_mana | int | ✅ |

Response:
```json
{"id": 1}
```

### Get character by id
```
GET /characters/{id}
```

### Get characters by party
```
GET /characters?party_id={party_id}
```

### Update health
```
PATCH /characters/health
```
| Field | Type | Required |
|-------|------|----------|
| character_id | int | ✅ |
| value | int (+ or -) | ✅ |

Response:
```json
{
    "character_id": 1,
    "new_health": 90
}
```

### Update mana
```
PATCH /characters/mana
```
| Field | Type | Required |
|-------|------|----------|
| character_id | int | ✅ |
| value | int (+ or -) | ✅ |

Response:
```json
{
    "character_id": 1,
    "new_mana": 45
}
```

### Update armor
```
PATCH /characters/armor
```
| Field | Type | Required |
|-------|------|----------|
| character_id | int | ✅ |
| value | int (+ or -) | ✅ |

Response:
```json
{
    "character_id": 1,
    "new_armor": 10
}
```

---

## Skills Routes
Requires Bearer token. All bodies are JSON.

### Create skill
```
POST /skills/create
```
| Field | Type | Required |
|-------|------|----------|
| label | string | ✅ |
| description | string | ✅ |
| mana_cost | int | ✅ |
| dice_cost | int | ✅ |
| damage | int | ❌ |
| healing | int | ❌ |

Response:
```json
{"id": 1}
```

### List all skills
```
GET /skills/list
```

### Assign skill to character
```
POST /skills/assign
```
| Field | Type | Required |
|-------|------|----------|
| character_id | int | ✅ |
| skill_id | int | ✅ |

Response:
```json
{
    "message": "Skill assigned to character",
    "id": 1
}
```

### Get skills of a character
```
GET /skills/character?character_id={character_id}
```
Response:
```json
[
    {
        "id": 1,
        "label": "Fireball",
        "description": "Lance une boule de feu",
        "mana_cost": 10,
        "healing": null,
        "damage": 50,
        "dice_cost": 2
    }
]
```

---

## Users Routes
Requires Bearer token.

### List all users
```
GET /users/list
```
Response:
```json
[
    {"id": 1, "username": "admin"},
    {"id": 2, "username": "testdm"}
]
```

---

## Dice Routes
Requires Bearer token. Body is JSON.

### Launch dice for a character
```
POST /dice/launch
```
| Field | Type | Required |
|-------|------|----------|
| character_id | int | ✅ |
| max_value | int | ✅ |

Response:
```json
{
    "message": "Dice launched",
    "value": 4
}
```

### Roll blank dice
```
POST /dice/roll/blank
```
| Field | Type | Required |
|-------|------|----------|
| max_value | int | ✅ |

Response:
```json
{
    "value": 3
}
```