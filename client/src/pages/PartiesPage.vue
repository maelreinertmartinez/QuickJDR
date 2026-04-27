<script setup>
import pcard from '@/components/PartyCard.vue'

import api, { hasRole } from '@/utils/api'
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
  <!-- main container -->
  <div class="flex flex-wrap p-4 gap-4 justify-center">
    <pcard
      v-for="party in parties"
      :key="party.id"
      :partyId="party.id"
      :partyMj="party.game_master"
      :nbPlayers="party.nb_players"
    />
    <a
      v-if="hasRole('game_master')"
      href="/party/create"
      class="w-full max-w-xs h-58.75 bg-green-jdr border-creamy-jdr border-2 rounded-lg flex justify-center items-center text-creamy-jdr font-light text-8xl"
    >
      +
    </a>
  </div>
</template>

<style scoped></style>
