<template>
  <div class="pc-card">
    <div class="pc-header">
      <div class="pc-av" :style="{ background: avBg, color: avText }">
        {{ initials(player.name) }}
      </div>
      <div class="pc-header-info">
        <p class="pc-name">{{ player.name }}</p>
        <p class="pc-class">{{ player.class ?? '—' }}</p>
      </div>
      <button class="pc-restore-btn" :disabled="restoring" @click="restore">
        {{ restoring ? '…' : 'Repos' }}
      </button>
    </div>

    <div class="pc-body">
      <StatControl
        label="❤️ PV"
        :value="player.health"
        :max="player.max_health"
        bar-color="#b83030"
        :loading="loadingMap.health"
        :error="errorMap.health"
        @change="(v) => updateStat('health', v)"
      />
      <StatControl
        label="💧 Mana"
        :value="player.mana"
        :max="player.max_mana"
        bar-color="#3870b8"
        :loading="loadingMap.mana"
        :error="errorMap.mana"
        @change="(v) => updateStat('mana', v)"
      />
      <StatControl
        label="🛡️ Armure"
        :value="player.armor"
        :max="player.max_armor"
        bar-color="#9a6422"
        :loading="loadingMap.armor"
        :error="errorMap.armor"
        @change="(v) => updateStat('armor', v)"
      />

      <!-- <hr class="pc-div" /> -->

      <!-- Caractéristiques -->
      <!-- <p class="pc-sk-title">Caractéristiques</p>
      <div v-if="statsLoading" class="pc-sk-empty">Chargement…</div>
      <div v-else-if="statsError" class="pc-sk-error">⚠️ {{ statsError }}</div>
      <div v-else class="pc-stats-grid">
        <div v-for="s in charStats" :key="s.short" class="pc-stat-cell">
          <div class="pc-stat-left">
            <span class="pc-stat-short">{{ s.short }}</span>
            <span class="pc-stat-name">{{ s.name }}</span>
          </div>
          <span class="pc-stat-mod">{{ modifier(s.value) }}</span>
          <span class="pc-stat-val">{{ s.value }}</span>
        </div>
      </div> -->
    </div>

    <hr class="pc-div" />

    <!-- Compétences -->
    <p class="pc-sk-title">Compétences</p>
    <div v-if="skillsLoading" class="pc-sk-empty">Chargement…</div>
    <div v-else-if="skillsError" class="pc-sk-error">⚠️ {{ skillsError }}</div>
    <div v-else-if="!skills.length" class="pc-sk-empty">Aucune compétence</div>
    <div v-else>
      <div v-for="s in skills" :key="s.skill_id" class="pc-sk-row">
        <span class="pc-sk-name">{{ s.label }}</span>
        <span class="badge b-mp">MP{{ s.mana_cost }}</span>
        <span v-if="s.damage != null" class="badge b-dmg">⚔{{ s.damage }}</span>
        <span v-if="s.healing != null" class="badge b-heal">+{{ s.healing }}</span>
        <span class="badge b-dice">D×{{ s.dice_cost }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/utils/api'
import StatControl from '@/components/StatControl.vue'

const props = defineProps({ player: { type: Object, required: true } })
const emit = defineEmits(['updated'])

const palette = [
  { bg: '#4a3410', text: '#f0c860' },
  { bg: '#102038', text: '#78b8f0' },
  { bg: '#2a1038', text: '#b878e8' },
  { bg: '#0e2e18', text: '#70d898' },
]
const idx = (props.player.character_id ?? 0) % palette.length
const avBg = palette[idx].bg
const avText = palette[idx].text

function initials(name) {
  if (!name) return '?'
  return name
    .split(' ')
    .map((w) => w[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
}

function modifier(val) {
  const m = Math.floor((val - 10) / 2)
  return (m >= 0 ? '+' : '') + m
}

// ── Caractéristiques (GET /api/character/:id/stats) ──────────────────────────
const charStats = ref([])
const statsLoading = ref(false)
const statsError = ref(null)

const fallbackStats = [
  { short: 'FOR', name: 'Force', value: 10 },
  { short: 'DEX', name: 'Dextérité', value: 10 },
  { short: 'CON', name: 'Constitution', value: 10 },
  { short: 'INT', name: 'Intelligence', value: 10 },
  { short: 'SAG', name: 'Sagesse', value: 10 },
  { short: 'CHA', name: 'Charisme', value: 10 },
]

async function fetchCharStats() {
  statsLoading.value = true
  statsError.value = null
  try {
    const { data } = await api.get(`/api/character/${props.player.character_id}/stats`)

    // Vérifie que c'est bien un tableau d'objets avec {short, value}
    if (
      Array.isArray(data) &&
      data.length > 0 &&
      typeof data[0] === 'object' &&
      'short' in data[0] &&
      'value' in data[0]
    ) {
      // Ajoute le nom complet si absent
      const nameMap = {
        FOR: 'Force',
        DEX: 'Dextérité',
        CON: 'Constitution',
        INT: 'Intelligence',
        SAG: 'Sagesse',
        CHA: 'Charisme',
      }
      charStats.value = data.map((s) => ({ ...s, name: s.name ?? nameMap[s.short] ?? s.short }))
    } else {
      // Format non reconnu → fallback
      charStats.value = fallbackStats
    }
  } catch (e) {
    // Endpoint absent ou erreur → fallback silencieux
    // charStats.value = fallbackStats
    console.error(e.response?.data)
  } finally {
    statsLoading.value = false
  }
}

// ── Compétences (GET /api/skill/list) ────────────────────────────────────────
const skills = ref([])
const skillsLoading = ref(false)
const skillsError = ref(null)

async function fetchSkills() {
  skillsLoading.value = true
  skillsError.value = null
  try {
    const { data } = await api.get('/api/skill/list')
    skills.value = Array.isArray(data) ? data : []
  } catch (e) {
    skillsError.value = e.response?.data?.message ?? 'Erreur de chargement'
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

// ── PV / Mana / Armure ───────────────────────────────────────────────────────
const loadingMap = reactive({ health: false, mana: false, armor: false })
const errorMap = reactive({ health: null, mana: null, armor: null })
const restoring = ref(false)

const endpointMap = {
  health: '/character/health/increase',
  mana: '/character/mana/increase',
  armor: '/character/armor/increase',
}

async function updateStat(stat, delta) {
  loadingMap[stat] = true
  errorMap[stat] = null
  try {
    const { data } = await api.patch(endpointMap[stat], {
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

async function restore() {
  restoring.value = true
  errorMap.health = null
  errorMap.mana = null
  try {
    const missingHp = props.player.max_health - props.player.health
    const missingMana = props.player.max_mana - props.player.mana
    await Promise.all([
      missingHp > 0
        ? api.patch('/character/health/increase', {
            character_id: props.player.character_id,
            value: missingHp,
          })
        : Promise.resolve(),
      missingMana > 0
        ? api.patch('/character/mana/increase', {
            character_id: props.player.character_id,
            value: missingMana,
          })
        : Promise.resolve(),
    ])
    emit('updated', {
      character_id: props.player.character_id,
      stat: 'health',
      newValue: props.player.max_health,
    })
    emit('updated', {
      character_id: props.player.character_id,
      stat: 'mana',
      newValue: props.player.max_mana,
    })
  } catch (e) {
    errorMap.health = e.response?.data?.message ?? 'Erreur lors de la restauration'
  } finally {
    restoring.value = false
  }
}

onMounted(() => {
  // fetchCharStats()
  fetchSkills()
})
</script>

<style scoped>
.pc-card {
  background: #232c1e;
  border: 1px solid #2e3a28;
  border-radius: 10px;
  overflow: hidden;
}
.pc-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border-bottom: 1px solid #2e3a28;
  background: #1a2216;
}
.pc-header-info {
  flex: 1;
  min-width: 0;
}
.pc-av {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
}
.pc-name {
  font-size: 14px;
  font-weight: 600;
  color: #d4a855;
}
.pc-class {
  font-size: 11px;
  color: #7a8a6a;
  margin-top: 1px;
}
.pc-restore-btn {
  padding: 4px 10px;
  border-radius: 6px;
  border: 1px solid #9a6422;
  background: #2e2208;
  color: #d4a855;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  flex-shrink: 0;
  transition: background 0.15s;
}
.pc-restore-btn:hover:not(:disabled) {
  background: #3e3010;
}
.pc-restore-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.pc-body {
  padding: 12px 14px;
}
.pc-div {
  border: none;
  border-top: 1px solid #2e3a28;
  margin: 10px 0;
}

/* Liste caractéristiques */
.pc-stats-grid {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 4px;
}
.pc-stat-cell {
  background: #1a2216;
  border: 1px solid #2e3a28;
  border-radius: 6px;
  padding: 6px 10px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pc-stat-left {
  flex: 1;
}
.pc-stat-short {
  display: block;
  font-size: 9px;
  color: #7a8a6a;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}
.pc-stat-name {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #d4a855;
}
.pc-stat-mod {
  font-size: 11px;
  color: #9a7840;
  min-width: 24px;
  text-align: right;
}
.pc-stat-val {
  font-size: 18px;
  font-weight: 700;
  color: #e8c870;
  min-width: 24px;
  text-align: right;
}

.pc-sk-title {
  font-size: 10px;
  color: #7a6030;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 6px;
}
.pc-sk-empty {
  font-size: 11px;
  color: #4a5a42;
  font-style: italic;
}
.pc-sk-error {
  font-size: 11px;
  color: #e05848;
}
.pc-sk-row {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-bottom: 5px;
  font-size: 11px;
}
.pc-sk-name {
  flex: 1;
  color: #a09a70;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
}
.badge {
  padding: 2px 6px;
  border-radius: 3px;
  font-size: 10px;
  font-weight: 500;
}
.b-mp {
  background: #0e1e38;
  color: #78b8f0;
}
.b-dmg {
  background: #380e0e;
  color: #f07878;
}
.b-heal {
  background: #0e2e10;
  color: #78d878;
}
.b-dice {
  background: #2e2208;
  color: #c8a038;
}
</style>
