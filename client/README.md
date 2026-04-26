# Documentation Client QuickJDR

## Image Docker

### Dev (hot-reload)
```bash
docker build --target dev -t quickjdr-client:dev ./client
docker run -p 5173:5173 -v ./:/app quickjdr-client:dev
```

### Prod (nginx)
```bash
docker build --target prod -t quickjdr-client:prod ./client
docker run -p 80:80 quickjdr-client:prod
```

---

## Paramètres de configuration

| Paramètre | Valeur par défaut |
|-----------|-------------------|
| VITE_API_URL | http://localhost:8000 |
