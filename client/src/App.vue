<script setup>
import api, { clearRoles, clearToken, hasToken } from '@/utils/api'

if (
  !hasToken() &&
  window.location.pathname !== '/login' &&
  window.location.pathname !== '/register'
) {
  window.location.href = '/login'
}

const logout = () => {
  api.post('/logout').then(() => {
    clearToken()
    clearRoles()
    window.location.href = '/'
  })
}
</script>

<template>
  <button
    v-if="hasToken()"
    class="absolute right-4 top-4 bg-orange-jdr text-creamy-jdr px-3 py-2 rounded-lg hover:bg-brown-jdr"
    @click="logout"
  >
    Déconnexion
  </button>
  <router-view />
</template>

<style scoped></style>
