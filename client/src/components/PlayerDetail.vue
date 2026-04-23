<template>
  <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    <!-- Header -->
    <div class="flex items-center gap-4 p-4 border-b border-gray-800">
      <div
        :class="avatarClass"
        class="w-12 h-12 rounded-full flex items-center justify-center text-base font-medium"
      >
        {{ player.initials }}
      </div>
      <div>
        <p class="text-base font-medium">{{ player.name }}</p>
        <p class="text-sm text-gray-400">
          Niveau {{ player.level }} — {{ player.race }} {{ player.class }}
        </p>
      </div>
      <div class="ml-auto text-right">
        <p class="text-xl font-medium text-green-400">{{ player.hp }} PV</p>
        <p class="text-xs text-gray-500">Points de vie</p>
      </div>
    </div>

    <!-- Caractéristiques -->
    <div class="grid grid-cols-3 divide-x divide-gray-800 border-b border-gray-800">
      <div v-for="stat in player.stats" :key="stat.short" class="p-3">
        <p class="text-xs text-gray-500 mb-0.5">{{ stat.short }}</p>
        <p class="text-lg font-medium">{{ stat.value }}</p>
        <p class="text-xs text-gray-500">{{ modifier(stat.value) }}</p>
      </div>
    </div>

    <!-- Compétences -->
    <div class="p-4">
      <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">Compétences</p>
      <div class="grid grid-cols-2 gap-x-6 gap-y-1">
        <div v-for="skill in player.skills" :key="skill.name" class="flex items-center gap-2 py-1">
          <!-- Point de maîtrise -->
          <div
            :class="skill.proficient ? 'bg-blue-500' : 'bg-gray-700'"
            class="w-2 h-2 rounded-full shrink-0"
          />
          <span class="text-sm text-gray-400 flex-1">{{ skill.name }}</span>
          <div class="flex-1 h-1 bg-gray-800 rounded-full">
            <div
              class="h-1 rounded-full transition-all duration-300"
              :style="{ width: barWidth(skill.value) + '%', background: barColor }"
            />
          </div>
          <span class="text-sm font-medium w-8 text-right">
            {{ skill.value >= 0 ? '+' : '' }}{{ skill.value }}
          </span>
        </div>
      </div>
    </div>

    <!-- Traits -->
    <div class="px-4 pb-4 flex flex-wrap gap-2">
      <span
        v-for="trait in player.traits"
        :key="trait"
        class="text-xs px-3 py-1 rounded-full bg-gray-800 border border-gray-700 text-gray-400"
      >
        {{ trait }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  player: { type: Object, required: true },
  barColor: { type: String, default: '#378ADD' },
})

const colorMap = {
  teal: 'bg-teal-800 text-teal-200',
  coral: 'bg-orange-900 text-orange-300',
  purple: 'bg-purple-900 text-purple-300',
  pink: 'bg-pink-900 text-pink-300',
}

const avatarClass = computed(() => colorMap[props.player.color] ?? colorMap.teal)

const maxSkill = computed(() => Math.max(...props.player.skills.map((s) => s.value), 1))

function modifier(val) {
  const m = Math.floor((val - 10) / 2)
  return (m >= 0 ? '+' : '') + m
}

function barWidth(val) {
  return Math.max(0, (val / (maxSkill.value + 2)) * 100)
}
</script>
