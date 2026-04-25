import { ref } from 'vue'
import axios from 'axios'

export function usePlayers() {
  const players = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchPlayers(partyId) {
    loading.value = true
    error.value = null
    try {
      const { data } = await axios.get('/api/party/players', {
        params: { id: partyId ?? 1 },
      })
      players.value = Array.isArray(data) && data.length > 0 ? data : getFallbackPlayers()
    } catch (e) {
      error.value = e.response?.data?.message ?? 'Erreur de chargement'
      players.value = getFallbackPlayers()
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

function getFallbackPlayers() {
  return [
    {
      character_id: 1,
      name: 'Arwen',
      class: 'Elfe Ranger',
      health: 28,
      max_health: 34,
      mana: 12,
      max_mana: 20,
      armor: 3,
      max_armor: 5,
      skills: [
        { name: 'Agilité', value: 18, proficient: true },
        { name: 'Perception', value: 16, proficient: true },
        { name: 'Furtivité', value: 14, proficient: true },
        { name: 'Survie', value: 12, proficient: true },
        { name: 'Force', value: 9, proficient: false },
      ],
    },
    {
      character_id: 2,
      name: 'Thordan',
      class: 'Nain Guerrier',
      health: 50,
      max_health: 58,
      mana: 5,
      max_mana: 10,
      armor: 8,
      max_armor: 10,
      skills: [
        { name: 'Force', value: 19, proficient: true },
        { name: 'Constitution', value: 17, proficient: true },
        { name: 'Intimidation', value: 13, proficient: true },
        { name: 'Athlétisme', value: 11, proficient: false },
        { name: 'Agilité', value: 8, proficient: false },
      ],
    },
    {
      character_id: 3,
      name: 'Mira',
      class: 'Humaine Mage',
      health: 20,
      max_health: 26,
      mana: 40,
      max_mana: 50,
      armor: 1,
      max_armor: 3,
      skills: [
        { name: 'Arcanes', value: 20, proficient: true },
        { name: 'Intelligence', value: 18, proficient: true },
        { name: 'Investigation', value: 15, proficient: true },
        { name: 'Histoire', value: 13, proficient: true },
        { name: 'Force', value: 6, proficient: false },
      ],
    },
    {
      character_id: 4,
      name: 'Kael',
      class: 'Semi-Orc Paladin',
      health: 38,
      max_health: 45,
      mana: 18,
      max_mana: 25,
      armor: 6,
      max_armor: 8,
      skills: [
        { name: 'Charisme', value: 16, proficient: true },
        { name: 'Constitution', value: 15, proficient: true },
        { name: 'Religion', value: 14, proficient: true },
        { name: 'Athlétisme', value: 12, proficient: false },
        { name: 'Furtivité', value: 7, proficient: false },
      ],
    },
  ]
}
