<template>
  <div
    class="bg-[#1e1e1e] border border-gray-800 rounded-2xl overflow-hidden text-gray-200 w-full max-w-2xl font-sans"
  >
    <div class="p-6 flex justify-between items-center">
      <div class="flex items-center gap-4">
        <div
          :class="avatarClass"
          class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-semibold shadow-lg"
        >
          {{ player.initials }}
        </div>
        <div>
          <h2 class="text-2xl font-bold text-white">{{ player.name }}</h2>
          <p class="text-sm text-gray-400">Niveau {{ player.level }} — {{ player.class }}</p>
        </div>
      </div>
      <div class="text-right">
        <div class="text-[#86efac] text-3xl font-bold leading-none">{{ player.hp }} PV</div>
        <div class="text-[10px] text-gray-400 uppercase font-medium mt-1">Points de vie</div>
      </div>
    </div>

    <div class="grid grid-cols-3 border-t border-b border-gray-800">
      <div
        v-for="(stat, index) in player.stats"
        :key="stat.short"
        class="p-4 border-gray-800"
        :class="[index % 3 !== 2 ? 'border-r' : '', index < 3 ? 'border-b' : '']"
      >
        <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">{{ stat.short }}</p>
        <p class="text-2xl font-bold text-white leading-none">{{ stat.value }}</p>
        <p class="text-sm text-gray-400 mt-1">{{ calculateModifier(stat.value) }}</p>
      </div>
    </div>

    <div class="p-6">
      <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-5">
        Compétences
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
        <div v-for="skill in player.skills" :key="skill.name" class="flex items-center gap-3">
          <div
            :class="
              skill.proficient ? 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]' : 'bg-gray-700'
            "
            class="w-2 h-2 rounded-full shrink-0"
          ></div>

          <span class="text-sm font-medium w-24">{{ skill.name }}</span>

          <div class="flex-1 h-[6px] bg-[#2a2a2a] rounded-full overflow-hidden">
            <div
              class="h-full bg-[#6366f1] rounded-full"
              :style="{ width: (skill.value / 15) * 100 + '%' }"
            />
          </div>

          <span class="text-sm font-bold w-6 text-right text-white">+{{ skill.value }}</span>
        </div>
      </div>

      <div class="flex flex-wrap gap-2 mt-8">
        <span
          v-for="trait in player.traits"
          :key="trait"
          class="px-4 py-1.5 bg-[#262626] rounded-full text-xs font-medium text-gray-300 border border-gray-700/50"
        >
          {{ trait }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ player: Object })

// Calcule le modificateur D&D basé sur le score (ex: 14 -> +2)
const calculateModifier = (val) => {
  const mod = Math.floor((val - 10) / 2)
  return mod >= 0 ? `+${mod}` : mod
}

const colorMap = {
  teal: 'bg-[#b4c6fc] text-[#1e1e1e]', // Adaptation pour ressembler à la couleur MI de l'image
  purple: 'bg-[#c7d2fe] text-[#1e1e1e]',
}

const avatarClass = computed(() => colorMap[props.player.color] ?? colorMap.teal)
</script>
