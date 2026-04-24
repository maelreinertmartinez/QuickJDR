# Kubernetes Deployment - QuickJDR

## Prérequis
- Docker Desktop installé et en cours d'exécution
- Minikube installé
- kubectl installé

## Étapes de déploiement

### 1. Démarrer Minikube
```bash
minikube start --driver=docker
```

### 2. Construire les images Docker
```bash
docker build -t quickjdr-backend ./backend
docker build -t quickjdr-client ./client
```

### 3. Charger les images dans Minikube
```bash
minikube image load quickjdr-backend
minikube image load quickjdr-client
```

### 4. Déployer la base de données
```bash
kubectl apply -f k8s/database/deployment.yaml
kubectl apply -f k8s/database/service-db.yaml
```

### 5. Déployer le backend
```bash
kubectl apply -f k8s/backend/deployment.yaml
kubectl apply -f k8s/backend/service-web.yaml
```

### 6. Déployer le client
```bash
kubectl apply -f k8s/client/deployment.yaml
kubectl apply -f k8s/client/service-web.yaml
```

### 7. Vérifier que tout fonctionne
```bash
kubectl get pods
kubectl get services
```

### 8. Exposer les services
```bash
minikube tunnel
```

L'application sera accessible sur:
- Backend: http://localhost:8000
- Client: http://localhost:5173

## Paramètres de configuration

### Base de données
| Paramètre | Valeur |
|-----------|--------|
| POSTGRES_USER | postgres |
| POSTGRES_PASSWORD | postgres |
| POSTGRES_DB | quickjdr |
| Port | 5432 |

### Backend
| Paramètre | Valeur |
|-----------|--------|
| DB_HOST | database |
| DB_PORT | 5432 |
| DB_DATABASE | quickjdr |
| DB_USERNAME | postgres |
| DB_PASSWORD | postgres |
| Port | 8000 |

### Client
| Paramètre | Valeur |
|-----------|--------|
| VITE_API_URL | http://backend:8000 |
| Port | 5173 |

## Adapter pour un autre environnement
Pour changer la configuration, modifier les variables d'environnement dans les fichiers `deployment.yaml` correspondants.