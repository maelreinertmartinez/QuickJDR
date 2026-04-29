<script setup>
import { ref } from 'vue'
import api from '@/utils/api'

const form = ref({
  name: '',
  health: 20,
  mana: 10,
  armor: 15,
  for: 10,
  dex: 10,
  con: 10,
  int: 10,
  sag: 10,
  cha: 10,
})

const error = ref(null)
const loading = ref(false)

const createCharacter = async () => {
  if (!form.value.name) return (error.value = 'Le nom est requis.')

  loading.value = true
  try {
    await api.post('/character', form.value)
    window.location.href = '/joueur'
  } catch (err) {
    error.value = err.response?.data?.message || 'Erreur de création.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center p-4">
    <div
      class="bg-green-jdr border-2 border-olive-jdr rounded-xl p-6 w-full max-w-lg shadow-2xl text-creamy-jdr"
    >
      <h1 class="text-2xl font-black uppercase text-center mb-6">Créer un personnage</h1>

      <div
        v-if="error"
        class="bg-red-900/50 border border-red-500 text-red-200 p-2 rounded mb-4 text-sm font-bold"
      >
        ⚠️ {{ error }}
      </div>

      <form @submit.prevent="createCharacter" class="space-y-4">
        <div>
          <label class="text-xs font-bold uppercase tracking-widest">Nom</label>
          <input
            v-model="form.name"
            type="text"
            class="w-full bg-dark-green-jdr border border-olive-jdr rounded p-2 outline-none mt-1"
            required
          />
        </div>

        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="text-xs font-bold uppercase tracking-widest text-green-400"
              >Vie (Max)</label
            >
            <input
              v-model.number="form.health"
              type="number"
              class="w-full bg-dark-green-jdr border border-olive-jdr rounded p-2 outline-none mt-1"
            />
          </div>
          <div>
            <label class="text-xs font-bold uppercase tracking-widest text-blue-400"
              >Mana (Max)</label
            >
            <input
              v-model.number="form.mana"
              type="number"
              class="w-full bg-dark-green-jdr border border-olive-jdr rounded p-2 outline-none mt-1"
            />
          </div>
          <div>
            <label class="text-xs font-bold uppercase tracking-widest text-yellow-400"
              >Armure</label
            >
            <input
              v-model.number="form.armor"
              type="number"
              class="w-full bg-dark-green-jdr border border-olive-jdr rounded p-2 outline-none mt-1"
            />
          </div>
        </div>

        <hr class="border-olive-jdr my-4" />

        <div class="grid grid-cols-3 gap-4">
          <div v-for="stat in ['for', 'dex', 'con', 'int', 'sag', 'cha']" :key="stat">
            <label class="text-xs font-bold uppercase tracking-widest">{{ stat }}</label>
            <input
              v-model.number="form[stat]"
              type="number"
              class="w-full bg-dark-green-jdr border border-olive-jdr rounded p-2 outline-none mt-1"
            />
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-orange-jdr hover:bg-brown-jdr p-3 rounded-lg font-bold uppercase mt-6 transition-colors"
        >
          {{ loading ? 'Création...' : 'Valider' }}
        </button>
      </form>
    </div>
  </div>
</template>
