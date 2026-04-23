<template>
  <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
    <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-4">Lancer des dés</p>
    <div class="flex flex-wrap gap-3 items-end mb-5">
      <div class="flex flex-col gap-1">
        <label class="text-xs text-gray-400">Nombre de dés</label>
        <input
          v-model.number="count"
          type="number"
          min="1"
          max="20"
          class="w-16 bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-center text-gray-100 focus:outline-none focus:border-blue-500"
        />
      </div>
      <div class="flex flex-col gap-1">
        <label class="text-xs text-gray-400">Type de dé</label>
        <select
          v-model.number="sides"
          class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-1.5 text-sm text-gray-100 focus:outline-none focus:border-blue-500"
        >
          <option v-for="d in diceOptions" :key="d" :value="d">D{{ d }}</option>
        </select>
      </div>
      <button
        @click="roll"
        :disabled="rolling"
        class="bg-blue-700 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg px-5 py-2 text-sm font-medium transition-colors active:scale-95"
      >
        {{ rolling ? '⏳ Lancer…' : '🎲 Lancer' }}
      </button>
    </div>

    <!-- Erreur -->
    <p v-if="rollError" class="text-red-400 text-sm mb-3">{{ rollError }}</p>

    <!-- Résultats -->
    <div v-if="results.length" class="flex flex-wrap gap-2 items-center">
      <div
        v-for="(r, i) in results"
        :key="i"
        :class="dieClass(r)"
        class="w-10 h-10 rounded-lg border flex items-center justify-center text-base font-medium transition-all"
      >
        {{ r }}
      </div>
      <div class="ml-2 bg-gray-800 border border-gray-700 rounded-lg px-3 py-1">
        <span class="text-xs text-gray-400">Total </span>
        <span class="text-lg font-medium">{{ total }}</span>
      </div>
    </div>
    <p v-else-if="!rolling" class="text-sm text-gray-500">Les résultats apparaîtront ici…</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const emit = defineEmits(['rolled'])

const diceOptions = [4, 6, 8, 10, 12, 20, 100]
const count = ref(1)
const sides = ref(20)
const results = ref([])
const rolling = ref(false)
const rollError = ref(null)

const total = computed(() => results.value.reduce((a, b) => a + b, 0))

function dieClass(r) {
  if (r === sides.value) return 'bg-green-900 border-green-600 text-green-300'
  if (r === 1) return 'bg-red-900 border-red-600 text-red-300'
  return 'bg-gray-800 border-gray-700 text-gray-100'
}

async function rollOne(maxValue) {
  const res = await fetch('/api/dice/roll', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ max_value: maxValue }),
  })
  if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`)
  const data = await res.json()
  return data.value
}

async function roll() {
  rolling.value = true
  rollError.value = null
  results.value = []

  const n = Math.min(20, Math.max(1, count.value))

  try {
    // Lance tous les dés en parallèle
    const rolls = await Promise.all(Array.from({ length: n }, () => rollOne(sides.value)))
    results.value = rolls

    emit('rolled', {
      count: n,
      sides: sides.value,
      results: [...rolls],
      total: rolls.reduce((a, b) => a + b, 0),
      time: new Date(),
    })
  } catch (e) {
    rollError.value = 'Impossible de contacter le serveur de dés.'
  } finally {
    rolling.value = false
  }
}
</script>
