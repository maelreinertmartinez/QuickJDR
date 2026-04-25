<template>
  <div class="space-y-2">
    <p v-if="!history.length" class="text-sm text-stone-600 italic">Aucun lancer pour le moment.</p>
    <div
      v-for="(h, i) in history" :key="i"
      class="bg-stone-900 border border-amber-900/30 rounded-xl px-4 py-2.5 flex items-center justify-between"
    >
      <span class="text-sm text-amber-800">
        <strong class="text-amber-200 font-semibold">{{ h.count }}D{{ h.sides }}</strong>
        — {{ formatTime(h.time) }}
      </span>
      <div class="flex gap-1.5 items-center">
        <div
          v-for="(r, j) in h.results" :key="j"
          :class="r === h.sides ? 'bg-green-900 text-green-300' : r === 1 ? 'bg-red-900 text-red-300' : 'bg-stone-800 text-amber-200'"
          class="w-7 h-7 rounded text-xs font-bold flex items-center justify-center"
        >{{ r }}</div>
        <span class="ml-1 text-sm font-semibold text-amber-100">= {{ h.results.reduce((a, b) => a + b, 0) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ history: { type: Array, default: () => [] } })
function formatTime(date) {
  return new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}
</script>
