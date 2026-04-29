<script setup>
import api from '@/utils/api'
import { onMounted, ref } from 'vue'

const players = ref([])

onMounted(() => {
  api.get('/users/list').then((res) => {
    players.value = res.data
    console.log(players.value)
  })
})
</script>

<template>
  <div class="flex w-full max-w-xs max-h-50 flex-col">
    <h2 class="text-xl text-creamy-jdr font-semibold p-2">Liste des joueurs</h2>
    <div clss="flex overflow-y-auto">
      <div v-for="player in players" :key="player.id" class="px-2 py-1 flex flex-row gap-4">
        <div>{{ player.username }}</div>
        <button class="ml-auto">Ajouter</button>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
