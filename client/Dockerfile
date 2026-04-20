# =============================================================================
# QuickJDR - Dockerfile multi-étapes (dev / staging / prod)
# =============================================================================
# Usage:
#   Dev:     docker build --target dev -t quick-jdr-client:dev . && docker run -p 5173:5173 -v ./:/app quick-jdr-client:dev
#   Prod:    docker build --target prod -t quick-jdr-client:prod . && docker run -p 8080:8080 quick-jdr-client:prod
# =============================================================================

# -----------------------------------------------------------------------------
# Base : dépendances communes
# -----------------------------------------------------------------------------
FROM node:22-alpine AS base
WORKDIR /app

# -----------------------------------------------------------------------------
# Dev : serveur de développement Vite avec hot-reload
# -----------------------------------------------------------------------------
FROM base AS dev

COPY . .

EXPOSE 5173
ENV NODE_ENV=development

CMD ["sh", "-c", "npm install && npm run dev -- --host 0.0.0.0"]

# -----------------------------------------------------------------------------
# Builder (prod) : compilation en mode production
# -----------------------------------------------------------------------------
FROM base AS builder-prod
COPY package*.json ./

RUN npm i
COPY . .

ENV NODE_ENV=production

RUN npm run build

# -----------------------------------------------------------------------------
# Prod : build de production servi par serve
# -----------------------------------------------------------------------------
FROM nginx:alpine AS prod

COPY --from=builder-prod /app/dist /usr/share/nginx/html

EXPOSE 80
