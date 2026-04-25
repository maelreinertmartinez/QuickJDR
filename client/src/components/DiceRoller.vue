<template>
  <div class="rounded-2xl border border-amber-900/40 bg-stone-900 p-5 shadow-lg">
    <p class="text-xs font-semibold text-amber-700 uppercase tracking-widest mb-4">
      🎲 Lancer des dés
    </p>

    <div class="flex flex-wrap gap-3 items-end mb-5">
      <div class="flex flex-col gap-1">
        <label class="text-xs text-amber-800">Nombre de dés</label>
        <input
          v-model.number="count"
          type="number"
          min="1"
          max="20"
          class="w-16 bg-stone-800 border border-stone-700 rounded-lg px-3 py-1.5 text-sm text-center text-amber-100 focus:outline-none focus:border-amber-600"
        />
      </div>
      <div class="flex flex-col gap-1">
        <label class="text-xs text-amber-800">Type de dé</label>
        <select
          v-model.number="sides"
          class="bg-stone-800 border border-stone-700 rounded-lg px-3 py-1.5 text-sm text-amber-100 focus:outline-none focus:border-amber-600"
        >
          <option v-for="d in diceOptions" :key="d" :value="d">D{{ d }}</option>
        </select>
      </div>
      <button
        @click="roll"
        :disabled="rolling"
        class="bg-amber-700 hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed text-amber-100 rounded-lg px-5 py-2 text-sm font-semibold transition-colors active:scale-95"
      >
        {{ rolling ? '⏳ Lancer…' : '🎲 Lancer' }}
      </button>
    </div>

    <p
      v-if="rollError"
      class="text-red-400 text-sm mb-3 bg-red-950/40 border border-red-900/50 rounded-lg px-3 py-2"
    >
      ⚠️ {{ rollError }}
    </p>

    <div v-if="results.length" class="flex flex-wrap gap-2 items-center">
      <div
        v-for="(r, i) in results"
        :key="i"
        :class="dieClass(r)"
        class="w-10 h-10 rounded-lg border flex items-center justify-center text-base font-bold transition-all"
      >
        {{ r }}
      </div>
      <div class="ml-2 bg-stone-800 border border-stone-700 rounded-lg px-3 py-1">
        <span class="text-xs text-amber-800">Total </span>
        <span class="text-lg font-bold text-amber-100">{{ total }}</span>
      </div>
    </div>
    <p v-else-if="!rolling" class="text-sm text-stone-600">Les résultats apparaîtront ici…</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const emit = defineEmits(['rolled'])

const diceOptions = [4, 6, 8, 10, 12, 20, 100]
const count = ref(1)
const sides = ref(20)
const results = ref([])
const rolling = ref(false)
const rollError = ref(null)

const total = computed(() => results.value.reduce((a, b) => a + b, 0))

function dieClass(r) {
  if (r === sides.value) return 'bg-green-900 border-green-700 text-green-300'
  if (r === 1) return 'bg-red-900 border-red-700 text-red-300'
  return 'bg-stone-800 border-stone-700 text-amber-100'
}

async function rollOne(maxValue) {
  const { data } = await axios.get('/api/dice/blank-roll', {
    params: { max_value: maxValue },
  })
  return data.result
}

async function roll() {
  rolling.value = true
  rollError.value = null
  results.value = []
  const n = Math.min(20, Math.max(1, count.value))
  try {
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
    rollError.value = e.response?.data?.message ?? 'Impossible de contacter le serveur de dés.'
  } finally {
    rolling.value = false
  }
}
</script>
