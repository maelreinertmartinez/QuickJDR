<template>
  <div class="min-h-screen bg-gray-950 text-gray-100 p-6">
    <header class="flex items-center gap-3 mb-8">
      <div class="w-9 h-9 bg-amber-600 rounded-lg flex items-center justify-center text-amber-100">
        ♛
      </div>
      <h1 class="text-xl font-medium">Table de jeu — Maître du jeu</h1>
    </header>

    <div v-if="loading" class="text-gray-400 text-sm">Chargement des joueurs…</div>
    <div v-else-if="error" class="text-red-400 text-sm">
      Erreur de chargement (données de secours utilisées).
    </div>

    <template v-if="!loading">
      <section class="mb-6">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">Joueurs</p>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="p in players"
            :key="p.id"
            @click="selectedId = p.id"
            :class="[
              'flex items-center gap-2 px-3 py-1.5 rounded-full border text-sm transition-all',
              selectedId === p.id
                ? 'border-2 border-blue-500 bg-blue-950 text-blue-200'
                : 'border border-gray-700 bg-gray-900 hover:border-gray-600',
            ]"
          >
            <div
              :class="avatarClass(p)"
              class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-medium"
            >
              {{ p.initials }}
            </div>
            <span class="font-medium">{{ p.name }}</span>
            <span class="text-gray-400 text-xs">{{ p.class }}</span>
          </button>
        </div>
      </section>

      <section class="mb-8">
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">
          Fiche du personnage
        </p>
        <PlayerDetail v-if="selectedPlayer" :player="selectedPlayer" :barColor="selectedColor" />
        <p v-else class="text-sm text-gray-500 py-4">
          Sélectionne un joueur pour voir sa fiche complète.
        </p>
      </section>

      <section class="mb-8">
        <DiceRoller @rolled="onRoll" />
      </section>

      <section>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">
          Historique des lancers
        </p>
        <DiceHistory :history="rollHistory" />
      </section>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import PlayerDetail from '@/components/PlayerDetail.vue'
import DiceRoller from '@/components/DiceRoller.vue'
import DiceHistory from '@/components/DiceHistory.vue'
import { usePlayers } from '@/composables/usePlayers'

const { players, loading, error, fetchPlayers } = usePlayers()
const selectedId = ref(null)
const rollHistory = ref([])

const colorMap = {
  teal: { avatar: 'bg-teal-800 text-teal-200', bar: '#1D9E75' },
  coral: { avatar: 'bg-orange-900 text-orange-300', bar: '#D85A30' },
  purple: { avatar: 'bg-purple-900 text-purple-300', bar: '#7F77DD' },
  pink: { avatar: 'bg-pink-900 text-pink-300', bar: '#D4537E' },
}

const selectedPlayer = computed(() => {
  if (!Array.isArray(players.value)) return null
  return players.value.find((p) => p.id === selectedId.value) ?? null
})

const selectedColor = computed(() => colorMap[selectedPlayer.value?.color]?.bar ?? '#378ADD')

function avatarClass(p) {
  return colorMap[p?.color]?.avatar ?? 'bg-gray-700 text-gray-300'
}

function onRoll(result) {
  rollHistory.value.unshift(result)
  if (rollHistory.value.length > 10) rollHistory.value.pop()
}

onMounted(fetchPlayers)
</script>
