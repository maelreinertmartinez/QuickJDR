<template>
  <div class="sc-wrap">
    <div class="sc-row">
      <span class="sc-label">{{ label }}</span>
      <div class="sc-bar-bg">
        <div class="sc-bar" :style="{ width: barPct + '%', background: barColor }" />
      </div>
      <span class="sc-val">{{ value }}/{{ max }}</span>
      <div class="sc-ctrls">
        <button class="sc-btn" :disabled="loading" @click="emit('change', -step)">−</button>
        <input
          v-model.number="inputVal"
          type="number"
          class="sc-input"
          placeholder="±"
          @keydown.enter="applyInput"
        />
        <button class="sc-btn" :disabled="loading" @click="emit('change', +step)">+</button>
      </div>
    </div>
    <p v-if="error" class="sc-error">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  label: { type: String, required: true },
  value: { type: Number, default: 0 },
  max: { type: Number, default: 1 },
  barColor: { type: String, default: '#9a6422' },
  loading: { type: Boolean, default: false },
  error: { type: String, default: null },
  step: { type: Number, default: 1 },
})

const emit = defineEmits(['change'])
const inputVal = ref(null)

const barPct = computed(() =>
  Math.min(100, Math.max(0, Math.round((props.value / props.max) * 100))),
)

function applyInput() {
  if (inputVal.value === null || isNaN(inputVal.value)) return
  emit('change', inputVal.value)
  inputVal.value = null
}
</script>

<style scoped>
.sc-wrap {
  margin-bottom: 8px;
}
.sc-row {
  display: flex;
  align-items: center;
  gap: 8px;
}
.sc-label {
  font-size: 11px;
  color: #7a8a6a;
  width: 58px;
  flex-shrink: 0;
}
.sc-bar-bg {
  flex: 1;
  height: 5px;
  background: #171e13;
  border-radius: 3px;
  overflow: hidden;
}
.sc-bar {
  height: 5px;
  border-radius: 3px;
  transition: width 0.3s;
}
.sc-val {
  font-size: 12px;
  color: #e8c870;
  min-width: 40px;
  text-align: right;
  font-weight: 500;
}
.sc-ctrls {
  display: flex;
  gap: 3px;
  align-items: center;
}
.sc-btn {
  width: 20px;
  height: 20px;
  border-radius: 4px;
  border: 1px solid #3a4a2e;
  background: #171e13;
  color: #c8a860;
  font-size: 13px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 0;
  line-height: 1;
}
.sc-btn:hover:not(:disabled) {
  background: #2e3a28;
}
.sc-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.sc-input {
  width: 36px;
  height: 20px;
  background: #171e13;
  border: 1px solid #3a4a2e;
  border-radius: 3px;
  color: #e8c870;
  font-size: 10px;
  text-align: center;
  padding: 0 2px;
}
.sc-error {
  font-size: 10px;
  color: #e05848;
  margin-top: 3px;
  margin-left: 66px;
}
</style>
