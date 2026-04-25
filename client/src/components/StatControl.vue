<template>
  <div>
    <div class="flex items-center gap-2">
      <!-- Label + valeur -->
      <span class="text-xs w-20 shrink-0" :class="color">
        {{ label }}
        <span class="font-bold text-sm">{{ value }}</span>
        <span class="text-stone-600" v-if="max != null"> / {{ max }}</span>
      </span>

      <!-- Barre de progression -->
      <div class="flex-1 h-1.5 bg-stone-800 rounded-full overflow-hidden" v-if="max">
        <div class="h-full rounded-full transition-all duration-300"
             :class="barColor"
             :style="{ width: Math.max(0, Math.min(100, (value / max) * 100)) + '%' }" />
      </div>

      <!-- Bouton - -->
      <button
        @click="emit('change', -step)"
        :disabled="loading"
        class="w-6 h-6 rounded-md bg-stone-800 border border-stone-700 text-stone-300 hover:bg-red-900/60 hover:border-red-700 disabled:opacity-40 flex items-center justify-center text-sm font-bold transition-colors"
      >−</button>

      <!-- Input direct -->
      <input
        v-model.number="inputVal"
        type="number"
        class="w-10 h-6 text-center text-xs bg-stone-800 border border-stone-700 rounded-md text-stone-200 focus:outline-none focus:border-amber-600"
        @keydown.enter="applyInput"
        placeholder="±"
      />

      <!-- Bouton + -->
      <button
        @click="emit('change', +step)"
        :disabled="loading"
        class="w-6 h-6 rounded-md bg-stone-800 border border-stone-700 text-stone-300 hover:bg-green-900/60 hover:border-green-700 disabled:opacity-40 flex items-center justify-center text-sm font-bold transition-colors"
      >+</button>

      <!-- Spinner -->
      <span v-if="loading" class="text-xs text-stone-500">…</span>
    </div>

    <!-- Message d'erreur -->
    <p v-if="error" class="text-xs text-red-400 mt-0.5 ml-20">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  label:   { type: String, required: true },
  value:   { type: Number, default: 0 },
  max:     { type: Number, default: null },
  color:   { type: String, default: 'text-stone-300' },
  loading: { type: Boolean, default: false },
  error:   { type: String, default: null },
  step:    { type: Number, default: 1 },
})

const emit = defineEmits(['change'])
const inputVal = ref(null)

const barColor = computed(() => {
  if (props.color.includes('red')) return 'bg-red-600'
  if (props.color.includes('blue')) return 'bg-blue-600'
  if (props.color.includes('amber')) return 'bg-amber-600'
  return 'bg-stone-500'
})

function applyInput() {
  if (inputVal.value === null || isNaN(inputVal.value)) return
  emit('change', inputVal.value)
  inputVal.value = null
}
</script>
