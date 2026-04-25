<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'
import StatBar from '@/components/StarBar.vue'

const apiUrl = 'http://localhost:8000'
const api = axios.create({
  baseURL: apiUrl,
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
const healthModifier = ref(0)

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
      armor: charRes.data.armor || 15,
      max_armor: 20,
    }
    const skillsRes = await api.get('/skill/list')
    skills.value = skillsRes.data
  } catch (error) {
    console.error('Erreur fetchData:', error)
  }
}

const sleep = async () => {
  try {
    const res = await axios.post(
      apiUrl + '/character/sleep',
      {},
      {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      },
    )
    if (res.data) {
      currentPlayer.value.health = res.data.new_health || currentPlayer.value.max_health
      currentPlayer.value.mana = res.data.new_mana || currentPlayer.value.max_mana
    }
  } catch (error) {
    console.error(error)
  }
}

const modifyHealth = async (isDamage) => {
  if (!currentPlayer.value || healthModifier.value <= 0) return
  try {
    const damageValue = isDamage ? healthModifier.value : -healthModifier.value
    const res = await api.post('/character/take-damage', { damage: damageValue })
    if (res.data.new_health !== undefined) currentPlayer.value.health = res.data.new_health
    healthModifier.value = 0
  } catch (error) {
    console.error(error)
  }
}

// Lancement de dé utilisant l'URL concaténée
const rollDice = async () => {
  if (!currentPlayer.value) return
  isRolling.value = true
  diceResult.value = null
  try {
    const payload = {
      max_value: Number(diceType.value),
      skill_id: selectedSkill.value ? selectedSkill.value.skill_id : null,
    }
    const res = await axios.post(apiUrl + '/dice/roll', payload, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    })
    diceResult.value = res.data.value
    if (res.data.new_health !== undefined) currentPlayer.value.health = res.data.new_health
    if (res.data.new_mana !== undefined) currentPlayer.value.mana = res.data.new_mana
  } catch (error) {
    console.error(error)
  } finally {
    isRolling.value = false
  }
}

onMounted(() => fetchData())
</script>

<template>
  <div class="layout-container" v-if="currentPlayer">
    <div class="column">
      <div
        class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-4 flex-1 overflow-y-auto shadow-md"
      >
        <p class="text-xs font-medium text-creamy-jdr uppercase tracking-widest mb-4">
          Caractéristiques
        </p>
        <div class="space-y-2">
          <div
            v-for="stat in currentPlayer.stats"
            :key="stat.short"
            class="flex items-center justify-between py-2 px-3 bg-dark-green-jdr/50 rounded-lg border border-olive-jdr"
          >
            <div class="flex flex-col">
              <span class="text-[10px] font-bold text-creamy-jdr/60 uppercase leading-none">{{
                stat.short
              }}</span>
              <span class="text-sm font-medium text-creamy-jdr">{{ stat.name }}</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-xs text-creamy-jdr/60 font-mono">{{
                calculateModifier(stat.value)
              }}</span>
              <span class="text-xl font-bold text-creamy-jdr w-8 text-right">{{ stat.value }}</span>
            </div>
          </div>
        </div>
      </div>

      <div
        class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-4 shadow-md flex flex-col gap-3"
      >
        <div class="flex justify-between items-center">
          <span class="text-lg font-black text-creamy-jdr uppercase">{{ currentPlayer.name }}</span>
          <button
            @click="sleep"
            class="bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr p-2 rounded-lg transition-colors"
          >
            🛏️
          </button>
        </div>
        <StatBar
          label="ARMURE"
          v-model="currentPlayer.armor"
          :max="currentPlayer.max_armor"
          color="yellow"
        />
      </div>
    </div>

    <div class="column">
      <div class="space-y-2 text-center">
        <h1 class="text-4xl font-black text-creamy-jdr uppercase tracking-tighter">
          {{ currentPlayer.name }}
        </h1>
        <div class="flex flex-col gap-2 p-2 bg-green-jdr border-2 border-olive-jdr rounded-xl">
          <StatBar
            label="MANA"
            v-model="currentPlayer.mana"
            :max="currentPlayer.max_mana"
            color="blue"
            readonly
          />

          <StatBar
            label="VIE"
            v-model="currentPlayer.health"
            :max="currentPlayer.max_health"
            color="green"
            readonly
          />
          <div class="flex items-center justify-end gap-2 mt-1">
            <input
              type="number"
              v-model.number="healthModifier"
              class="w-16 bg-dark-green-jdr text-creamy-jdr border border-olive-jdr rounded-lg px-2 py-1 text-center outline-none"
            />
            <button
              @click="modifyHealth(false)"
              class="bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr w-8 h-8 rounded-lg font-bold transition-colors"
            >
              +
            </button>
            <button
              @click="modifyHealth(true)"
              class="bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr w-8 h-8 rounded-lg font-bold transition-colors"
            >
              -
            </button>
          </div>
        </div>
      </div>

      <div
        class="box-grow bg-green-jdr border-2 border-olive-jdr shadow-md rounded-xl flex flex-col items-center justify-center relative overflow-hidden"
      >
        <div v-if="isRolling" class="text-5xl animate-bounce">🎲</div>
        <div v-else-if="diceResult !== null" class="flex flex-col items-center">
          <span class="text-creamy-jdr/60 text-[10px] uppercase font-bold"
            >Résultat D{{ diceType }}</span
          >
          <span class="text-7xl font-black text-creamy-jdr">{{ diceResult }}</span>
        </div>
        <span v-else class="text-olive-jdr font-bold uppercase text-xs tracking-widest"
          >Prêt à lancer</span
        >
      </div>

      <button
        @click="rollDice"
        :disabled="isRolling"
        class="h-14 bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr font-bold uppercase rounded-xl shadow-md border border-olive-jdr/50 transition-all active:scale-95"
      >
        {{ isRolling ? 'Lancement...' : 'Lancer le dé' }}
      </button>

      <div
        class="bg-green-jdr border-2 border-olive-jdr shadow-md rounded-xl p-3 flex items-center justify-between"
      >
        <span class="text-xs font-bold text-creamy-jdr uppercase tracking-widest">Type de dé</span>
        <select
          v-model="diceType"
          class="bg-dark-green-jdr text-creamy-jdr rounded-lg px-2 py-1 border border-olive-jdr outline-none"
        >
          <option v-for="d in [4, 6, 8, 10, 12, 20, 100]" :key="d" :value="d">D{{ d }}</option>
        </select>
      </div>
    </div>

    <div class="column">
      <div
        class="h-12 bg-green-jdr border-2 border-olive-jdr shadow-md rounded-xl flex items-center px-4"
      >
        <span class="text-xs font-bold text-creamy-jdr uppercase tracking-widest">Grimoire</span>
      </div>
      <div
        class="box-grow bg-green-jdr border-2 border-olive-jdr shadow-inner rounded-xl p-4 overflow-y-auto"
      >
        <div v-if="skills.length > 0" class="space-y-1">
          <div
            v-for="skill in skills"
            :key="skill.skill_id"
            @click="selectedSkill = skill"
            :class="{
              'bg-dark-green-jdr border-creamy-jdr/50': selectedSkill?.skill_id === skill.skill_id,
            }"
            class="flex items-center gap-3 py-2 px-2 hover:bg-dark-green-jdr/60 rounded-lg cursor-pointer border border-transparent transition-all"
          >
            <span class="text-sm font-medium text-creamy-jdr flex-1 truncate">{{
              skill.label
            }}</span>
            <span class="text-blue-400 text-xs font-bold">{{ skill.mana_cost }} MP</span>
            <span class="text-red-400 text-xs font-bold">{{ skill.dice_cost }} Cout </span>
            <span class="text-yellow-400 text-xs font-bold">{{ skill.healing }} healing </span>
            <span class="text-red-400 text-xs font-bold">{{ skill.damage }} damage </span>
          </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center h-full opacity-40">

          <p class="text-xs text-creamy-jdr font-bold uppercase tracking-widest text-center">

          </p>
        </div>
      </div>
      <div
        class="h-32 bg-green-jdr border-2 border-olive-jdr shadow-md rounded-xl p-4 overflow-y-auto"
      >
        <p class="text-[10px] font-bold text-creamy-jdr/60 uppercase mb-2 tracking-widest">
          Description
        </p>
        <p v-if="selectedSkill" class="text-sm text-creamy-jdr leading-relaxed italic">
          {{ selectedSkill.description }}
        </p>
        <p v-else class="text-xs text-olive-jdr italic">Sélectionnez une compétence...</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.layout-container {
  display: flex;
  flex-direction: row;
  gap: 15px;
  height: 95vh;
  padding: 15px;
  background-color: var(--color-dark-green-jdr);
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
<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import StatBar from '@/components/StarBar.vue'

const apiUrl = 'http://localhost:8000'
const api = axios.create({
  baseURL: apiUrl,
  headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
})

const currentPlayer = ref(null)
const skills = ref([])
const selectedSkill = ref(null)
const diceResult = ref(null)
const diceType = ref(20)
const isRolling = ref(false)
const healthModifier = ref(0)

// Gestion des erreurs
const errorMessage = ref(null)
const showError = (msg) => {
  errorMessage.value = msg
  setTimeout(() => (errorMessage.value = null), 5000)
}

const handleApiError = (error) => {
  const msg = error.response?.data?.error || 'Une erreur inattendue est survenue.'
  showError(msg)
}

const calculateModifier = (val) => {
  const mod = Math.floor((val - 10) / 2)
  return mod >= 0 ? `+${mod}` : mod
}

const fetchData = async () => {
  try {
    const charRes = await api.get('/character')
    currentPlayer.value = { ...charRes.data, stats: mockStats, max_armor: 20 }
    const skillsRes = await api.get('/skill/list')
    skills.value = skillsRes.data
  } catch (e) {
    handleApiError(e)
  }
}

const sleep = async () => {
  try {
    const res = await api.post('/character/sleep')
    currentPlayer.value.health = res.data.new_health
    currentPlayer.value.mana = res.data.new_mana
  } catch (e) {
    handleApiError(e)
  }
}

const modifyHealth = async (isDamage) => {
  if (healthModifier.value <= 0) return
  try {
    const res = await api.post('/character/take-damage', {
      damage: isDamage ? healthModifier.value : -healthModifier.value,
    })
    currentPlayer.value.health = res.data.new_health
    healthModifier.value = 0
  } catch (e) {
    handleApiError(e)
  }
}

const rollDice = async () => {
  isRolling.value = true
  diceResult.value = null
  try {
    const res = await api.post('/dice/roll', {
      max_value: Number(diceType.value),
      skill_id: selectedSkill.value?.skill_id || null,
    })
    diceResult.value = res.data.value
    if (res.data.new_health !== undefined) currentPlayer.value.health = res.data.new_health
    if (res.data.new_mana !== undefined) currentPlayer.value.mana = res.data.new_mana
  } catch (e) {
    handleApiError(e)
  } finally {
    isRolling.value = false
  }
}

const mockStats = [
  { short: 'FOR', name: 'Force', value: 10 },
  { short: 'DEX', name: 'Dextérité', value: 18 },
  { short: 'CON', name: 'Constitution', value: 14 },
  { short: 'INT', name: 'Intelligence', value: 12 },
  { short: 'SAG', name: 'Sagesse', value: 16 },
  { short: 'CHA', name: 'Charisme', value: 11 },
]

onMounted(fetchData)
</script>

<template>
  <div class="layout-container" v-if="currentPlayer">
    <Transition name="fade">
      <div
        v-if="errorMessage"
        class="absolute top-5 right-5 z-50 bg-red-600 border-2 border-white text-white px-6 py-3 rounded-lg shadow-2xl font-bold flex items-center gap-3"
      >
        <span>⚠️</span> {{ errorMessage }}
        <button @click="errorMessage = null" class="ml-4 hover:opacity-70">✕</button>
      </div>
    </Transition>

    <div class="column">
      <div
        class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-4 flex-1 overflow-y-auto shadow-md"
      >
        <p class="text-xs font-medium text-creamy-jdr uppercase tracking-widest mb-4 font-black">
          Caractéristiques
        </p>
        <div class="space-y-2">
          <div
            v-for="stat in currentPlayer.stats"
            :key="stat.short"
            class="flex items-center justify-between py-2 px-3 bg-dark-green-jdr/50 rounded-lg border border-olive-jdr"
          >
            <div class="flex flex-col">
              <span class="text-[10px] font-bold text-creamy-jdr/60 uppercase">{{
                stat.short
              }}</span>
              <span class="text-sm font-medium text-creamy-jdr">{{ stat.name }}</span>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-xs text-creamy-jdr/60 font-mono">{{
                calculateModifier(stat.value)
              }}</span>
              <span class="text-xl font-bold text-creamy-jdr w-8 text-right">{{ stat.value }}</span>
            </div>
          </div>
        </div>
      </div>

      <div
        class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-4 shadow-md flex flex-col gap-3"
      >
        <div class="flex justify-between items-center">
          <span class="text-lg font-black text-creamy-jdr uppercase">{{ currentPlayer.name }}</span>
          <button
            @click="sleep"
            class="bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr p-2 rounded-lg transition-all active:scale-90 shadow-lg"
          >
            <span class="text-xl">🛏️</span>
          </button>
        </div>
        <StatBar
          label="ARMURE"
          v-model="currentPlayer.armor"
          :max="currentPlayer.max_armor"
          color="yellow"
        />
      </div>
    </div>

    <div class="column">
      <div class="space-y-2 text-center">
        <h1 class="text-4xl font-black text-creamy-jdr uppercase tracking-tighter drop-shadow-lg">
          {{ currentPlayer.name }}
        </h1>
        <div
          class="flex flex-col gap-2 p-2 bg-green-jdr border-2 border-olive-jdr rounded-xl shadow-inner"
        >
          <StatBar
            label="MANA"
            v-model="currentPlayer.mana"
            :max="currentPlayer.max_mana"
            color="blue"
            readonly
          />
          <StatBar
            label="VIE"
            v-model="currentPlayer.health"
            :max="currentPlayer.max_health"
            color="green"
            readonly
          />
          <div class="flex items-center justify-end gap-2 mt-1">
            <input
              type="number"
              v-model.number="healthModifier"
              min="0"
              placeholder="0"
              class="w-16 bg-dark-green-jdr text-creamy-jdr border border-olive-jdr rounded-lg px-2 py-1 text-center outline-none focus:border-creamy-jdr"
            />
            <button
              @click="modifyHealth(false)"
              class="bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr w-8 h-8 rounded-lg font-bold shadow-md"
            >
              +
            </button>
            <button
              @click="modifyHealth(true)"
              class="bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr w-8 h-8 rounded-lg font-bold shadow-md"
            >
              -
            </button>
          </div>
        </div>
      </div>

      <div
        class="box-grow bg-green-jdr border-2 border-olive-jdr shadow-2xl rounded-xl flex flex-col items-center justify-center relative overflow-hidden"
      >
        <div v-if="isRolling" class="text-6xl animate-bounce">🎲</div>
        <div v-else-if="diceResult !== null" class="flex flex-col items-center animate-in zoom-in">
          <span class="text-creamy-jdr/60 text-[10px] uppercase font-bold"
            >Résultat D{{ diceType }}</span
          >
          <span class="text-8xl font-black text-creamy-jdr tracking-tighter">{{ diceResult }}</span>
        </div>
        <span
          v-else
          class="text-olive-jdr font-bold uppercase text-xs tracking-widest animate-pulse"
          >Prêt à lancer</span
        >
      </div>

      <button
        @click="rollDice"
        :disabled="isRolling"
        class="h-16 bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr font-black text-xl uppercase rounded-xl shadow-xl border-b-4 border-brown-jdr active:border-b-0 transition-all"
      >
        {{ isRolling ? 'Lancement...' : 'LANCER LE DÉ' }}
      </button>

      <div
        class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-3 flex items-center justify-between shadow-md"
      >
        <span class="text-xs font-bold text-creamy-jdr uppercase">Type de dé</span>
        <select
          v-model="diceType"
          class="bg-dark-green-jdr text-creamy-jdr rounded-lg px-3 py-1 border border-olive-jdr outline-none font-bold"
        >
          <option v-for="d in [4, 6, 8, 10, 12, 20, 100]" :key="d" :value="d">D{{ d }}</option>
        </select>
      </div>
    </div>

    <div class="column">
      <div
        class="h-12 bg-green-jdr border-2 border-olive-jdr rounded-xl flex items-center px-4 shadow-md"
      >
        <span class="text-xs font-black text-creamy-jdr uppercase tracking-widest">Grimoire</span>
      </div>
      <div
        class="box-grow bg-green-jdr border-2 border-olive-jdr shadow-inner rounded-xl p-4 overflow-y-auto"
      >
        <div class="space-y-1">
          <div
            v-for="skill in skills"
            :key="skill.skill_id"
            @click="selectedSkill = skill"
            :class="{
              'bg-dark-green-jdr border-creamy-jdr/50 scale-[1.02]':
                selectedSkill?.skill_id === skill.skill_id,
            }"
            class="flex items-center gap-3 py-3 px-3 hover:bg-dark-green-jdr/40 rounded-lg cursor-pointer border border-transparent transition-all shadow-sm"
          >
            <span class="text-sm font-bold text-creamy-jdr flex-1 truncate">{{ skill.label }}</span>
            <span class="text-blue-400 text-xs font-black">{{ skill.mana_cost }} MP</span>
            <span class="text-red-400 text-xs font-black">{{ skill.dice_cost }} Cout</span>
            <span class="text-yellow-400 text-xs font-black">{{ skill.healing }} Soin</span>
            <span class="text-red-400 text-xs font-black">{{ skill.damage }} Dégat</span>
          </div>
        </div>
      </div>
      <div
        class="h-40 bg-green-jdr border-2 border-olive-jdr shadow-md rounded-xl p-4 overflow-y-auto"
      >
        <p class="text-[10px] font-black text-creamy-jdr/60 uppercase mb-2 tracking-widest">
          Description
        </p>
        <p
          v-if="selectedSkill"
          class="text-sm text-creamy-jdr leading-relaxed italic animate-in slide-in-from-bottom-2"
        >
          "{{ selectedSkill.description }}"
        </p>
        <p v-else class="text-xs text-olive-jdr italic">
          Sélectionnez une compétence dans le grimoire...
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.layout-container {
  display: flex;
  flex-direction: row;
  gap: 15px;
  height: 95vh;
  padding: 15px;
  background-color: var(--color-dark-green-jdr);
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
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
