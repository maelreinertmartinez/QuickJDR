import { ref } from 'vue'
import api from '@/utils/api'

export function usePlayers() {
  const players = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchPlayers(partyId) {
    loading.value = true
    error.value = null
    try {
      const { data } = await api.get('/party/players', {
        params: { id: partyId ?? 1 },
      })
      players.value = Array.isArray(data) && data.length > 0 ? data : []
    } catch (e) {
      error.value = e.response?.data?.message ?? 'Erreur de chargement'
    } finally {
      loading.value = false
    }
  }

  function applyUpdate({ character_id, stat, newValue }) {
    const p = players.value.find((x) => x.character_id === character_id)
    if (!p) return
    if (stat === 'health') p.health = newValue
    else if (stat === 'mana') p.mana = newValue
    else if (stat === 'armor') p.armor = newValue
  }

  return { players, loading, error, fetchPlayers, applyUpdate }
}
