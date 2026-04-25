<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import StatBar from '@/components/StarBar.vue'

const api = axios.create({
  baseURL: 'http://localhost:8000',
  headers: {
    Authorization: `Bearer ${localStorage.getItem('token')}`,
  },
})

const currentPlayer = ref(null)
const skills = ref([])
const selectedSkill = ref(null)
const diceResult = ref(null)
const diceType = ref(20)
const isRolling = ref(false)

const mockStats = [
  { short: 'FOR', name: 'Force', value: 10 },
  { short: 'DEX', name: 'Dextérité', value: 18 },
  { short: 'CON', name: 'Constitution', value: 14 },
  { short: 'INT', name: 'Intelligence', value: 12 },
  { short: 'SAG', name: 'Sagesse', value: 16 },
  { short: 'CHA', name: 'Charisme', value: 11 },
]

const calculateModifier = (val) => {
  const mod = Math.floor((val - 10) / 2)
  return mod >= 0 ? `+${mod}` : mod
}

const fetchData = async () => {
  try {
    const charRes = await api.get('/character')
    currentPlayer.value = {
      ...charRes.data,
      stats: mockStats,
      level: charRes.data.level || 1,
    }

    const skillsRes = await api.get('/skill/list')
    skills.value = skillsRes.data
  } catch (error) {
    console.error(error)
  }
}

const selectSkill = (skill) => {
  selectedSkill.value = skill
  if (skill.dice_cost) {
    diceType.value = skill.dice_cost
  }
}

const rollDice = async () => {
  if (!currentPlayer.value) return
  isRolling.value = true
  diceResult.value = null

  try {
    const payload = {
      max_value: Number(diceType.value),
      skill_id: selectedSkill.value ? selectedSkill.value.skill_id : null,
    }

    const res = await api.post('/dice/roll', payload)

    diceResult.value = res.data.value

    if (res.data.new_health !== undefined) {
      currentPlayer.value.health = res.data.new_health
    }
    if (res.data.new_mana !== undefined) {
      currentPlayer.value.mana = res.data.new_mana
    }
  } catch (error) {
    console.error(error)
  } finally {
    isRolling.value = false
  }
}

onMounted(() => {
  fetchData()
})
</script>

<template>
  <div class="layout-container" v-if="currentPlayer">
    <div class="column">
      <div
        id="stats-normales"
        class="bg-gray-900 border border-gray-800 rounded-xl p-4 flex-1 overflow-y-auto"
      >
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-4">
          Caractéristiques
        </p>
        <div class="space-y-2">
          <div
            v-for="stat in currentPlayer.stats"
            :key="stat.short"
            class="flex items-center justify-between py-2 px-3 bg-gray-800/30 rounded-lg border border-gray-800/50"
          >
            <div class="flex flex-col">
              <span class="text-[10px] font-bold text-gray-500 uppercase leading-none">{{
                stat.short
              }}</span>
              <span class="text-sm font-medium text-gray-200">{{ stat.name }}</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-xs text-gray-500 font-mono">{{
                calculateModifier(stat.value)
              }}</span>
              <span class="text-xl font-bold text-white w-8 text-right">{{ stat.value }}</span>
            </div>
          </div>
        </div>
      </div>
      <div id="inventaire" class="bg-gray-900 border border-gray-800 rounded-xl flex-1"></div>
    </div>

    <div class="column">
      <div class="space-y-2">
        <div class="text-center mb-1">
          <h1 class="text-xl font-black text-white uppercase tracking-tighter leading-none">
            {{ currentPlayer.name }}
          </h1>
        </div>
        <StatBar label="NIVEAU" v-model="currentPlayer.level" :max="20" color="yellow" />
        <div class="flex flex-col gap-2">
          <StatBar
            label="VIE"
            v-model="currentPlayer.health"
            :max="currentPlayer.max_health"
            color="green"
          />
          <StatBar
            label="MANA"
            v-model="currentPlayer.mana"
            :max="currentPlayer.max_mana"
            color="blue"
          />
        </div>
      </div>

      <div
        class="box-grow bg-gray-900 border border-gray-800 rounded-xl flex flex-col items-center justify-center relative overflow-hidden"
      >
        <div v-if="isRolling" class="text-4xl animate-bounce">🎲</div>
        <div v-else-if="diceResult !== null" class="flex flex-col items-center">
          <span class="text-gray-500 text-[10px] uppercase font-bold"
            >Résultat D{{ diceType }}</span
          >
          <span class="text-7xl font-black text-white">{{ diceResult }}</span>
        </div>
        <span v-else class="text-gray-700 font-bold uppercase text-xs tracking-widest"
          >En attente du jet</span
        >
      </div>

      <button
        @click="rollDice"
        :disabled="isRolling"
        class="h-14 bg-blue-700 hover:bg-blue-600 text-white font-bold uppercase rounded-xl transition-all active:scale-95 disabled:opacity-50"
      >
        {{ isRolling ? 'Lancement...' : 'Lancer le dé' }}
      </button>

      <div
        class="bg-gray-900 border border-gray-800 rounded-xl p-3 flex items-center justify-between"
      >
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Type de dé</span>
        <select
          v-model="diceType"
          class="bg-gray-800 text-white rounded px-2 py-1 outline-none border border-gray-700 text-sm"
        >
          <option :value="4">D4</option>
          <option :value="6">D6</option>
          <option :value="8">D8</option>
          <option :value="10">D10</option>
          <option :value="12">D12</option>
          <option :value="20">D20</option>
          <option :value="100">D100</option>
        </select>
      </div>
    </div>

    <div class="column">
      <div class="h-12 bg-gray-900 border border-gray-800 rounded-xl flex items-center px-4">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Table de jeu</span>
      </div>

      <div class="box-grow bg-gray-900 border border-gray-800 rounded-xl p-4 overflow-y-auto">
        <div class="flex justify-between items-center mb-4 border-b border-gray-800 pb-2">
          <p class="text-xs font-medium text-gray-400 uppercase tracking-widest">Compétences</p>
          <div class="flex gap-4 text-[9px] text-gray-500 font-bold uppercase">
            <span class="w-8 text-center">Mana</span>
            <span class="w-8 text-center">Soin</span>
            <span class="w-8 text-center">Dgt</span>
            <span class="w-8 text-right">Dé</span>
          </div>
        </div>

        <div class="space-y-1">
          <div
            v-for="skill in skills"
            :key="skill.skill_id"
            @click="selectSkill(skill)"
            :class="{
              'bg-blue-900/20 border-blue-500/50': selectedSkill?.skill_id === skill.skill_id,
            }"
            class="flex items-center gap-3 py-2 px-2 hover:bg-gray-800/50 rounded-lg cursor-pointer transition-all border border-transparent"
          >
            <span class="text-sm font-medium text-gray-200 flex-1 truncate">{{ skill.label }}</span>
            <div class="flex gap-4 items-center">
              <span class="w-8 text-center text-blue-400 text-sm">{{ skill.mana_cost }}</span>
              <span class="w-8 text-center text-green-400 text-sm">{{ skill.healing || '-' }}</span>
              <span class="w-8 text-center text-red-400 text-sm">{{ skill.damage || '-' }}</span>
              <span class="w-8 text-right text-gray-400 text-xs italic"
                >D{{ skill.dice_cost }}</span
              >
            </div>
          </div>
        </div>
      </div>

      <div class="h-32 bg-gray-900 border border-gray-800 rounded-xl p-4 overflow-y-auto">
        <p class="text-[10px] font-bold text-gray-500 uppercase mb-2 tracking-widest">
          Description
        </p>
        <p v-if="selectedSkill" class="text-sm text-gray-300 leading-relaxed italic">
          {{ selectedSkill.description || 'Aucune description' }}
        </p>
        <p v-else class="text-sm text-gray-700 italic">Cliquez sur une compétence...</p>
      </div>
    </div>
  </div>

  <div v-else class="flex justify-center items-center h-screen bg-gray-900 text-white">
    <p>Chargement des données du joueur...</p>
  </div>
</template>

<style scoped>
.layout-container {
  display: flex;
  flex-direction: row;
  gap: 15px;
  height: 95vh;
  padding: 15px;
  background-color: #050505;
  color: white;
}
.column {
  display: flex;
  flex-direction: column;
  flex: 1;
  gap: 15px;
}
.box-grow {
  flex: 1;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
