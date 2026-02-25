# =============================================================================
# QuickJDR - Dockerfile multi-étapes (dev / staging / prod)
# =============================================================================
# Usage:
#   Dev:     docker build --target dev -t quick-jdr-frontend:dev . && docker run -p 5173:5173 -v $(pwd):/app quick-jdr-frontend:dev
#   Prod:    docker build --target prod -t quick-jdr-frontend:prod . && docker run -p 3000:3000 quick-jdr-frontend:prod
# =============================================================================

# -----------------------------------------------------------------------------
# Base : dépendances communes
# -----------------------------------------------------------------------------
FROM node:22-alpine AS base
WORKDIR /app

COPY package.json package-lock.json* ./

# -----------------------------------------------------------------------------
# Dev : serveur de développement Vite avec hot-reload
# -----------------------------------------------------------------------------
FROM base AS dev
COPY . .

RUN npm i --dev

EXPOSE 5173
ENV NODE_ENV=development

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

# -----------------------------------------------------------------------------
# Builder (prod) : compilation en mode production
# -----------------------------------------------------------------------------
FROM base AS builder-prod
COPY . .

RUN npm ci

ENV NODE_ENV=production
RUN npm run build

# -----------------------------------------------------------------------------
# Prod : build de production servi par serve
# -----------------------------------------------------------------------------
FROM builder-prod AS prod

RUN npm install -g serve
EXPOSE 3000

CMD ["serve", "dist", "-s", "-l", "3000"]
