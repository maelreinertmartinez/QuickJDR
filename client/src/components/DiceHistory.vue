<template>
  <div class="dh-panel">
    <p class="dh-title">Historique</p>
    <div v-if="!history.length" class="dh-empty">Aucun lancer.</div>
    <div v-for="(h, i) in history" :key="i" class="dh-item">
      <span class="dh-label">
        <strong>{{ h.count }}D{{ h.sides }}</strong>
        <span class="dh-time">{{ formatTime(h.time) }}</span>
      </span>
      <div class="dh-right">
        <div class="dh-dice">
          <div
            v-for="(r, j) in h.results"
            :key="j"
            :class="['dh-die', r === h.sides ? 'crit' : r === 1 ? 'fail' : '']"
          >
            {{ r }}
          </div>
        </div>
        <span class="dh-total">={{ h.total }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ history: { type: Array, default: () => [] } })
function formatTime(date) {
  return new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}
</script>

<style scoped>
.dh-panel {
  background: #232c1e;
  border: 1px solid #2e3a28;
  border-radius: 8px;
  padding: 8px 10px;
}
.dh-title {
  font-size: 9px;
  color: #9a7840;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  margin-bottom: 6px;
  font-weight: 500;
}
.dh-empty {
  font-size: 10px;
  color: #4a5a42;
  font-style: italic;
}
.dh-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 3px 0;
  border-bottom: 1px solid #1e2818;
}
.dh-item:last-child {
  border-bottom: none;
}
.dh-label {
  font-size: 10px;
  color: #8a9a72;
  display: flex;
  align-items: center;
  gap: 5px;
}
.dh-label strong {
  color: #c8a860;
}
.dh-time {
  font-size: 9px;
  color: #4a5a42;
}
.dh-right {
  display: flex;
  align-items: center;
  gap: 4px;
}
.dh-dice {
  display: flex;
  gap: 2px;
}
.dh-die {
  width: 18px;
  height: 18px;
  border-radius: 3px;
  background: #171e13;
  border: 1px solid #3a4a2e;
  color: #c8a860;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 9px;
  font-weight: 500;
}
.dh-die.crit {
  background: #1c3010;
  color: #90d850;
}
.dh-die.fail {
  background: #301210;
  color: #e05848;
}
.dh-total {
  font-size: 10px;
  font-weight: 600;
  color: #e8c870;
  margin-left: 2px;
}
</style>
