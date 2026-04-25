<template>
  <div class="min-h-screen bg-stone-950 text-amber-100 p-6">

    <!-- Header -->
    <header class="flex items-center gap-3 mb-8">
      <div class="w-9 h-9 bg-amber-700 rounded-lg flex items-center justify-center text-amber-100 text-lg">
        ♛
      </div>
      <h1 class="text-xl font-semibold text-amber-100">Table de jeu — Maître du jeu</h1>
    </header>

    <!-- Erreur globale -->
    <div v-if="error && !loading" class="mb-4 px-4 py-2.5 rounded-xl bg-red-950/50 border border-red-900/50 text-red-400 text-sm">
      ⚠️ {{ error }} — données de secours affichées.
    </div>

    <div v-if="loading" class="text-amber-800 text-sm">Chargement des aventuriers…</div>

    <template v-if="!loading">

      <!-- Grille des cartes joueurs -->
      <section class="mb-8">
        <p class="text-xs font-semibold text-amber-700 uppercase tracking-widest mb-4">Aventuriers</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <PlayerCard
            v-for="p in players"
            :key="p.character_id"
            :player="p"
            @updated="applyUpdate"
          />
        </div>
      </section>

      <!-- Lanceur de dés -->
      <section class="mb-8">
        <DiceRoller @rolled="onRoll" />
      </section>

      <!-- Historique -->
      <section>
        <p class="text-xs font-semibold text-amber-700 uppercase tracking-widest mb-3">
          Historique des lancers
        </p>
        <DiceHistory :history="rollHistory" />
      </section>

    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import PlayerCard from '@/components/PlayerCard.vue'
import DiceRoller from '@/components/DiceRoller.vue'
import DiceHistory from '@/components/DiceHistory.vue'
import { usePlayers } from '@/composables/usePlayers'

const { players, loading, error, fetchPlayers, applyUpdate } = usePlayers()
const rollHistory = ref([])

function onRoll(result) {
  rollHistory.value.unshift(result)
  if (rollHistory.value.length > 10) rollHistory.value.pop()
}

onMounted(() => fetchPlayers(1))
</script>
