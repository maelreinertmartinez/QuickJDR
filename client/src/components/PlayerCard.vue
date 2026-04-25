<template>
  <div class="rounded-2xl border border-amber-900/40 bg-stone-900 overflow-hidden shadow-lg">
    <!-- Header personnage -->
    <div class="flex items-center gap-3 px-4 pt-4 pb-3 border-b border-amber-900/30">
      <div
        class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
        :style="{ background: avatarBg, color: avatarText }"
      >
        {{ initials(player.name) }}
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-amber-100 truncate">{{ player.name }}</p>
        <p class="text-xs text-amber-700 truncate">{{ player.class ?? '—' }}</p>
      </div>
    </div>

    <!-- Compétences -->
    <div class="px-4 pt-3 pb-2 border-b border-amber-900/20">
      <p class="text-xs font-semibold text-amber-700 uppercase tracking-widest mb-2">Compétences</p>

      <div v-if="skillsLoading" class="text-xs text-stone-500 italic">Chargement…</div>
      <div v-else-if="skillsError" class="text-xs text-red-400">⚠️ {{ skillsError }}</div>
      <div v-else-if="skills.length === 0" class="text-xs text-stone-600 italic">
        Aucune compétence
      </div>

      <!-- Une compétence par ligne -->
      <div v-else class="space-y-1.5">
        <div v-for="skill in skills" :key="skill.skill_id" class="flex items-center gap-2 text-xs">
          <!-- Label -->
          <span class="text-amber-200 font-medium w-24 truncate shrink-0">{{ skill.label }}</span>

          <!-- Mana -->
          <span class="text-blue-400 w-12 shrink-0"> 💧 {{ skill.mana_cost }} </span>

          <!-- Dégâts -->
          <span v-if="skill.damage != null" class="text-red-400 w-14 shrink-0">
            ⚔️ {{ skill.damage }}
          </span>
          <span v-else class="w-14 shrink-0" />

          <!-- Soin -->
          <span v-if="skill.healing != null" class="text-green-400 w-14 shrink-0">
            💚 {{ skill.healing }}
          </span>
          <span v-else class="w-14 shrink-0" />

          <!-- Coût en dés -->
          <span class="text-amber-600 shrink-0"> 🎲 {{ skill.dice_cost }} </span>
        </div>
      </div>
    </div>

    <!-- Stats PV / Mana / Armure -->
    <div class="px-4 py-3 space-y-2">
      <StatControl
        label="❤️ PV"
        :value="player.health"
        :max="player.max_health"
        color="text-red-400"
        :loading="loadingMap.health"
        :error="errorMap.health"
        @change="(val) => updateStat('health', val)"
      />

      <StatControl
        label="💧 Mana"
        :value="player.mana"
        :max="player.max_mana"
        color="text-blue-400"
        :loading="loadingMap.mana"
        :error="errorMap.mana"
        @change="(val) => updateStat('mana', val)"
      />

      <StatControl
        label="🛡️ Armure"
        :value="player.armor"
        :max="player.max_armor"
        color="text-amber-400"
        :loading="loadingMap.armor"
        :error="errorMap.armor"
        @change="(val) => updateStat('armor', val)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import StatControl from '@/components/StatControl.vue'

const props = defineProps({
  player: { type: Object, required: true },
})

const emit = defineEmits(['updated'])

// Avatar
const palette = [
  { bg: '#78350f', text: '#fef3c7' },
  { bg: '#1e3a5f', text: '#bfdbfe' },
  { bg: '#3b1f4e', text: '#e9d5ff' },
  { bg: '#1a3d2b', text: '#bbf7d0' },
]
const avatarBg = palette[(props.player.character_id ?? 0) % palette.length].bg
const avatarText = palette[(props.player.character_id ?? 0) % palette.length].text

function initials(name) {
  if (!name) return '?'
  return name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
}

// Compétences
const skills = ref([])
const skillsLoading = ref(false)
const skillsError = ref(null)

async function fetchSkills() {
  skillsLoading.value = true
  skillsError.value = null
  try {
    const { data } = await axios.get('/api/skill/list')
    skills.value = Array.isArray(data) ? data : []
  } catch (e) {
    skillsError.value = e.response?.data?.message ?? 'Erreur de chargement'
    // Données de secours
    skills.value = [
      {
        skill_id: 1,
        label: 'Attaque de base',
        mana_cost: 2,
        damage: 8,
        healing: null,
        dice_cost: 1,
      },
      { skill_id: 2, label: 'Soin mineur', mana_cost: 4, damage: null, healing: 6, dice_cost: 1 },
      {
        skill_id: 3,
        label: 'Frappe lourde',
        mana_cost: 6,
        damage: 15,
        healing: null,
        dice_cost: 2,
      },
    ]
  } finally {
    skillsLoading.value = false
  }
}

// Stats PV / Mana / Armure
const loadingMap = reactive({ health: false, mana: false, armor: false })
const errorMap = reactive({ health: null, mana: null, armor: null })

const endpointMap = {
  health: '/character/health/increase',
  mana: '/character/mana/increase',
  armor: '/character/armor/increase',
}

async function updateStat(stat, delta) {
  loadingMap[stat] = true
  errorMap[stat] = null
  try {
    const { data } = await axios.patch(endpointMap[stat], {
      character_id: props.player.character_id,
      value: delta,
    })
    emit('updated', {
      character_id: props.player.character_id,
      stat,
      newValue:
        stat === 'health' ? data.new_health : stat === 'mana' ? data.new_mana : data.new_armor,
    })
  } catch (e) {
    errorMap[stat] = e.response?.data?.message ?? 'Erreur serveur'
  } finally {
    loadingMap[stat] = false
  }
}

onMounted(fetchSkills)
</script>
