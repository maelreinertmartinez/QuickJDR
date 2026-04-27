# QuickJDR

## Documentations

- [Documentation backend](./backend/README.md)
- [Documentation client](./client/README.md)
- [Documentation Kubernetes](./k8s/README.md)

---

## Lancement de l'environnement de développement

```bash
docker compose up -d
```

---

## Initialisation de la base de données

### Option 1 — Via une application externe

| Paramètre | Valeur |
|-----------|--------|
| host | localhost |
| port | 5432 |
| username | postgres |
| password | postgres |
| database | quickjdr |

Puis coller le contenu présent dans `backend/database.sql`.

### Option 2 — Via Docker

```bash
docker compose exec database psql -U postgres -d quickjdr
```

Puis coller le contenu présent dans `backend/database.sql`.

## Liste des pages fonctionnels

- http://localhost:5173/login
- http://localhost:5173/register
- http://localhost:5173/party/list (authentifié)
