<script setup>
import pscroll from '@/components/PlayerListScrollable.vue'
import api, { hasRole } from '@/utils/api'
import { onMounted, ref } from 'vue'

const partyInfo = ref([])

onMounted(() => {
  api
    .post('/party/create')
    .then((result) => {
      partyInfo.value = result.data
      console.log(result)
    })
    .catch((err) => {
      console.log(err)
    })
})
</script>

<template>
  <div class="min-h-screen flex flex-row flex-wrap items-center justify-center gap-4">
    <div class="flex flex-col">
      <div
        v-if="hasRole('game_master')"
        class="bg-green-jdr rounded-lg shadow-md p-8 w-full max-w-xs border-olive-jdr/40 border-2 text-center"
      >
        <h1 class="text-2xl text-creamy-jdr mb-2 font-bold px-2">
          Partie n°{{ partyInfo.id }} créee avec succès
        </h1>
        <div class="flex flex-col gap-3">
          <a
            v-if="hasRole('game_master')"
            href="/party/list"
            class="mt-4 mb-2 bg-orange-jdr text-creamy-jdr px-1 py-2 rounded-lg hover:bg-brown-jdr"
          >
            Retour à la liste des parties
          </a>
          <a
            v-if="hasRole('game_master')"
            :href="`/party/${partyInfo.id}`"
            class="mt-4 mb-2 bg-orange-jdr text-creamy-jdr px-1 py-2 rounded-lg hover:bg-brown-jdr"
          >
            Dashboard de la partie
          </a>
        </div>
      </div>
    </div>
    <pscroll :partyId="partyInfo.id" />
  </div>
</template>

<style scoped></style>
