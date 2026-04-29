<script setup>
import api from '@/utils/api'
import { onMounted, ref } from 'vue'

const players = ref([])
const selectedPlayer = ref(null)

const playerName = ref('')
const playerHealth = ref(100)
const playerMana = ref(1000)
const playerArmor = ref(100)

const props = defineProps({ partyId: { type: Number } })

onMounted(() => {
  api.get('/users/list').then((res) => {
    players.value = res.data
    console.log(players.value)
  })
})

function ouvrirFormulaire(player) {
  selectedPlayer.value = player
}
function fermerFormulaire() {
  selectedPlayer.value = null
}

function addPlayer() {
  api
    .post('/characters/create', {
      party_id: props.partyId,
      user_id: selectedPlayer.value.id,
      name: playerName.value,
      max_health: playerHealth.value,
      max_armor: playerArmor.value,
      max_mana: playerMana.value,
    })
    .then(() => {
      fermerFormulaire()
      playerName.value = ''
      playerHealth.value = 100
      playerArmor.value = 100
      playerMana.value = 1000
      console.log('Ajout effectué')
    })
    .catch((err) => console.log(err))
}
</script>

<template>
  <div class="bg-green-jdr rounded-lg p-2 w-full max-w-xs border-olive-jdr/40 border-2">
    <h2 class="text-xl text-creamy-jdr font-semibold p-2">Liste des joueurs</h2>
    <div
      class="flex w-full overflow-y-auto max-w-xs max-h-50 flex-col rounded-lg outline-1 outline-olive-jdr/40 bg-dark-green-jdr"
    >
      <div
        v-for="player in players"
        :key="player.id"
        class="px-2 py-1 flex flex-row gap-4 items-center"
      >
        <div class="text-creamy-jdr">{{ player.username }}</div>
        <button
          class="ml-auto mt-4 mb-2 bg-orange-jdr text-creamy-jdr px-6 py-1 rounded-lg hover:bg-brown-jdr"
          @click="ouvrirFormulaire(player)"
        >
          Ajouter
        </button>
      </div>
    </div>
  </div>

  <div
    v-if="selectedPlayer"
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
    @click.self="fermerFormulaire"
  >
    <div class="bg-green-jdr rounded-lg p-6 w-full max-w-sm border-olive-jdr/40 border-2">
      <h3 class="text-xl text-creamy-jdr font-bold mb-4">Ajouter {{ selectedPlayer.username }}</h3>

      <form class="flex flex-col gap-3">
        <label class="text-creamy-jdr">Nom</label>
        <input
          v-model="playerName"
          type="text"
          class="rounded-lg px-3 py-2 bg-dark-green-jdr text-creamy-jdr outline-1 outline-olive-jdr/40"
          placeholder="Vaelion de l'Aube-Opaline"
        />
        <label class="text-creamy-jdr">Vie maximale</label>
        <input
          v-model="playerHealth"
          type="number"
          class="rounded-lg px-3 py-2 bg-dark-green-jdr text-creamy-jdr outline-1 outline-olive-jdr/40"
          placeholder="150"
        />
        <label class="text-creamy-jdr">Mana maximal</label>
        <input
          v-model="playerMana"
          type="number"
          class="rounded-lg px-3 py-2 bg-dark-green-jdr text-creamy-jdr outline-1 outline-olive-jdr/40"
          placeholder="7000"
        />
        <label class="text-creamy-jdr">Armure maximale</label>
        <input
          v-model="playerArmor"
          type="number"
          class="rounded-lg px-3 py-2 bg-dark-green-jdr text-creamy-jdr outline-1 outline-olive-jdr/40"
          placeholder="150"
        />
      </form>
      <div class="flex gap-3 mt-6">
        <button
          class="bg-orange-jdr text-creamy-jdr px-4 py-2 rounded-lg hover:bg-brown-jdr"
          @click="addPlayer"
        >
          Confirmer
        </button>
        <button
          class="bg-dark-green-jdr text-creamy-jdr px-4 py-2 rounded-lg outline-1 outline-olive-jdr/40 hover:bg-brown-jdr"
          @click="fermerFormulaire"
        >
          Annuler
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
