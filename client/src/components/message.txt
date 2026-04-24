import { ref } from 'vue'

export function usePlayers() {
  const players = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchPlayers() {
    loading.value = true
    error.value = null
    try {
      const res = await fetch('/api/players')
      if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`)
      const data = await res.json()
      players.value = Array.isArray(data) ? data : []
    } catch (e) {
      error.value = e
      players.value = [
        {
          id: 1,
          name: 'Arwen',
          class: 'Elfe Ranger',
          race: 'Elfe',
          level: 5,
          initials: 'AR',
          color: 'teal',
          hp: 34,
          stats: [
            { short: 'FOR', name: 'Force', value: 10 },
            { short: 'DEX', name: 'Dextérité', value: 18 },
            { short: 'CON', name: 'Constitution', value: 13 },
            { short: 'INT', name: 'Intelligence', value: 12 },
            { short: 'SAG', name: 'Sagesse', value: 16 },
            { short: 'CHA', name: 'Charisme', value: 11 },
          ],
          skills: [
            { name: 'Acrobaties', value: 7, proficient: true },
            { name: 'Discrétion', value: 7, proficient: true },
            { name: 'Perception', value: 6, proficient: true },
            { name: 'Survie', value: 6, proficient: true },
            { name: 'Athlétisme', value: 2, proficient: false },
            { name: 'Persuasion', value: 1, proficient: false },
          ],
          traits: ['Sens aiguisés', 'Transe elfique', 'Esquive'],
        },
        {
          id: 2,
          name: 'Thordan',
          class: 'Nain Guerrier',
          race: 'Nain',
          level: 5,
          initials: 'TH',
          color: 'coral',
          hp: 58,
          stats: [
            { short: 'FOR', name: 'Force', value: 19 },
            { short: 'DEX', name: 'Dextérité', value: 10 },
            { short: 'CON', name: 'Constitution', value: 17 },
            { short: 'INT', name: 'Intelligence', value: 9 },
            { short: 'SAG', name: 'Sagesse', value: 11 },
            { short: 'CHA', name: 'Charisme', value: 8 },
          ],
          skills: [
            { name: 'Athlétisme', value: 7, proficient: true },
            { name: 'Intimidation', value: 4, proficient: true },
            { name: 'Histoire', value: 2, proficient: true },
            { name: 'Perception', value: 2, proficient: false },
            { name: 'Acrobaties', value: 0, proficient: false },
            { name: 'Persuasion', value: -1, proficient: false },
          ],
          traits: ['Résistance naine', 'Second souffle', 'Attaque multiple'],
        },
        {
          id: 3,
          name: 'Mira',
          class: 'Humaine Mage',
          race: 'Humaine',
          level: 5,
          initials: 'MI',
          color: 'purple',
          hp: 26,
          stats: [
            { short: 'FOR', name: 'Force', value: 8 },
            { short: 'DEX', name: 'Dextérité', value: 14 },
            { short: 'CON', name: 'Constitution', value: 12 },
            { short: 'INT', name: 'Intelligence', value: 20 },
            { short: 'SAG', name: 'Sagesse', value: 13 },
            { short: 'CHA', name: 'Charisme', value: 15 },
          ],
          skills: [
            { name: 'Arcanes', value: 8, proficient: true },
            { name: 'Histoire', value: 8, proficient: true },
            { name: 'Investigation', value: 8, proficient: true },
            { name: 'Médecine', value: 4, proficient: true },
            { name: 'Perception', value: 3, proficient: false },
            { name: 'Discrétion', value: 2, proficient: false },
          ],
          traits: ['Récupération arcanique', 'Maîtrise des sorts', 'Mémoire de sort'],
        },
        {
          id: 4,
          name: 'Kael',
          class: 'Semi-Orc Paladin',
          race: 'Semi-Orc',
          level: 5,
          initials: 'KA',
          color: 'pink',
          hp: 45,
          stats: [
            { short: 'FOR', name: 'Force', value: 17 },
            { short: 'DEX', name: 'Dextérité', value: 9 },
            { short: 'CON', name: 'Constitution', value: 15 },
            { short: 'INT', name: 'Intelligence', value: 10 },
            { short: 'SAG', name: 'Sagesse', value: 12 },
            { short: 'CHA', name: 'Charisme', value: 16 },
          ],
          skills: [
            { name: 'Athlétisme', value: 6, proficient: true },
            { name: 'Intimidation', value: 6, proficient: true },
            { name: 'Religion', value: 6, proficient: true },
            { name: 'Persuasion', value: 6, proficient: true },
            { name: 'Perception', value: 1, proficient: false },
            { name: 'Discrétion', value: -1, proficient: false },
          ],
          traits: ['Imposition des mains', 'Châtiment divin', 'Endurance implacable'],
        },
      ]
    } finally {
      loading.value = false
    }
  }

  return { players, loading, error, fetchPlayers }
}
