<template>
  <div class="space-y-2">
    <p v-if="!history.length" class="text-sm text-gray-500">Aucun lancer pour le moment.</p>
    <div
      v-for="(h, i) in history"
      :key="i"
      class="bg-gray-900 border border-gray-800 rounded-lg px-4 py-2.5 flex items-center justify-between"
    >
      <span class="text-sm text-gray-400">
        <strong class="text-gray-100">{{ h.count }}D{{ h.sides }}</strong>
        — {{ formatTime(h.time) }}
      </span>
      <div class="flex gap-1.5 items-center">
        <div
          v-for="(r, j) in h.results"
          :key="j"
          :class="
            r === h.sides
              ? 'bg-green-900 text-green-300'
              : r === 1
                ? 'bg-red-900 text-red-300'
                : 'bg-gray-800 text-gray-300'
          "
          class="w-7 h-7 rounded text-xs font-medium flex items-center justify-center"
        >
          {{ r }}
        </div>
        <span class="ml-1 text-sm font-medium">= {{ h.results.reduce((a, b) => a + b, 0) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ history: Array })

function formatTime(date) {
  return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}
</script>
