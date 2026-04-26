<!-- component for party cards -->
<script setup>
import api from '@/utils/api'
import { onMounted, ref } from 'vue'

const parties = ref([])

onMounted(() => {
  api
    .get('/party/list')
    .then((res) => {
      parties.value = res.data
      console.log(parties.value)
    })
    .catch((err) => {
      console.log(err)
    })
})
</script>

<template>
  <!-- card -->
  <div
    class="bg-green-jdr rounded-lg shadow-md p-8 w-full max-w-xs border-olive-jdr/40 border-2"
    v-for="party in parties"
    :key="party.id"
  >
    <!-- party id -->
    <h1 class="text-2xl text-creamy-jdr mb-2 font-bold bg-orange-jdr rounded-lg px-2">
      Partie #{{ party.id }}
    </h1>
    <!-- party owner -->
    <h2 class="text-xl text-creamy-jdr font-semibold p-2">Hébergée par:</h2>
    <h2 class="text-xl text-creamy-jdr font-semibold p-2 text-center bg-orange-jdr rounded-lg">
      {{ party.mj_id }}
    </h2>
    <!-- party population -->
    <p class="text-creamy-jdr p-2">Nombre de joueurs: 5<!-- {{ party.character_count }}--></p>
  </div>
</template>

<style scoped></style>
