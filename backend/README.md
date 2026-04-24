# QuickJDR API Documentation

## Base URL
```
http://localhost:8000
```

## Authentication
All routes except `/auth/register` and `/auth/login` require being logged in via session.

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

### Login
```
POST /auth/login
```
| Field | Type | Required |
|-------|------|----------|
| username | string | ✅ |
| password | string | ✅ |

### Logout
```
POST /auth/logout
```

---

## Party Routes
Requires login. Create party requires `game_master` role.

### Create party
```
POST /party/create
```

### List all parties
```
GET /party/list
```

### Get players in a party
```
GET /party/players?id={party_id}
```

---

## Character Routes
Requires login. All bodies are JSON.

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

### Get character by id
```
GET /characters/{id}
```

### Get characters by party
```
GET /characters?party_id={party_id}
```

---

## Skills Routes
Requires login. All bodies are JSON.

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

### Get skills of a character
```
GET /skills/character?character_id={character_id}
```

---

## Users Routes
Requires login.

### List all users
```
GET /users/list
```

---

## Dice Routes
Requires login. Body is JSON.

### Launch dice
```
POST /dice/launch
```
| Field | Type | Required |
|-------|------|----------|
| character_id | int | ✅ |
| max_value | int | ✅ |