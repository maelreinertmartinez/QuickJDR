<script setup>
import { ref, onMounted } from 'vue'
import api from '@/utils/api'
import StatBar from '@/components/StarBar.vue'
const mockStats = [
  { short: 'FOR', name: 'Force', value: 10 },
  { short: 'DEX', name: 'Dextérité', value: 18 },
  { short: 'CON', name: 'Constitution', value: 14 },
  { short: 'INT', name: 'Intelligence', value: 12 },
  { short: 'SAG', name: 'Sagesse', value: 16 },
  { short: 'CHA', name: 'Charisme', value: 11 },
]

const mockSkill = {
  skill_id: 999,
  label: 'Boule de Feu',
  description: 'Une explosion de flammes dévastatrice qui brûle tout sur son passage.',
  mana_cost: 5,
  dice_cost: 10,
  damage: '2d6',
  healing: null,
}

const currentPlayer = ref({
  name: 'Chargement...',
  health: 74,
  max_health: 100,
  mana: 20,
  max_mana: 1000,
  armor: 2,
  max_armor: 10,
  stats: mockStats,
})
const skills = ref([mockSkill])
const selectedSkill = ref(mockSkill)
const diceResult = ref(null)
const diceType = ref(20)
const isRolling = ref(false)
const healthModifier = ref(0)
const errorMessage = ref(null)

const calculateModifier = (val) => {
  const mod = Math.floor((val - 10) / 2)
  return mod >= 0 ? `+${mod}` : mod
}

const handleApiError = (error) => {
  errorMessage.value = error.response?.data?.error || 'Erreur de communication avec le serveur.'
  setTimeout(() => (errorMessage.value = null), 5000)
}

const fetchData = async () => {
  try {
    const charRes = await api.get('/characters')

    // 1. Extraction des données
    const characterData = Array.isArray(charRes.data) ? charRes.data[0] : charRes.data
    console.log('Données reçues du serveur :', charRes.data)

    // 2. Vérification de l'existence du personnage
    if (!characterData || !characterData.id) {
      console.warn('Aucun personnage trouvé.')
      // window.location.href = '/characters/create'
      return
    }

    // 3. Mise à jour de currentPlayer avec les données SQL
    currentPlayer.value = {
      ...characterData,
      health: characterData.health,
      max_health: characterData.max_health,
      mana: characterData.mana ?? 0,
      max_mana: characterData.max_mana,
      armor: characterData.armor ?? 0,
      max_armor: characterData.max_armor,
      stats: mockStats, // Vos stats par défaut
    }

    // 4. Récupération des compétences
    const skillsRes = await api.get('/skills/list')
    skills.value = Array.isArray(skillsRes.data) ? skillsRes.data : []
    if (skills.value == null) {
      skills.value = [mockSkill]
    }
    /*skills.value = [
      {
        skill_id: 999,
        label: 'Boule de Feu',
        description: 'Une explosion de flammes dévastatrice qui brûle tout sur son passage.',
        mana_cost: 5,
        dice_cost: 10,
        damage: '2d6',
        healing: null,
      },
    ]*/
  } catch (e) {
    console.error('Erreur lors de la récupération des données :', e)
    if (e.response?.status === 404) {
      // window.location.href = '/characters/create'
    } else {
      handleApiError(e)
    }
  }
}

const sleep = async () => {
  try {
    const res = await api.post('/characters/sleep')
    currentPlayer.value.health = res.data.max_health
    currentPlayer.value.mana = res.data.max_mana
  } catch (e) {
    handleApiError(e)
  }
}

const modifyHealth = async (isDamage) => {
  if (!currentPlayer.value || healthModifier.value <= 0) return
  try {
    const damageValue = isDamage ? healthModifier.value : -healthModifier.value
    const res = await api.post('/characters/take-damage', { damage: damageValue })
    currentPlayer.value.health = res.data.new_health
    healthModifier.value = 0
  } catch (e) {
    handleApiError(e)
  }
}

const rollDice = async () => {
  if (!currentPlayer.value) return

  isRolling.value = true
  diceResult.value = null

  try {
    // On récupère l'ID du skill sélectionné s'il existe
    const skillId = selectedSkill.value?.skill_id || null

    const res = await api.post('/dice/roll', {
      max_value: Number(diceType.value),
      skill_id: skillId
    })

    // Mise à jour du résultat visuel
    diceResult.value = res.data.value

    // Mise à jour automatique des ressources si le backend les renvoie
    if (res.data.new_health !== undefined) {
      currentPlayer.value.health = res.data.new_health
    }
    if (res.data.new_mana !== undefined) {
      currentPlayer.value.mana = res.data.new_mana
    }
  } catch (e) {
    handleApiError(e)
  } finally {
    isRolling.value = false
  }
}

onMounted(fetchData)
</script>

<template>
  <div class="layout-container" v-if="currentPlayer">
    <Transition name="fade">
      <div
        v-if="errorMessage"
        class="absolute top-5 right-5 z-50 bg-red-600 text-white px-6 py-3 rounded-lg shadow-2xl font-bold flex items-center gap-3"
      >
        <span>⚠️</span> {{ errorMessage }}
        <button @click="errorMessage = null" class="ml-4 hover:opacity-70">✕</button>
      </div>
    </Transition>

    <div class="column">
      <div class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-4 flex-1 overflow-y-auto">
        <p class="text-xs font-black text-creamy-jdr uppercase tracking-widest mb-4">
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

      <div class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-4 flex flex-col gap-3">
        <div class="flex justify-between items-center">
          <span class="text-lg font-black text-creamy-jdr uppercase">{{ currentPlayer.name }}</span>
          <button
            @click="sleep"
            class="bg-orange-jdr hover:bg-brown-jdr text-creamy-jdr p-2 rounded-lg transition-all active:scale-90"
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
              class="w-16 bg-dark-green-jdr text-creamy-jdr border border-olive-jdr rounded-lg px-2 py-1 text-center outline-none"
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
        <span v-else class="text-olive-jdr font-bold uppercase text-xs tracking-widest">Prêt</span>
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
      <div class="h-12 bg-green-jdr border-2 border-olive-jdr rounded-xl flex items-center px-4">
        <span class="text-xs font-black text-creamy-jdr uppercase tracking-widest">Grimoire</span>
      </div>
      <div
        class="box-grow bg-green-jdr border-2 border-olive-jdr shadow-inner rounded-xl p-4 overflow-y-auto max-h-[450px]"
      >
        <div v-if="skills.length > 0" class="space-y-1">
          <div
            v-for="skill in skills"
            :key="skill.skill_id"
            @click="selectedSkill = skill"
            :class="{
              'bg-dark-green-jdr border-creamy-jdr/50 scale-[1.02]':
                selectedSkill?.skill_id === skill.skill_id,
            }"
            class="flex flex-col gap-1 py-3 px-3 hover:bg-dark-green-jdr/40 rounded-lg cursor-pointer border border-transparent transition-all"
          >
            <span class="text-sm font-bold text-creamy-jdr truncate">{{ skill.label }}</span>
            <div class="flex gap-2">
              <span class="text-blue-400 text-[10px] font-black uppercase"
                >{{ skill.mana_cost }} MP</span
              >
              <span v-if="skill.healing" class="text-green-400 text-[10px] font-black uppercase"
                >{{ skill.healing }} Soin</span
              >
              <span v-if="skill.damage" class="text-red-400 text-[10px] font-black uppercase"
                >{{ skill.damage }} Dgt</span
              >
              <span class="text-creamy-jdr/40 text-[10px] font-black uppercase"
                >Coût D{{ skill.dice_cost }}</span
              >
            </div>
          </div>
        </div>
        <p v-else class="text-xs text-olive-jdr italic text-center mt-10">Grimoire vide...</p>
      </div>
      <div
        class="h-40 bg-green-jdr border-2 border-olive-jdr rounded-xl p-4 overflow-y-auto shadow-md"
      >
        <p class="text-[10px] font-black text-creamy-jdr/60 uppercase mb-2 tracking-widest">
          Description
        </p>
        <p v-if="selectedSkill" class="text-sm text-creamy-jdr leading-relaxed italic">
          "{{ selectedSkill.description }}"
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
</style>
