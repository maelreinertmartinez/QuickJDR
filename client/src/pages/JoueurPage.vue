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

// Configuration de l'URL de base et de l'API
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
      level: charRes.data.level || 1,
    }

    const skillsRes = await api.get('/skill/list')
    skills.value = skillsRes.data
  } catch (error) {
    console.error('Erreur fetchData:', error)
  }
}

const selectSkill = (skill) => {
  selectedSkill.value = skill
  if (skill.dice_cost) diceType.value = skill.dice_cost
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

    // Utilisation de apiUrl + path spécifique [cite: 18]
    const res = await axios.post(apiUrl + '/dice/roll', payload, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    })

    diceResult.value = res.data.value
    if (res.data.new_health !== undefined) currentPlayer.value.health = res.data.new_health
    if (res.data.new_mana !== undefined) currentPlayer.value.mana = res.data.new_mana
  } catch (error) {
    console.error('Erreur rollDice:', error)
  } finally {
    isRolling.value = false
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
    console.error('Erreur modifyHealth:', error)
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
        id="inventaire"
        class="bg-green-jdr border-2 border-olive-jdr rounded-xl flex-1 shadow-md"
      ></div>
    </div>

    <div class="column">
      <div class="space-y-2">
        <div class="text-center mb-1">
          <h1 class="text-2xl font-black text-creamy-jdr uppercase tracking-tighter leading-none">
            {{ currentPlayer.name }}
          </h1>
        </div>
        <StatBar label="NIVEAU" v-model="currentPlayer.level" :max="20" color="yellow" />

        <div class="flex flex-col gap-2 p-2 bg-green-jdr border-2 border-olive-jdr rounded-xl">
          <StatBar
            label="VIE"
            v-model="currentPlayer.health"
            :max="currentPlayer.max_health"
            color="green"
          />
          <div class="flex items-center justify-end gap-2 mb-2">
            <input
              type="number"
              v-model.number="healthModifier"
              min="0"
              placeholder="0"
              class="w-16 bg-dark-green-jdr text-creamy-jdr border border-olive-jdr rounded-lg px-2 py-1 text-center outline-none focus:border-creamy-jdr"
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
          <StatBar
            label="MANA"
            v-model="currentPlayer.mana"
            :max="currentPlayer.max_mana"
            color="blue"
          />
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
        class="h-14 bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr font-bold uppercase rounded-xl transition-all active:scale-95 disabled:opacity-50 shadow-md border border-olive-jdr/50"
      >
        {{ isRolling ? 'Lancement...' : 'Lancer le dé' }}
      </button>

      <div
        class="bg-green-jdr border-2 border-olive-jdr shadow-md rounded-xl p-3 flex items-center justify-between"
      >
        <span class="text-xs font-bold text-creamy-jdr uppercase tracking-widest">Type de dé</span>
        <select
          v-model="diceType"
          class="bg-dark-green-jdr text-creamy-jdr rounded-lg px-2 py-1 outline-none border border-olive-jdr text-sm"
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
        class="box-grow bg-green-jdr border-2 border-olive-jdr shadow-md rounded-xl p-4 overflow-y-auto"
      >
        <div class="space-y-1">
          <div
            v-for="skill in skills"
            :key="skill.skill_id"
            @click="selectSkill(skill)"
            :class="{
              'bg-dark-green-jdr border-creamy-jdr/50': selectedSkill?.skill_id === skill.skill_id,
            }"
            class="flex items-center gap-3 py-2 px-2 hover:bg-dark-green-jdr/60 rounded-lg cursor-pointer transition-all border border-transparent"
          >
            <span class="text-sm font-medium text-creamy-jdr flex-1 truncate">{{
              skill.label
            }}</span>
            <div class="flex gap-4 items-center">
              <span class="w-8 text-center text-blue-400 text-sm font-bold">{{
                skill.mana_cost
              }}</span>
              <span class="w-8 text-center text-red-400 text-sm font-bold">{{
                skill.damage || '-'
              }}</span>
              <span class="w-8 text-right text-creamy-jdr/60 text-xs italic"
                >D{{ skill.dice_cost }}</span
              >
            </div>
          </div>
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
