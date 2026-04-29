<template>
  <div
    class="relative h-12 w-full bg-gray-900 border border-gray-800 rounded-lg overflow-hidden flex items-center transition-all hover:border-gray-600"
  >
    <div
      class="absolute top-0 left-0 h-full transition-all duration-500 ease-out opacity-30"
      :class="barColorClass"
      :style="{ width: percentage + '%' }"
    ></div>

    <div class="relative z-10 flex justify-between w-full px-4 items-center">
      <span class="text-xs font-black text-gray-500 uppercase tracking-tighter">
        {{ label }}
      </span>

      <div class="flex items-center gap-1">
        <input
          type="number"
          :value="modelValue"
          :readonly="readonly"
          @input="!readonly && $emit('update:modelValue', Number($event.target.value))"
          class="bg-transparent text-right font-bold outline-none w-12 text-lg transition-colors"
          :class="[textColorClass, { 'cursor-default': readonly, 'focus:text-white': !readonly }]"
        />
        <span class="text-gray-600 text-sm font-medium">/ {{ max }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: String,
  modelValue: Number,
  max: Number,
  color: { type: String, default: 'green' },
  readonly: { type: Boolean, default: false },
})

defineEmits(['update:modelValue'])

const percentage = computed(() => {
  if (props.max <= 0) return 0
  return Math.min((props.modelValue / props.max) * 100, 100)
})

const barColorClass = computed(() => ({
  'bg-green-500': props.color === 'green',
  'bg-blue-500': props.color === 'blue',
  'bg-yellow-500': props.color === 'yellow',
  'bg-red-500': props.color === 'red',
}))

const textColorClass = computed(() => ({
  'text-green-400': props.color === 'green',
  'text-blue-400': props.color === 'blue',
  'text-yellow-400': props.color === 'yellow',
  'text-red-400': props.color === 'red',
}))
</script>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type='number'] {
  -moz-appearance: textfield;
}
</style>
