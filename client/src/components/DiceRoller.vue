<template>
  <div class="dr-panel">
    <p class="dr-title">Lancer des dés</p>
    <div class="dr-controls">
      <div class="dr-group">
        <label>Nombre</label>
        <input v-model.number="count" type="number" min="1" max="20" class="dr-input" />
      </div>
      <div class="dr-group">
        <label>Type</label>
        <select v-model.number="sides" class="dr-select">
          <option v-for="d in diceOptions" :key="d" :value="d">D{{ d }}</option>
        </select>
      </div>
      <button @click="roll" :disabled="rolling" class="dr-btn">
        {{ rolling ? '⏳…' : 'Lancer' }}
      </button>
    </div>
    <p v-if="rollError" class="dr-error">⚠️ {{ rollError }}</p>
    <div class="dr-results">
      <div
        v-for="(r, i) in results"
        :key="i"
        :class="['dr-die', r === sides ? 'crit' : r === 1 ? 'fail' : '']"
      >
        {{ r }}
      </div>
      <div v-if="results.length" class="dr-total">= {{ total }}</div>
      <span v-else class="dr-empty">Prêt à lancer…</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '@/utils/api'

const emit = defineEmits(['rolled'])
const diceOptions = [4, 6, 8, 10, 12, 20, 100]
const count = ref(1)
const sides = ref(20)
const results = ref([])
const rolling = ref(false)
const rollError = ref(null)
const total = computed(() => results.value.reduce((a, b) => a + b, 0))

async function roll() {
  rolling.value = true
  rollError.value = null
  results.value = []
  const n = Math.min(20, Math.max(1, count.value))
  try {
    const rolls = await Promise.all(
      Array.from({ length: n }, async () => {
        return api.post('/dice/blank', { max_value: sides.value }).then((res) => {
          console.log(res)
          return res.data.value
        })
      }),
    )
    results.value = rolls
    emit('rolled', {
      count: n,
      sides: sides.value,
      results: [...rolls],
      total: rolls.reduce((a, b) => a + b, 0),
      time: new Date(),
    })
  } catch (e) {
    rollError.value = e.response?.data?.message ?? 'Impossible de contacter le serveur.'
  } finally {
    rolling.value = false
  }
}
</script>

<style scoped>
.dr-panel {
  background: #232c1e;
  border: 1px solid #2e3a28;
  border-radius: 8px;
  padding: 8px 10px;
}
.dr-title {
  font-size: 9px;
  color: #9a7840;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 6px;
  font-weight: 500;
}
.dr-controls {
  display: flex;
  gap: 6px;
  align-items: flex-end;
  margin-bottom: 8px;
  flex-wrap: wrap;
}
.dr-group {
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.dr-group label {
  font-size: 9px;
  color: #8a9a72;
}
.dr-input {
  width: 46px;
  background: #171e13;
  border: 1px solid #3a4a2e;
  border-radius: 4px;
  padding: 3px 6px;
  color: #e8c870;
  font-size: 12px;
  text-align: center;
}
.dr-select {
  background: #171e13;
  border: 1px solid #3a4a2e;
  border-radius: 4px;
  padding: 3px 8px;
  color: #e8c870;
  font-size: 12px;
}
.dr-btn {
  background: #9a6422;
  border: none;
  border-radius: 5px;
  padding: 5px 14px;
  color: #f5e0a0;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.dr-btn:hover:not(:disabled) {
  background: #b07830;
}
.dr-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.dr-error {
  font-size: 10px;
  color: #e05848;
  margin-bottom: 6px;
}
.dr-results {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  align-items: center;
  min-height: 32px;
}
.dr-die {
  width: 30px;
  height: 30px;
  border-radius: 5px;
  background: #171e13;
  border: 1px solid #3a4a2e;
  color: #e8c870;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 500;
}
.dr-die.crit {
  background: #1c3010;
  border-color: #4a7828;
  color: #90d850;
}
.dr-die.fail {
  background: #301210;
  border-color: #6a2018;
  color: #e05848;
}
.dr-total {
  background: #2e2208;
  border: 1px solid #7a5818;
  border-radius: 4px;
  padding: 3px 8px;
  color: #e8c870;
  font-size: 12px;
  font-weight: 600;
}
.dr-empty {
  font-size: 10px;
  color: #4a5a42;
  font-style: italic;
}
</style>
