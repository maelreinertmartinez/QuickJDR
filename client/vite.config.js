import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), vueDevTools(), tailwindcss()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    host: true, // Permet l'accès externe au conteneur
    port: 5173,
    watch: {
      usePolling: true, // Force Vite à vérifier les fichiers toutes les X millisecondes
      interval: 100,
    },
    host: true, // Nécessaire pour que Docker expose correctement le port
    strictPort: true,
  },
})
