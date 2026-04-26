<template>
  <div class="mj-page">
    <!-- Header -->
    <header class="mj-header">
      <div class="mj-crown">♛</div>
      <h1 class="mj-title">Table du maître</h1>
    </header>

    <!-- Erreur globale -->
    <div v-if="error && !loading" class="mj-error">
      ⚠️ {{ error }} — données de secours affichées.
    </div>

    <div v-if="loading" class="mj-loading">Chargement des aventuriers…</div>

    <template v-if="!loading">
      <!-- Dés + Historique en haut -->
      <div class="mj-top-row">
        <DiceRoller @rolled="onRoll" />
        <DiceHistory :history="rollHistory" />
      </div>

      <!-- Cartes joueurs en bas -->
      <div class="mj-player-grid">
        <PlayerCard v-for="p in players" :key="p.character_id" :player="p" @updated="applyUpdate" />
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import PlayerCard from '@/components/PlayerCard.vue'
import DiceRoller from '@/components/DiceRoller.vue'
import DiceHistory from '@/components/DiceHistory.vue'
import { usePlayers } from '@/composables/usePlayers'
import api, { setToken } from '@/utils/api'

const { players, loading, error, fetchPlayers, applyUpdate } = usePlayers()
const rollHistory = ref([])

function onRoll(result) {
  rollHistory.value.unshift(result)
  if (rollHistory.value.length > 10) rollHistory.value.pop()
}

onMounted(() => fetchPlayers(1))
</script>

<style scoped>
.mj-page {
  min-height: 100vh;
  background: #1c2318;
  color: #d4c5a9;
  padding: 12px;
  font-size: 12px;
  font-family: 'Segoe UI', sans-serif;
}

.mj-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
  padding-bottom: 8px;
  border-bottom: 1px solid #2e3a28;
}

.mj-crown {
  width: 28px;
  height: 28px;
  background: #9a6422;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #f5e0a0;
  font-size: 14px;
  flex-shrink: 0;
}

.mj-title {
  font-size: 11px;
  font-weight: 500;
  color: #d4a855;
  text-transform: uppercase;
  letter-spacing: 0.14em;
}

.mj-error {
  margin-bottom: 8px;
  padding: 8px 12px;
  border-radius: 6px;
  background: #2e1010;
  border: 1px solid #6a2018;
  color: #e05848;
  font-size: 12px;
}

.mj-loading {
  color: #7a8a6a;
  font-size: 12px;
  padding: 20px 0;
}

.mj-top-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  margin-bottom: 8px;
}

.mj-player-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}

@media (max-width: 900px) {
  .mj-player-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 500px) {
  .mj-player-grid,
  .mj-top-row {
    grid-template-columns: 1fr;
  }
}
</style>
