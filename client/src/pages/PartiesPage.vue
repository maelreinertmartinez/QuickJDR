<script setup>
import pcard from '@/components/PartyCard.vue'

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
  <!-- main container -->
  <div class="flex flex-wrap p-4 gap-4 justify-center" v-for="party in parties" :key="party.id">
    <pcard :partyId="party.id" :partyMjId="party.mj_id" />
  </div>
</template>

<style scoped></style>
