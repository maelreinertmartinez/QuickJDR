<script setup>
import api, { hasToken, clearToken, clearRoles } from '@/utils/api'

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
    class="absolute right-4 top-4 bg-[#9a6422] py-1 px-2 rounded-[5px] text-[#f5e0a0]"
    @click="logout"
  >
    Logout
  </button>
  <router-view />
</template>

<style scoped></style>
